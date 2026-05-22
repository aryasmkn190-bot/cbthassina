<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HasilUjianModel;
use App\Models\SoalModel;
use App\Models\PesertaModel;
use App\Models\SoalOpsiModel;
use App\Models\JawabanModel;
use App\Models\UjianModel;
use App\Models\TopikSoalModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilUjianController extends BaseController
{
    protected $hasilUjianModel;
    protected $soalModel;
    protected $soalOpsiModel;
    protected $jawabanModel;
    protected $pesertaModel;
    protected $validation;
    protected $ujianModel;
    protected $topikSoalModel;
    public function __construct()
    {
        $this->soalModel = new SoalModel();
        $this->pesertaModel = new PesertaModel();
        $this->soalOpsiModel = new SoalOpsiModel();
        $this->ujianModel = new UjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->jawabanModel = new JawabanModel();
        $this->topikSoalModel = new TopikSoalModel();
        $this->validation = \Config\Services::validation();
    }

    public function index($idujian)
    {
        $ujian = $this->ujianModel->find($idujian);

        $data = [
            'title' => 'Data Hasil Ujian',
            'ujianid' => $idujian,
            'setting' => $this->appSetting(),
            'ujian' => $ujian
        ];

        return view('Panel/Hasil/hasil_ujian_view', $data);
    }

    public function getAll($ujianId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Hanya bisa diakses via AJAX.');
        }

        if (!$ujianId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID Ujian tidak ditemukan.'
            ]);
        }

        $hasil = $this->hasilUjianModel->getHasilUjianLengkap($ujianId);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $hasil,
        ]);
    }
    public function resetDevice()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');

            if (!$id) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'ID peserta tidak ditemukan'
                ]);
            }

            try {
                // Update device_id menjadi null
                $this->hasilUjianModel->update($id, ['device_id' => null]);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Device berhasil dibuka kunci'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }
    public function ulangUjian($id)
    {
        if ($this->request->isAJAX()) {
            // Ambil data hasil ujian terlebih dahulu
            $hasil = $this->hasilUjianModel->find($id);

            if (!$hasil) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data hasil ujian tidak ditemukan.'
                ]);
            }

            // Reset status ujian
            $this->hasilUjianModel->resetUjian($id);

            // Hapus jawaban berdasarkan ujian_id dan peserta_id dari hasil ujian
            $this->jawabanModel
                ->where('ujian_id', $hasil['ujian_id'])
                ->where('peserta_id', $hasil['peserta_id'])
                ->delete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Ujian berhasil diulang dan jawaban dihapus.'
            ]);
        }

        return $this->fail('Akses tidak valid.');
    }


    public function selesaikanUjian($id)
    {
        if ($this->request->isAJAX()) {
            $model = new HasilUjianModel();
            $model->tandaiSelesai($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Ujian ditandai sebagai selesai.'
            ]);
        }

        return $this->fail('Akses tidak valid.');
    }
    private function fail($message)
    {
        return $this->response->setJSON([
            'status' => false,
            'message' => $message
        ]);
    }

    public function jawaban($hasilUjianId)
    {
        $hasil = $this->hasilUjianModel->find($hasilUjianId);
        if (!$hasil) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data hasil ujian tidak ditemukan.'
            ]);
        }

        $ujian = $this->ujianModel->find($hasil['ujian_id']);
        if (!$ujian) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data ujian tidak ditemukan.'
            ]);
        }

        $peserta = $this->pesertaModel
            ->select('peserta.id, peserta.nama, peserta.nisn, kelas.nama AS nama_kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->find($hasil['peserta_id']);

        $soalList = $this->soalModel
            ->select('id, soal_no, pertanyaan, jenis_soal,bobot')
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $soalIds = array_column($soalList, 'id');
        $opsiList = [];

        if (!empty($soalIds)) {
            $allOpsi = $this->soalOpsiModel
                ->select('id, soal_id, label, teks, is_true, pasangan, bobot')
                ->whereIn('soal_id', $soalIds)
                ->orderBy('label', 'ASC')
                ->findAll();

            foreach ($soalIds as $soalId) {
                $opsiList[$soalId] = array_values(array_filter($allOpsi, fn($op) => $op['soal_id'] === $soalId));
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'peserta'   => $peserta,
                'jawaban'   => json_decode($hasil['jawaban_json'], true) ?? [],
                'soalList'  => $soalList,
                'opsiList'  => $opsiList,
                'hasil' => $hasil
            ]
        ]);
    }

    public function exportSkoring($ujianId)
    {
        helper(['text']);

        $hasilList = $this->hasilUjianModel->getSkoringByUjianId($ujianId);
        if (!$hasilList) {
            return redirect()->back()->with('error', 'Data hasil ujian tidak ditemukan.');
        }

        $ujian = $this->ujianModel->getWithNamaMapel($ujianId);

        // === Ambil Soal & Topik ===
        $soalList = $this->soalModel
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $topikIds = array_unique(array_column($soalList, 'topik_soal_id'));
        $topikList = $this->topikSoalModel->whereIn('id', $topikIds)->findAll();
        $topikMap = array_column($topikList, null, 'id');

        // Mapping soal berdasarkan topik → mempercepat analisis
        $soalByTopik = [];
        foreach ($soalList as $s) {
            $soalByTopik[$s['topik_soal_id']][] = $s;
        }

        $spreadsheet = new Spreadsheet();

        // =========================
        // === SHEET 1: SKORING ===
        // =========================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Skoring');

        // Judul besar
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'Hasil ' . $ujian['nama_ujian'] . ' - ' . $ujian['nama_mapel']);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = [
            'No',
            'NISN',
            'Nama',
            'Kelas',
            'Nilai PG',
            'Nilai Esai',
            'Nilai Total',
            'Poin Benar',
            'Poin Salah',
            'Poin Maksimal',
            'Lama Ujian',
            'Waktu Mulai',
            'Waktu Selesai'
        ];
        foreach ($header as $i => $h) {
            $sheet->setCellValue($this->colIndexToLetter($i) . '3', $h);
        }

        // Header soal
        $startColIndex = count($header);
        foreach ($soalList as $i => $soal) {
            $col = $this->colIndexToLetter($startColIndex + $i);
            $sheet->setCellValue("{$col}3", "Soal-" . ($i + 1));
        }

        // Styling header
        $lastCol = $this->colIndexToLetter($startColIndex + count($soalList) - 1);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheet->freezePane('E4');

        // Data
        $row = 4;
        $no = 1;
        foreach ($hasilList as $hasil) {
            $lama = strtotime($hasil['waktu_selesai']) - strtotime($hasil['waktu_mulai']);
            $lamaFormat = gmdate("H:i:s", max(0, $lama));

            $jawaban = json_decode($hasil['jawaban_json'], true) ?? [];

            $data = [
                $no++,
                $hasil['nisn'],
                $hasil['nama_peserta'],
                $hasil['nama_kelas'],
                $hasil['nilai_pg'] ?? 0,
                $hasil['nilai_esai'] ?? 0,
                $hasil['nilai_total'],
                $hasil['poin_benar'],
                $hasil['poin_salah'],
                $hasil['poin_maksimal'],
                $lamaFormat,
                $hasil['waktu_mulai'],
                $hasil['waktu_selesai'],
            ];

            // Data utama
            foreach ($data as $i => $val) {
                $sheet->setCellValue($this->colIndexToLetter($i) . $row, $val);
            }

            // Jawaban per soal
            foreach ($soalList as $i => $soal) {
                $col = $this->colIndexToLetter($startColIndex + $i);
                $jawabData = $jawaban[$soal['id']] ?? null;

                $jawab = '-';
                if (is_array($jawabData)) {
                    if (isset($jawabData['value'])) $jawab = $jawabData['value'];
                    elseif (isset($jawabData['text'])) $jawab = $jawabData['text'];
                    elseif (isset($jawabData['values'])) $jawab = implode(', ', $jawabData['values']);
                    else {
                        $entries = [];
                        foreach ($jawabData as $k => $v) {
                            if (in_array($k, ['is_benar', 'poin'])) continue;
                            $entries[] = "$k: $v";
                        }
                        $jawab = implode(' | ', $entries);
                    }
                }

                $sheet->setCellValue("{$col}{$row}", $jawab);

                // Tandai benar/salah dengan warna (tanpa berat)
                if (isset($jawabData['is_benar'])) {
                    $fill = $jawabData['is_benar'] ? 'C6EFCE' : 'F8CBAD';
                    $sheet->getStyle("{$col}{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($fill);
                }
            }

            $row++;
        }

        // Auto size
        for ($i = 0; $i <= $startColIndex + count($soalList); $i++) {
            $sheet->getColumnDimension($this->colIndexToLetter($i))->setAutoSize(true);
        }

        $sheet->getStyle("A3:{$lastCol}" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


        // =========================
        // === SHEET 2: ANALISIS ===
        // =========================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Analisis');

        $sheet2->setCellValue('A1', 'Analisis ' . $ujian['nama_ujian'] . ' - ' . $ujian['nama_mapel']);
        $sheet2->mergeCells('A1:H1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet2->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet2->setCellValue('A2', 'No');
        $sheet2->setCellValue('B2', 'Nama');
        $sheet2->setCellValue('C2', 'Kelas');
        $sheet2->freezePane('D4');

        // Header topik
        $startCol = 2;
        foreach ($topikList as $i => $topik) {
            $base = $startCol + $i * 3 + 1;
            $c1 = $this->colIndexToLetter($base);
            $c2 = $this->colIndexToLetter($base + 2);

            $sheet2->mergeCells("{$c1}2:{$c2}2");
            $sheet2->setCellValue("{$c1}2", $topik['nama']);
            $sheet2->setCellValue("{$c1}3", 'Benar');
            $sheet2->setCellValue($this->colIndexToLetter($base + 1) . '3', 'Maks');
            $sheet2->setCellValue($this->colIndexToLetter($base + 2) . '3', 'Skor');
        }

        $analisisStartCol = $startCol + count($topikList) * 3 + 1;
        $sheet2->mergeCells($this->colIndexToLetter($analisisStartCol) . '2:' . $this->colIndexToLetter($analisisStartCol + 2) . '2');
        $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol) . '2', 'Analisis');
        $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol) . '3', 'Total Benar');
        $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol + 1) . '3', 'Total Maks');
        $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol + 2) . '3', 'Skor');

        $catatanCol = $analisisStartCol + 3;
        $sheet2->mergeCells($this->colIndexToLetter($catatanCol) . '2:' . $this->colIndexToLetter($catatanCol) . '3');
        $sheet2->setCellValue($this->colIndexToLetter($catatanCol) . '2', 'Catatan');

        // Isi data analisis
        $row = 4;
        $no = 1;
        foreach ($hasilList as $hasil) {
            $jawaban = json_decode($hasil['jawaban_json'], true) ?? [];

            $sheet2->setCellValue("A{$row}", $no++);
            $sheet2->setCellValue("B{$row}", $hasil['nama_peserta']);
            $sheet2->setCellValue("C{$row}", $hasil['nama_kelas']);

            $totalBenar = 0;
            $totalMaks = 0;
            $catatanList = [];

            foreach ($soalByTopik as $topikId => $daftarSoal) {
                $benar = 0;
                $maks = 0;
                foreach ($daftarSoal as $soal) {
                    $sid = $soal['id'];
                    $jawab = $jawaban[$sid] ?? [];
                    $benar += (int)($jawab['poin'] ?? 0);
                    $maks += (int)($soal['bobot'] ?? 1);
                }

                $skor = $maks > 0 ? round(($benar / $maks) * 100) : 0;

                $colBase = $startCol + (array_search($topikId, array_keys($soalByTopik)) * 3) + 1;
                $sheet2->setCellValue($this->colIndexToLetter($colBase) . $row, $benar);
                $sheet2->setCellValue($this->colIndexToLetter($colBase + 1) . $row, $maks);
                $sheet2->setCellValue($this->colIndexToLetter($colBase + 2) . $row, $skor);

                // Catatan
                if ($maks > 0) {
                    if ($skor >= 80) $catatanList[] = 'Sangat baik di ' . $topikMap[$topikId]['nama'];
                    elseif ($skor >= 60) $catatanList[] = 'Cukup di ' . $topikMap[$topikId]['nama'];
                    else $catatanList[] = 'Kurang di ' . $topikMap[$topikId]['nama'];
                }

                $totalBenar += $benar;
                $totalMaks += $maks;
            }

            $skorTotal = $totalMaks > 0 ? round(($totalBenar / $totalMaks) * 100) : 0;
            $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol) . $row, $totalBenar);
            $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol + 1) . $row, $totalMaks);
            $sheet2->setCellValue($this->colIndexToLetter($analisisStartCol + 2) . $row, $skorTotal);
            $sheet2->setCellValue($this->colIndexToLetter($catatanCol) . $row, implode(', ', $catatanList));

            $row++;
        }

        // Styling cepat
        $headerEndCol = $this->colIndexToLetter($catatanCol);
        $sheet2->getStyle("A2:{$headerEndCol}3")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet2->getStyle("A2:{$headerEndCol}" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        for ($i = 0; $i <= $catatanCol; $i++) {
            $sheet2->getColumnDimension($this->colIndexToLetter($i))->setAutoSize(true);
        }

        // Warna nilai otomatis
        for ($r = 4; $r < $row; $r++) {
            $cell = $this->colIndexToLetter($analisisStartCol + 2) . $r;
            $val = (int)$sheet2->getCell($cell)->getValue();
            $clr = $val >= 80 ? 'C6EFCE' : ($val >= 60 ? 'FFF2CC' : 'F8CBAD');
            $sheet2->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($clr);
        }

        // Download file
        $fileName = 'Skoring_' . url_title($ujian['nama_ujian'], '_', true) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tmp = tempnam(sys_get_temp_dir(), 'xls');
        $writer->save($tmp);

        return $this->response
            ->download($fileName, file_get_contents($tmp))
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }



    function colIndexToLetter($index)
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr($index % 26 + 65) . $letters;
            $index = floor($index / 26) - 1;
        }
        return $letters;
    }

    public function sinkronisasi()
    {
        $token = $this->request->getPost('token');
        $url = $this->request->getPost('url');
        $ujianId = $this->request->getPost('ujian_id');

        if (!$token || !$url || !$ujianId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter tidak lengkap.']);
        }

        // Ambil data hasil ujian yg sudah selesai
        $model = new \App\Models\HasilUjianModel();
        $data = $model->where('ujian_id', $ujianId)->where('status', 'selesai')->findAll();

        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data hasil ujian yang selesai.']);
        }

        // Kirim ke server pusat
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post("$url/api/kirimujian", [
                'form_params' => [
                    'token' => $token,
                    'ujian_id' => $ujianId,
                    'data' => json_encode($data)
                ],
                'timeout' => 10
            ]);

            $res = json_decode($response->getBody(), true);
            return $this->response->setJSON($res);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengirim ke server pusat.']);
        }
    }
}
