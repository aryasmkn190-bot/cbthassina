<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use App\Controllers\BaseController;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use Ramsey\Uuid\Uuid;
use App\Models\TopikSoalModel;
use App\Models\BankSoalModel;

class SoalController extends BaseController
{
    protected $soalModel;
    protected $soalOpsiModel;
    protected $validation;
    protected $mediaModel;
    public function __construct()
    {
        $this->soalModel = new SoalModel();
        $this->soalOpsiModel = new SoalOpsiModel();
        $this->validation = \Config\Services::validation();
        $this->mediaModel = new \App\Models\MediaFileModel();
    }
    public function index($bankSoalId)
    {
        $topikModel = new TopikSoalModel();
        $bankSoalModel = new BankSoalModel();
        $bankSoal = $bankSoalModel->where('id', $bankSoalId)->first();
        if (!$bankSoal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if (!has_role('admin') && $bankSoal['created_by'] !== user_id()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $topikList = $topikModel->where('bank_soal_id', $bankSoalId)->findAll();
        $data = [
            'title' => 'Daftar Soal',
            'bank_soal_id' => $bankSoalId,
            'setting' => $this->appSetting(),
            'topik_list' => $topikList,
            'bank_soal' => $bankSoal, // ini yang dikirim
        ];
        return view('Panel/Soal/soal_view', $data);
    }
    public function getAll($bankSoalId)
    {
        if ($this->request->isAJAX()) {
            $soals = $this->soalModel
                ->select('soal.*, topik_soal.nama as nama_topik')
                ->join('topik_soal', 'topik_soal.id = soal.topik_soal_id', 'left')
                ->where('soal.bank_soal_id', $bankSoalId)
                ->orderBy('soal.soal_no', 'ASC')
                ->findAll();


            foreach ($soals as &$soal) {
                $soal['opsi'] = $this->soalOpsiModel
                    ->where('soal_id', $soal['id'])
                    ->orderBy('label', 'ASC')
                    ->findAll();
            }

            return $this->response->setJSON(['status' => true, 'data' => $soals]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }
    public function getSoalById($id)
    {
        if ($this->request->isAJAX()) {
            $soal = $this->soalModel->find($id);
            if (!$soal) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Soal tidak ditemukan.'
                ]);
            }

            // Ambil opsi jika jenis soal cocok
            if (in_array($soal['jenis_soal'], ['pg', 'mpg', 'benar_salah', 'jodohkan'])) {
                $soal['opsi'] = $this->soalOpsiModel
                    ->where('soal_id', $id)
                    ->orderBy('label', 'ASC')
                    ->findAll();
            }

            return $this->response->setJSON([
                'status' => true,
                'data' => $soal
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'bank_soal_id' => 'required',
                'jenis_soal'   => 'required|in_list[pg,mpg,jodohkan,benar_salah,esai,isian]',
                'pertanyaan'   => 'required',
                'topik_soal_id'   => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $soalId = Uuid::uuid4()->toString();
            $jenis = $this->request->getPost('jenis_soal');
            $bankSoalId = $this->request->getPost('bank_soal_id');
            $jawaban = $this->request->getPost('jawaban');
            if ($jenis === 'isian' && is_array($jawaban)) {
                $jawaban = json_encode($jawaban);
            }
            // Ambil soal_no jika dikirim, jika tidak hitung otomatis
            $soalNo = $this->request->getPost('soal_no');
            if (!$soalNo) {
                $jumlah = $this->soalModel
                    ->where('bank_soal_id', $bankSoalId)
                    ->countAllResults();
                $soalNo = $jumlah + 1;
            }

            $this->soalModel->insert([
                'id'           => $soalId,
                'bank_soal_id' => $bankSoalId,
                'soal_no'      => $soalNo,
                'jenis_soal'   => $jenis,
                'pertanyaan'   => $this->request->getPost('pertanyaan'),
                'topik_soal_id'   => $this->request->getPost('topik_soal_id'),
                'jawaban' => $jawaban ?? null,
                'bobot'        => $this->request->getPost('bobot_total') ?? 1,
                'created_at'   => date('Y-m-d H:i:s')
            ]);

            // Insert opsi jika diperlukan
            if (in_array($jenis, ['pg', 'mpg', 'benar_salah', 'jodohkan'])) {
                $opsi = json_decode($this->request->getPost('pilihan'), true) ?? [];
                foreach ($opsi as $i => $item) {
                    $this->soalOpsiModel->insert([
                        'id'         => Uuid::uuid4()->toString(),
                        'soal_id'    => $soalId,
                        'label'      => chr(65 + $i),
                        'teks'       => is_array($item) ? ($item['teks'] ?? '-') : $item,
                        'pasangan'   => is_array($item) ? ($item['pasangan'] ?? null) : null,
                        'bobot'      => is_array($item) && isset($item['bobot_opsi']) ? (float)$item['bobot_opsi'] : 1,
                        'is_true'    => is_array($item) && !empty($item['benar']) ? 1 : 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            // Update used_in_soal di media_files
            $pertanyaanHtml = $this->request->getPost('pertanyaan');

            // Ambil semua src media dari pertanyaan
            preg_match_all('/<(?:img|audio|video)[^>]*src="([^"]+)"/i', $pertanyaanHtml, $matches);
            $srcList = $matches[1] ?? [];

            foreach ($srcList as $src) {
                // Ambil path dari URL
                $parsedPath = parse_url($src, PHP_URL_PATH); // hasil: "/uploads/images/xxxx.png"

                // Hilangkan leading slash supaya match dengan DB
                $path = ltrim($parsedPath, '/'); // hasil: "uploads/images/xxxx.png"

                // Update media_files
                $this->mediaModel
                    ->where('path', $path)
                    ->set('used_in_soal', $soalId)
                    ->update();
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Soal berhasil ditambahkan.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    // public function update($id)
    // {
    //     if ($this->request->isAJAX()) {
    //         $rules = [
    //             'jenis_soal'   => 'required|in_list[pg,mpg,jodohkan,benar_salah,esai,isian]',
    //             'pertanyaan'   => 'required',
    //             'topik_soal_id'   => 'required'
    //         ];

    //         if (!$this->validate($rules)) {
    //             return $this->response->setJSON([
    //                 'status' => false,
    //                 'message' => $this->validation->getErrors()
    //             ]);
    //         }

    //         $jenis = $this->request->getPost('jenis_soal');

    //         $this->soalModel->update($id, [
    //             'jenis_soal'  => $jenis,
    //             'pertanyaan'  => $this->request->getPost('pertanyaan'),
    //             'topik_soal_id'   => $this->request->getPost('topik_soal_id'),
    //             'jawaban'     => $this->request->getPost('jawaban') ?? null,
    //             'bobot'       => $this->request->getPost('bobot_total') ?? 1,
    //             'updated_at'  => date('Y-m-d H:i:s')
    //         ]);

    //         // Hapus opsi lama, jika ada
    //         $this->soalOpsiModel->where('soal_id', $id)->delete();

    //         // Insert ulang opsi baru jika diperlukan
    //         if (in_array($jenis, ['pg', 'mpg', 'benar_salah', 'jodohkan'])) {
    //             $opsi = json_decode($this->request->getPost('pilihan'), true) ?? [];
    //             foreach ($opsi as $i => $item) {
    //                 $this->soalOpsiModel->insert([
    //                     'id'       => Uuid::uuid4()->toString(),
    //                     'soal_id'  => $id,
    //                     'label'    => chr(65 + $i),
    //                     'teks'     => is_array($item) ? ($item['teks'] ?? '-') : $item,
    //                     'pasangan'   => is_array($item) ? ($item['pasangan'] ?? null) : null,
    //                     'bobot'      => is_array($item) && isset($item['bobot_opsi']) ? (float)$item['bobot_opsi'] : 1,
    //                     'is_true'  => is_array($item) && !empty($item['benar']) ? 1 : 0,
    //                     'created_at' => date('Y-m-d H:i:s')
    //                 ]);
    //             }
    //         }

    //         return $this->response->setJSON([
    //             'status' => true,
    //             'message' => 'Soal berhasil diperbarui.'
    //         ]);
    //     }
    //     return $this->fail('Hanya bisa diakses via AJAX.');
    // }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'jenis_soal'     => 'required|in_list[pg,mpg,jodohkan,benar_salah,esai,isian]',
                'pertanyaan'     => 'required',
                'topik_soal_id'  => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $jenis = $this->request->getPost('jenis_soal');

            // Update soal
            $this->soalModel->update($id, [
                'jenis_soal'    => $jenis,
                'pertanyaan'    => $this->request->getPost('pertanyaan'),
                'topik_soal_id' => $this->request->getPost('topik_soal_id'),
                'jawaban'       => $this->request->getPost('jawaban') ?? null,
                'bobot'         => $this->request->getPost('bobot_total') ?? 1,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

            // Update opsi (update/tambah berdasarkan label, hapus label yang tidak dikirim)
            if (in_array($jenis, ['pg', 'mpg', 'benar_salah', 'jodohkan'])) {
                $opsiInput = json_decode($this->request->getPost('pilihan'), true) ?? [];

                $labelBaru = [];
                foreach ($opsiInput as $i => $item) {
                    $label = chr(65 + $i); // A, B, C, ...
                    $labelBaru[] = $label;

                    $data = [
                        'soal_id'   => $id,
                        'label'     => $label,
                        'teks'      => $item['teks'] ?? '-',
                        'pasangan'  => $item['pasangan'] ?? null,
                        'bobot'     => isset($item['bobot_opsi']) ? (float)$item['bobot_opsi'] : 1,
                        'is_true'   => !empty($item['benar']) ? 1 : 0,
                    ];

                    $existing = $this->soalOpsiModel
                        ->where('soal_id', $id)
                        ->where('label', $label)
                        ->first();

                    if ($existing) {
                        $this->soalOpsiModel->update($existing['id'], $data);
                    } else {
                        $data['id'] = Uuid::uuid4()->toString();
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $this->soalOpsiModel->insert($data);
                    }
                }

                // Hapus label yang sudah tidak ada
                if (!empty($labelBaru)) {
                    $this->soalOpsiModel
                        ->where('soal_id', $id)
                        ->whereNotIn('label', $labelBaru)
                        ->delete();
                }
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Soal berhasil diperbarui.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    public function delete($id)
    {
        if ($this->request->isAJAX()) {

            // Hapus opsi terkait
            $this->soalOpsiModel->where('soal_id', $id)->delete();

            // Ambil semua media terkait soal ini
            $mediaFiles = $this->mediaModel->where('used_in_soal', $id)->findAll();

            foreach ($mediaFiles as $media) {
                $fullPath = FCPATH . $media['path'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }

                // Hapus dari database
                $this->mediaModel->delete($media['id']);
            }

            // Hapus soalnya
            $this->soalModel->delete($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Soal dan media berhasil dihapus.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    private function fail($message)
    {
        return $this->response->setJSON([
            'status' => false,
            'message' => $message
        ]);
    }
    public function import()
    {
        $file = $this->request->getFile('file');
        $bankSoalId = $this->request->getPost('bank_soal_id');

        if (!$this->request->isAJAX() || !$file || !$file->isValid() || !$bankSoalId) {
            return $this->fail('Gunakan metode yang benar dan unggah file yang valid.');
        }

        $extension = strtolower($file->getClientExtension());

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                return $this->importExcel($file, $bankSoalId);
            } elseif ($extension === 'docx') {
                return $this->importWord($file, $bankSoalId);
            } else {
                return $this->fail('Ekstensi file tidak didukung. Gunakan .xlsx atau .docx');
            }
        } catch (\Throwable $e) {
            return $this->fail('Gagal memproses file: ' . $e->getMessage());
        }
    }

    protected function importExcel($file, $bankSoalId)
    {
        // $file = $this->request->getFile('file');
        // $bankSoalId = $this->request->getPost('bank_soal_id');

        if (!$this->request->isAJAX() || !$file || !$file->isValid() || !$bankSoalId) {
            return $this->fail('Gunakan metode yang benar dan unggah file.');
        }

        try {
            $spreadsheet = ExcelIOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($sheet); // Hapus baris header

            $topikModel = new TopikSoalModel();
            $soalModel = new SoalModel();
            $opsiModel = new SoalOpsiModel();

            $inserted = 0;
            $gagal = [];

            foreach ($sheet as $i => $row) {
                $rowNum = $i + 2;

                $topikNama = trim($row['B'] ?? '');
                $soalNo = trim($row['C'] ?? '');
                $pertanyaan = trim($row['D'] ?? '');
                $jenisSoal = strtolower(trim($row['E'] ?? ''));
                $jawaban = trim($row['F'] ?? '');
                $bobot = is_numeric($row['G']) ? (float)$row['G'] : 0;

                if (!$topikNama || !$pertanyaan || !$jenisSoal) {
                    $gagal[] = ['baris' => $rowNum, 'alasan' => 'Data wajib (topik/soal/jenis) kosong'];
                    continue;
                }

                if (!in_array($jenisSoal, ['pg', 'mpg', 'benar_salah', 'jodohkan', 'isian', 'esai'])) {
                    $gagal[] = ['baris' => $rowNum, 'alasan' => "Jenis soal '$jenisSoal' tidak dikenali"];
                    continue;
                }

                // Cek atau buat topik
                $topik = $topikModel->where('nama', $topikNama)->where('bank_soal_id', $bankSoalId)->first();
                if (!$topik) {
                    $topikId = Uuid::uuid4()->toString();
                    $topikModel->insert([
                        'id' => $topikId,
                        'bank_soal_id' => $bankSoalId,
                        'nama' => $topikNama
                    ]);
                } else {
                    $topikId = $topik['id'];
                }
                // Cek apakah soal sudah ada dengan pertanyaan sama di topik & bank soal
                $cekSoal = $soalModel
                    ->where('bank_soal_id', $bankSoalId)
                    ->where('topik_soal_id', $topikId)
                    ->where('pertanyaan', $pertanyaan)
                    ->first();

                if ($cekSoal) {
                    $gagal[] = ['baris' => $rowNum, 'alasan' => 'Soal dengan pertanyaan ini sudah ada sebelumnya'];
                    continue;
                } else {
                    $soalId = Uuid::uuid4()->toString();

                    $soalModel->insert([
                        'id' => $soalId,
                        'bank_soal_id' => $bankSoalId,
                        'soal_no' => $soalNo ?: ($i + 1),
                        'jenis_soal' => $jenisSoal,
                        'pertanyaan' => $pertanyaan,
                        'jawaban' => $jawaban,
                        'bobot' => $bobot,
                        'topik_soal_id' => $topikId,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // Opsi dan pasangan
                    if (in_array($jenisSoal, ['pg', 'mpg', 'benar_salah', 'jodohkan'])) {
                        // Untuk benar_salah, pecah jawaban sekali saja
                        $jawabanArrayBs = [];
                        if ($jenisSoal === 'benar_salah') {
                            $jawabanArrayBs = array_map('strtolower', array_map('trim', explode(',', $jawaban)));
                        }

                        foreach (range(0, 4) as $idx) {
                            $label = chr(65 + $idx); // A-E
                            $colOpsi = chr(72 + ($idx * 2)); // H, J, L, N, P
                            $colBobot = chr(ord($colOpsi) + 1); // I, K, M, O, Q
                            $colPasangan = chr(82 + $idx); // R, S, T, U, V

                            $teks = trim($row[$colOpsi] ?? '');
                            if ($teks === '') continue;

                            $opsi = [
                                'id' => Uuid::uuid4()->toString(),
                                'soal_id' => $soalId,
                                'label' => $label,
                                'teks' => $teks,
                                'is_true' => 0,
                                'bobot' => is_numeric($row[$colBobot] ?? '') ? (float)$row[$colBobot] : 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ];

                            // Penanda jawaban benar
                            if ($jenisSoal === 'pg') {
                                if (strtoupper(trim($jawaban)) === $label) {
                                    $opsi['is_true'] = 1;
                                }
                            } elseif ($jenisSoal === 'mpg') {
                                $jawabanLabels = array_map('trim', explode(',', strtoupper($jawaban)));
                                if (in_array($label, $jawabanLabels)) {
                                    $opsi['is_true'] = 1;
                                }
                            } elseif ($jenisSoal === 'benar_salah') {
                                // === PERUBAHAN PENTING: treat like MPG per posisi ===
                                $jawabanLabel = $jawabanArrayBs[$idx] ?? ''; // 'b' atau 's' (atau 'benar'/'salah')
                                if (in_array($jawabanLabel, ['b', 'benar'])) {
                                    $opsi['is_true'] = 1;
                                } else {
                                    $opsi['is_true'] = 0;
                                }
                            }

                            // Pasangan (jodohkan)
                            if ($jenisSoal === 'jodohkan') {
                                $opsi['pasangan'] = trim($row[$colPasangan] ?? '');
                            }

                            $opsiModel->insert($opsi);
                        }
                    }
                }

                $inserted++;
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => "$inserted soal berhasil diimpor.",
                'gagal' => $gagal
            ]);
        } catch (\Throwable $e) {
            return $this->fail('Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function exportExcel($bankSoalId)
    {
        $soalModel  = new \App\Models\SoalModel();
        $opsiModel  = new \App\Models\SoalOpsiModel();
        $topikModel = new \App\Models\TopikSoalModel();

        $soalList = $soalModel
            ->where('bank_soal_id', $bankSoalId)
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'No',
            'Topik',
            'Urutan Soal',
            'Pertanyaan',
            'Jenis Soal',
            'Jawaban',
            'Bobot Total',
            'Opsi A',
            'Bobot A',
            'Opsi B',
            'Bobot B',
            'Opsi C',
            'Bobot C',
            'Opsi D',
            'Bobot D',
            'Opsi E',
            'Bobot E',
            'Pasangan A',
            'Pasangan B',
            'Pasangan C',
            'Pasangan D',
            'Pasangan E'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($soalList as $i => $soal) {
            $topik = $topikModel->find($soal['topik_soal_id']);
            $opsi = $opsiModel
                ->where('soal_id', $soal['id'])
                ->orderBy('label', 'ASC')
                ->findAll();

            $opsiTeks  = array_fill(0, 5, '');
            $bobotOpsi = array_fill(0, 5, '');
            $pasangan  = array_fill(0, 5, '');

            foreach ($opsi as $o) {
                $index = ord($o['label']) - 65; // A=0, B=1, dst
                if ($index >= 0 && $index <= 4) {
                    $opsiTeks[$index]  = $o['teks'] ?? '';
                    $bobotOpsi[$index] = $o['bobot'] ?? 1;
                    if ($soal['jenis_soal'] === 'jodohkan') {
                        $pasangan[$index] = $o['pasangan'] ?? '';
                    }
                }
            }

            // === Tentukan jawaban sesuai jenis soal ===
            $jawaban = $soal['jawaban']; // default untuk esai/isian
            if (in_array($soal['jenis_soal'], ['pg', 'mpg', 'benar_salah'])) {
                $jawabanBenar = [];

                if ($soal['jenis_soal'] === 'benar_salah') {
                    // untuk benar/salah -> B jika is_true=1, S jika is_true=0
                    foreach ($opsi as $o) {
                        $jawabanBenar[] = !empty($o['is_true']) ? 'B' : 'S';
                    }
                } else {
                    // untuk pg / mpg -> ambil label (A, B, C, ...)
                    foreach ($opsi as $o) {
                        if (!empty($o['is_true'])) {
                            $jawabanBenar[] = $o['label'];
                        }
                    }
                }

                $jawaban = implode(',', $jawabanBenar);
            }


            // === Isi ke Excel ===
            $sheet->fromArray([
                $i + 1,
                $topik['nama'] ?? '',
                $soal['soal_no'],
                $soal['pertanyaan'],
                $soal['jenis_soal'],
                $jawaban,
                $soal['bobot'],
                $opsiTeks[0],
                $bobotOpsi[0],
                $opsiTeks[1],
                $bobotOpsi[1],
                $opsiTeks[2],
                $bobotOpsi[2],
                $opsiTeks[3],
                $bobotOpsi[3],
                $opsiTeks[4],
                $bobotOpsi[4],
                $pasangan[0],
                $pasangan[1],
                $pasangan[2],
                $pasangan[3],
                $pasangan[4]
            ], null, 'A' . $rowNum++);
        }

        $fileName = 'soal_' . date('Ymd_His') . '.xlsx';

        // Header download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        // Bersihkan buffer
        ob_clean();
        flush();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    private function importWord($file, $bankSoalId)
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getTempName());

        // Folder upload
        $uploadDir = FCPATH . 'uploads/images/';
        $imageMapping = $this->extractImagesFromDocxWithMapping($file->getTempName(), $uploadDir);

        $tables = [];
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $tables[] = $element;
                }
            }
        }

        $topikModel = new \App\Models\TopikSoalModel();
        $soalModel  = new \App\Models\SoalModel();
        $opsiModel  = new \App\Models\SoalOpsiModel();
        $mediaModel = new \App\Models\MediaFileModel();

        $inserted = 0;
        $gagal = [];
        $parsed = [];

        foreach ($tables as $table) {
            $rows = $table->getRows();
            $currentSoal = [];
            $imagesUsedInQuestion = []; // simpan file gambar yang dipakai

            foreach ($rows as $row) {
                $cells = $row->getCells();
                if (count($cells) < 2) continue;

                $keyRaw = $this->extractTextFromCell($cells[0], $imageMapping, $imagesUsedInQuestion);
                $key = strtoupper(trim(preg_replace('/^>{1,3}/', '', html_entity_decode($keyRaw))));
                $key = preg_replace('/[^A-Z0-9]/', '', $key);

                $value = trim($this->extractTextFromCell($cells[1], $imageMapping, $imagesUsedInQuestion));

                if ($key !== '') {
                    $currentSoal[$key] = $value;
                }

                if ($key === 'NOMOR' && !empty($currentSoal)) {
                    $parsed[] = $currentSoal;
                    $this->simpanSoalWord($currentSoal, $bankSoalId, $topikModel, $soalModel, $opsiModel, $mediaModel, $inserted, $gagal, $imagesUsedInQuestion);
                    $currentSoal = [];
                    $imagesUsedInQuestion = [];
                }
            }

            if (!empty($currentSoal)) {
                $parsed[] = $currentSoal;
                $this->simpanSoalWord($currentSoal, $bankSoalId, $topikModel, $soalModel, $opsiModel, $mediaModel, $inserted, $gagal, $imagesUsedInQuestion);
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "$inserted soal berhasil diimpor dari Word.",
            'parsed' => $parsed,
            'gagal' => $gagal
        ]);
    }

    // ======================== Simpan Soal ========================
    private function simpanSoalWord($data, $bankSoalId, $topikModel, $soalModel, $opsiModel, $mediaModel, &$inserted, &$gagal, $imagesUsedInQuestion = [])
    {
        if (empty($data['PERTANYAAN']) || empty($data['JENIS'])) {
            $gagal[] = ['baris' => $data['NOMOR'] ?? '?', 'alasan' => 'Pertanyaan atau jenis soal kosong'];
            return;
        }

        $jenis = strtolower($data['JENIS']);
        if (!in_array($jenis, ['pg', 'mpg', 'benar_salah', 'jodohkan', 'esai', 'isian'])) {
            $gagal[] = ['baris' => $data['NOMOR'] ?? '?', 'alasan' => 'Jenis soal tidak dikenali'];
            return;
        }

        $existing = $soalModel->where('bank_soal_id', $bankSoalId)
            ->where('pertanyaan', $data['PERTANYAAN'])
            ->first();

        if ($existing) {
            $gagal[] = ['baris' => $data['NOMOR'] ?? '?', 'alasan' => 'Soal sudah ada'];
            return;
        }

        $topik = $topikModel->where('nama', $data['TOPIK'] ?? 'Umum')->where('bank_soal_id', $bankSoalId)->first();
        $topikId = $topik ? $topik['id'] : Uuid::uuid4()->toString();
        if (!$topik) {
            $topikModel->insert([
                'id' => $topikId,
                'bank_soal_id' => $bankSoalId,
                'nama' => $data['TOPIK'] ?? 'Umum'
            ]);
        }

        $soalId = Uuid::uuid4()->toString();
        $jawabanRaw = $data['JAWABAN'] ?? null;
        $jawaban = $jenis === 'isian' ? json_encode(array_map('trim', explode(',', strtolower($jawabanRaw)))) : $jawabanRaw;

        // Total bobot
        $bobotTotal = 0;
        foreach (['A', 'B', 'C', 'D', 'E'] as $opt) {
            if (!empty($data['BOBOT' . $opt])) $bobotTotal += floatval($data['BOBOT' . $opt]);
        }

        $soalModel->insert([
            'id' => $soalId,
            'bank_soal_id' => $bankSoalId,
            'soal_no' => $data['NOMOR'] ?? ($inserted + 1),
            'jenis_soal' => $jenis,
            'pertanyaan' => $data['PERTANYAAN'],
            'jawaban' => $jawaban,
            'bobot' => $bobotTotal > 0 ? $bobotTotal : 1,
            'topik_soal_id' => $topikId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Simpan opsi
        foreach (['A', 'B', 'C', 'D', 'E'] as $i => $opt) {
            if (empty($data[$opt])) continue;
            $opsi = [
                'id' => Uuid::uuid4()->toString(),
                'soal_id' => $soalId,
                'label' => $opt,
                'teks' => $data[$opt],
                'bobot' => is_numeric($data['BOBOT' . $opt] ?? null) ? (float)$data['BOBOT' . $opt] : 0,
                'is_true' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($jenis === 'pg' && strtoupper($data['JAWABAN'] ?? '') === $opt) $opsi['is_true'] = 1;
            elseif ($jenis === 'mpg' && in_array($opt, explode(',', strtoupper($data['JAWABAN'] ?? '')))) $opsi['is_true'] = 1;
            elseif ($jenis === 'benar_salah') {
                $jawabanList = explode(',', strtoupper($data['JAWABAN'] ?? ''));
                $opsi['is_true'] = ($jawabanList[$i] ?? '') === 'B' ? 1 : 0;
            } elseif ($jenis === 'jodohkan') {
                $opsi['pasangan'] = $data['PASANGAN' . $opt] ?? '';
            }

            $opsiModel->insert($opsi);
        }

        // Simpan file gambar yang dipakai ke media_files
        foreach ($imagesUsedInQuestion as $filename) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $mimeType = mime_content_type(FCPATH . 'uploads/images/' . $filename);
            $mediaModel->insert([
                'id' => Uuid::uuid4()->toString(),
                'path' => 'uploads/images/' . $filename,
                'type' => 'image',
                'mime_type' => $mimeType,
                'used_in_soal' => $soalId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $inserted++;
    }

    // ======================== Extract Text + Gambar ========================
    private function extractTextFromCell($cell, $imageMapping, &$imagesUsedInQuestion = [])
    {
        $text = '';
        foreach ($cell->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                $text .= $element->getText();
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
                // Setiap TextBreak → satu <br>
                $text .= "<br>";
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
                $source = $element->getSource();
                $filenameKey = basename($source);
                if (isset($imageMapping[$filenameKey])) {
                    $filename = $imageMapping[$filenameKey];
                    $text .= '<br><img src="/uploads/images/' . $filename . '" style="max-width:300px;"><br>';
                    $imagesUsedInQuestion[] = $filename;
                }
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                foreach ($element->getElements() as $child) {
                    if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $child->getText();
                    } elseif ($child instanceof \PhpOffice\PhpWord\Element\TextBreak) {
                        $text .= "<br>";
                    } elseif ($child instanceof \PhpOffice\PhpWord\Element\Image) {
                        $source = $child->getSource();
                        $filenameKey = basename($source);
                        if (isset($imageMapping[$filenameKey])) {
                            $filename = $imageMapping[$filenameKey];
                            $text .= '<br><img src="/uploads/images/' . $filename . '" style="max-width:300px;"><br>';
                            $imagesUsedInQuestion[] = $filename;
                        }
                    }
                }
            }
        }

        // jangan trim() di sini agar baris baru berturut-turut tidak hilang
        return $text;
    }






    // ======================== Extract Gambar DOCX ========================
    private function extractImagesFromDocxWithMapping($filePath, $targetDir)
    {
        $mapping = [];
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {

            // Ambil semua file media
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (preg_match('/^word\/media\/(.+)$/i', $entry, $matches)) {
                    $stream = $zip->getFromIndex($i);
                    if ($stream) {
                        $ext = pathinfo($entry, PATHINFO_EXTENSION);
                        $filename = uniqid('img_') . '.' . $ext;
                        file_put_contents($targetDir . $filename, $stream);
                        // mapping nama file internal Word → nama file publik
                        $mapping[$matches[1]] = $filename;
                    }
                }
            }

            // Ambil rels untuk mapping relId → file media
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (preg_match('/^word\/_rels\/document\.xml\.rels$/i', $entry)) {
                    $relsXml = $zip->getFromIndex($i);
                    $xml = simplexml_load_string($relsXml);
                    foreach ($xml->Relationship as $rel) {
                        $id = (string)$rel['Id'];
                        $target = basename((string)$rel['Target']);
                        if (isset($mapping[$target])) {
                            $mapping[$id] = $mapping[$target];
                        }
                    }
                }
            }

            $zip->close();
        }

        return $mapping;
    }







    public function printSoal($bankSoalId)
    {
        $soalModel = new \App\Models\SoalModel();
        $opsiModel = new \App\Models\SoalOpsiModel();
        $topikModel = new \App\Models\TopikSoalModel();
        $bankSoalModel = new \App\Models\BankSoalModel();

        $bankSoal = $bankSoalModel->find($bankSoalId);
        if (!$bankSoal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bank soal tidak ditemukan.");
        }

        $soalList = $soalModel
            ->where('bank_soal_id', $bankSoalId)
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        foreach ($soalList as &$soal) {
            $soal['topik'] = $topikModel->find($soal['topik_soal_id']);
            $soal['opsi'] = $opsiModel
                ->where('soal_id', $soal['id'])
                ->orderBy('label', 'ASC')
                ->findAll();
        }

        $kunci = $this->request->getGet('kunci') === '1';

        return view('Panel/BankSoal/print_soal_view', [
            'bankSoal' => $bankSoal,
            'soalList' => $soalList,
            'kunci'    => $kunci
        ]);
    }


    public function image()
    {
        return $this->handleUpload('image', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 'uploads/images');
    }

    public function audio()
    {
        return $this->handleUpload('audio', ['mp3', 'wav', 'ogg'], 'uploads/audio');
    }

    public function video()
    {
        return $this->handleUpload('video', ['mp4', 'webm', 'ogg'], 'uploads/video');
    }

    private function handleUpload($key, array $allowedExtensions, $targetDir)
    {
        $usedInSoal = $this->request->getPost('used_in_soal');
        $file = $this->request->getFile($key);

        if (!$file || !$file->isValid()) {
            return $this->fail('File tidak valid.');
        }

        // Ekstensi dan MIME
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, $allowedExtensions)) {
            return $this->fail("Ekstensi file '$ext' tidak didukung.");
        }

        // Ambil MIME dengan aman
        $mimeType = $file->getMimeType();
        if (!$mimeType && is_file($file->getTempName())) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file->getTempName());
            finfo_close($finfo);
        }

        // Buat nama dan path baru
        $newName = Uuid::uuid4()->toString() . '.' . $ext;
        $relativePath = $targetDir . '/' . $newName;
        $fullPath = FCPATH . $relativePath;

        // Pastikan direktori ada
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Pindahkan file
        if (!$file->move(dirname($fullPath), $newName)) {
            return $this->fail('Gagal menyimpan file.');
        }

        // Simpan ke database
        $this->mediaModel->saveMedia(
            $relativePath,
            $key, // type: image, audio, video
            $mimeType,
            $usedInSoal ?: null
        );

        return $this->response->setJSON([
            'status' => true,
            'url' => base_url($relativePath)
        ]);
    }



    public function deleteFile()
    {
        $path = $this->request->getPost('path');
        $fullPath = FCPATH . $path;

        // Validasi folder
        $allowedDirs = ['uploads/image/', 'uploads/images/', 'uploads/audio/', 'uploads/video/'];
        $allowed = false;
        foreach ($allowedDirs as $dir) {
            if (str_starts_with($path, $dir)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Folder tidak diizinkan.'
            ]);
        }

        // Hapus file dari filesystem
        $fileDeleted = false;
        if (file_exists($fullPath)) {
            $fileDeleted = unlink($fullPath);
        }

        // Hapus dari database (media_files)
        $deletedDb = $this->mediaModel->deleteByPath($path);

        return $this->response->setJSON([
            'status' => $fileDeleted || $deletedDb,
            'message' => $fileDeleted ? 'File berhasil dihapus.' : 'File tidak ditemukan (hanya data DB dihapus).'
        ]);
    }
}
