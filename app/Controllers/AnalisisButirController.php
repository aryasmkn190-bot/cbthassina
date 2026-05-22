<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BankSoalModel;
use App\Models\UjianModel;
use App\Models\HasilUjianModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\PesertaModel;
use App\Models\TopikSoalModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AnalisisButirController extends BaseController
{
    protected $bankSoalModel;
    protected $ujianModel;
    protected $hasilUjianModel;
    protected $soalModel;
    protected $soalOpsiModel;
    protected $pesertaModel;
    protected $topikSoalModel;

    public function __construct()
    {
        $this->bankSoalModel   = new BankSoalModel();
        $this->ujianModel      = new UjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->soalModel       = new SoalModel();
        $this->soalOpsiModel   = new SoalOpsiModel();
        $this->pesertaModel    = new PesertaModel();
        $this->topikSoalModel  = new TopikSoalModel();
    }

    /**
     * Halaman daftar bank soal untuk dianalisis.
     * Admin: lihat semua. Guru: hanya miliknya.
     */
    public function index()
    {
        $data = [
            'title'   => 'Analisis Butir Soal',
            'setting' => $this->appSetting(),
        ];

        return view('Panel/AnalisisButir/analisis_index', $data);
    }

    /**
     * API: daftar bank soal (AJAX)
     * Dipanggil oleh view index via JS.
     * Reuse BankSoalModel::getAll() yang sudah handle role.
     */

    /**
     * Halaman pilih ujian dari bank soal tertentu.
     */
    public function pilihUjian($bankSoalId)
    {
        $bankSoal = $this->bankSoalModel->find($bankSoalId);
        if (!$bankSoal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Otorisasi: guru hanya bisa lihat bank soal miliknya
        if (has_role('guru') && $bankSoal['created_by'] !== user_id()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Ambil daftar ujian yg menggunakan bank soal ini
        $ujianList = $this->ujianModel
            ->select('ujian.*, COUNT(DISTINCT hasil_ujian.id) as jumlah_peserta_selesai')
            ->join('hasil_ujian', 'hasil_ujian.ujian_id = ujian.id AND hasil_ujian.status = "selesai"', 'left')
            ->where('ujian.bank_soal_id', $bankSoalId)
            ->groupBy('ujian.id')
            ->orderBy('ujian.created_at', 'DESC')
            ->findAll();

        $data = [
            'title'     => 'Pilih Ujian - Analisis Butir Soal',
            'setting'   => $this->appSetting(),
            'bankSoal'  => $bankSoal,
            'ujianList' => $ujianList,
        ];

        return view('Panel/AnalisisButir/analisis_pilih_ujian', $data);
    }

    /**
     * Halaman detail analisis (dashboard + chart).
     */
    public function detail($ujianId)
    {
        $ujian = $this->ujianModel->getWithNamaMapel($ujianId);
        if (!$ujian) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $bankSoal = $this->bankSoalModel->find($ujian['bank_soal_id']);

        // Otorisasi
        if (has_role('guru') && $bankSoal['created_by'] !== user_id()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Ambil daftar kelas unik dari peserta yang ikut ujian ini
        $kelasList = $this->hasilUjianModel
            ->select('kelas.nama as nama_kelas')
            ->join('peserta', 'peserta.id = hasil_ujian.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('hasil_ujian.ujian_id', $ujianId)
            ->where('hasil_ujian.status', 'selesai')
            ->where('kelas.nama IS NOT NULL')
            ->groupBy('kelas.nama')
            ->findAll();

        $data = [
            'title'     => 'Analisis Butir Soal',
            'setting'   => $this->appSetting(),
            'ujian'     => $ujian,
            'bankSoal'  => $bankSoal,
            'ujianId'   => $ujianId,
            'kelasList' => array_column($kelasList, 'nama_kelas'),
        ];

        return view('Panel/AnalisisButir/analisis_detail', $data);
    }

    /**
     * API endpoint: menghitung semua statistik analisis butir soal.
     * Return JSON untuk chart dan tabel.
     */
    public function apiAnalisis($ujianId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => false, 'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ujian tidak ditemukan.']);
        }

        $bankSoal = $this->bankSoalModel->find($ujian['bank_soal_id']);

        // Otorisasi
        if (has_role('guru') && $bankSoal['created_by'] !== user_id()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => false, 'message' => 'Akses ditolak.'
            ]);
        }

        $filterKelas = $this->request->getGet('kelas');

        // === 1. Ambil data hasil ujian (selesai saja) ===
        $queryHasil = $this->hasilUjianModel
            ->select('hasil_ujian.*, peserta.nama as nama_peserta, peserta.nisn, kelas.nama as nama_kelas')
            ->join('peserta', 'peserta.id = hasil_ujian.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('hasil_ujian.ujian_id', $ujianId)
            ->where('hasil_ujian.status', 'selesai');

        if (!empty($filterKelas)) {
            $queryHasil->where('kelas.nama', $filterKelas);
        }

        $hasilList = $queryHasil->orderBy('hasil_ujian.nilai_total', 'DESC')->findAll();

        $totalPeserta = count($hasilList);

        if ($totalPeserta === 0) {
            return $this->response->setJSON([
                'status'  => true,
                'data'    => [
                    'ringkasan'    => ['total_peserta' => 0, 'rata_rata' => 0, 'kr20' => null, 'soal_berkualitas' => 0],
                    'per_soal'     => [],
                    'reliabilitas' => null,
                ]
            ]);
        }

        // === 2. Ambil soal + opsi ===
        $soalList = $this->soalModel
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $soalIds = array_column($soalList, 'id');
        $allOpsi = [];
        if (!empty($soalIds)) {
            $opsiRows = $this->soalOpsiModel->whereIn('soal_id', $soalIds)->orderBy('label', 'ASC')->findAll();
            foreach ($opsiRows as $op) {
                $allOpsi[$op['soal_id']][] = $op;
            }
        }

        // === 3. Decode jawaban semua peserta ===
        $jawabanPerPeserta = []; // [pesertaIndex => [soal_id => jawaban]]
        $skorTotalPerPeserta = []; // [pesertaIndex => skor_total_pg]

        foreach ($hasilList as $idx => $h) {
            $jawaban = json_decode($h['jawaban_json'], true) ?? [];
            $jawabanPerPeserta[$idx] = $jawaban;

            // Hitung skor PG untuk KR-20
            $skorPg = 0;
            foreach ($soalList as $soal) {
                if ($soal['jenis_soal'] !== 'pg') continue;
                $jwb = $jawaban[$soal['id']] ?? null;
                if ($jwb && !empty($jwb['is_benar'])) {
                    $skorPg++;
                }
            }
            $skorTotalPerPeserta[$idx] = $skorPg;
        }

        // Urutkan peserta berdasar nilai total desc untuk kelompok atas/bawah
        $nilaiTotalSorted = array_column($hasilList, 'nilai_total');
        arsort($nilaiTotalSorted);
        $sortedIndices = array_keys($nilaiTotalSorted);

        // Kelompok atas/bawah (27%)
        $nGroup = max(1, round(0.27 * $totalPeserta));
        $indeksAtas  = array_slice($sortedIndices, 0, $nGroup);
        $indeksBawah = array_slice($sortedIndices, -$nGroup);

        // === 4. Hitung statistik per soal ===
        $analisisPerSoal = [];
        $totalNilai = array_sum(array_column($hasilList, 'nilai_total'));
        $rataRata   = $totalPeserta > 0 ? round($totalNilai / $totalPeserta, 2) : 0;

        $sumPQ = 0; // Untuk KR-20
        $jumlahSoalPg = 0;
        $soalBerkualitas = 0;

        foreach ($soalList as $soal) {
            $soalId = $soal['id'];
            $jenisSoal = $soal['jenis_soal'];
            $opsiList = $allOpsi[$soalId] ?? [];

            // Hitung jumlah benar
            $jumlahBenar = 0;
            $jumlahBenarAtas = 0;
            $jumlahBenarBawah = 0;

            // Distribusi jawaban (untuk PG)
            $distribusi = [];
            foreach ($opsiList as $op) {
                $distribusi[$op['label']] = [
                    'label'   => $op['label'],
                    'teks'    => $op['teks'],
                    'is_true' => (bool)$op['is_true'],
                    'count'   => 0,
                    'persen'  => 0,
                    'efektif' => null,
                ];
            }
            $tidakMenjawab = 0;

            foreach ($jawabanPerPeserta as $idx => $jawaban) {
                $jwb = $jawaban[$soalId] ?? null;
                $isBenar = false;

                if ($jwb !== null && isset($jwb['is_benar'])) {
                    $isBenar = (bool)$jwb['is_benar'];
                }

                if ($isBenar) {
                    $jumlahBenar++;
                    if (in_array($idx, $indeksAtas)) $jumlahBenarAtas++;
                    if (in_array($idx, $indeksBawah)) $jumlahBenarBawah++;
                } else {
                    if (in_array($idx, $indeksAtas)) { /* salah di atas */ }
                    if (in_array($idx, $indeksBawah)) { /* salah di bawah */ }
                }

                // Distribusi jawaban PG
                if ($jenisSoal === 'pg' && $jwb !== null) {
                    $jawabanLabel = $jwb['value'] ?? null;
                    if ($jawabanLabel && isset($distribusi[$jawabanLabel])) {
                        $distribusi[$jawabanLabel]['count']++;
                    } else {
                        $tidakMenjawab++;
                    }
                } elseif ($jwb === null) {
                    $tidakMenjawab++;
                }
            }

            // Tingkat Kesukaran (P)
            $p = $totalPeserta > 0 ? round($jumlahBenar / $totalPeserta, 4) : 0;
            $klasifikasiP = $this->klasifikasiTingkatKesukaran($p);

            // Daya Pembeda (D)
            $d = null;
            $klasifikasiD = 'N/A';
            if ($totalPeserta >= 2) {
                $propAtas  = $nGroup > 0 ? $jumlahBenarAtas / $nGroup : 0;
                $propBawah = $nGroup > 0 ? $jumlahBenarBawah / $nGroup : 0;
                $d = round($propAtas - $propBawah, 4);
                $klasifikasiD = $this->klasifikasiDayaPembeda($d);
            }

            // Efektivitas pengecoh (PG)
            $pengecohEfektif = null;
            if ($jenisSoal === 'pg') {
                $pengecohEfektif = 0;
                $totalPengecoh = 0;
                foreach ($distribusi as $label => &$dist) {
                    $dist['persen'] = $totalPeserta > 0 ? round(($dist['count'] / $totalPeserta) * 100, 1) : 0;
                    if (!$dist['is_true']) {
                        $totalPengecoh++;
                        $dist['efektif'] = $dist['persen'] >= 5;
                        if ($dist['efektif']) $pengecohEfektif++;
                    } else {
                        $dist['efektif'] = null; // kunci jawaban
                    }
                }
                unset($dist);
            }

            // Rekomendasi status soal
            $status = $this->rekomendasiStatus($p, $d);

            if ($status === 'Diterima') $soalBerkualitas++;

            // KR-20 data (hanya PG)
            if ($jenisSoal === 'pg') {
                $jumlahSoalPg++;
                $q = 1 - $p;
                $sumPQ += ($p * $q);
            }

            $analisisPerSoal[] = [
                'soal_no'           => $soal['soal_no'],
                'soal_id'           => $soalId,
                'jenis_soal'        => $jenisSoal,
                'pertanyaan'        => mb_substr(strip_tags($soal['pertanyaan']), 0, 100),
                'pertanyaan_html'   => $soal['pertanyaan'],
                'p'                 => $p,
                'klasifikasi_p'     => $klasifikasiP,
                'd'                 => $d,
                'klasifikasi_d'     => $klasifikasiD,
                'pengecoh_efektif'  => $pengecohEfektif,
                'total_pengecoh'    => $jenisSoal === 'pg' ? max(0, count($opsiList) - 1) : null,
                'distribusi'        => array_values($distribusi),
                'tidak_menjawab'    => $tidakMenjawab,
                'status'            => $status,
            ];
        }

        // === 5. Hitung KR-20 ===
        $kr20 = null;
        $klasifikasiKr20 = null;
        if ($jumlahSoalPg > 1 && $totalPeserta > 1) {
            // Varians skor total PG
            $mean = array_sum($skorTotalPerPeserta) / $totalPeserta;
            $sumSquaredDiff = 0;
            foreach ($skorTotalPerPeserta as $skor) {
                $sumSquaredDiff += pow($skor - $mean, 2);
            }
            $varians = $sumSquaredDiff / $totalPeserta;

            if ($varians > 0) {
                $kr20 = round(($jumlahSoalPg / ($jumlahSoalPg - 1)) * (1 - ($sumPQ / $varians)), 4);
                $klasifikasiKr20 = $this->klasifikasiReliabilitas($kr20);
            }
        }

        // === 6. Build response ===
        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'ringkasan' => [
                    'total_peserta'    => $totalPeserta,
                    'rata_rata'        => $rataRata,
                    'kr20'             => $kr20,
                    'klasifikasi_kr20' => $klasifikasiKr20,
                    'soal_berkualitas'  => $soalBerkualitas,
                    'total_soal'       => count($soalList),
                    'total_soal_pg'    => $jumlahSoalPg,
                ],
                'per_soal' => $analisisPerSoal,
            ]
        ]);
    }

    /**
     * Export analisis butir soal ke Excel.
     */
    public function exportExcel($ujianId)
    {
        helper(['text']);

        $ujian = $this->ujianModel->getWithNamaMapel($ujianId);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $bankSoal = $this->bankSoalModel->find($ujian['bank_soal_id']);
        if (has_role('guru') && $bankSoal['created_by'] !== user_id()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $filterKelas = $this->request->getGet('kelas');

        // Ambil data hasil ujian
        $queryHasil = $this->hasilUjianModel
            ->select('hasil_ujian.*, peserta.nama as nama_peserta, peserta.nisn, kelas.nama as nama_kelas')
            ->join('peserta', 'peserta.id = hasil_ujian.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('hasil_ujian.ujian_id', $ujianId)
            ->where('hasil_ujian.status', 'selesai');

        if (!empty($filterKelas)) {
            $queryHasil->where('kelas.nama', $filterKelas);
        }

        $hasilList = $queryHasil->orderBy('hasil_ujian.nilai_total', 'DESC')->findAll();
        $totalPeserta = count($hasilList);

        if ($totalPeserta === 0) {
            return redirect()->back()->with('error', 'Tidak ada data peserta selesai.');
        }

        // Ambil soal + opsi
        $soalList = $this->soalModel
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $soalIds = array_column($soalList, 'id');
        $allOpsi = [];
        if (!empty($soalIds)) {
            $opsiRows = $this->soalOpsiModel->whereIn('soal_id', $soalIds)->orderBy('label', 'ASC')->findAll();
            foreach ($opsiRows as $op) {
                $allOpsi[$op['soal_id']][] = $op;
            }
        }

        // Decode jawaban
        $jawabanPerPeserta = [];
        $skorTotalPerPeserta = [];

        foreach ($hasilList as $idx => $h) {
            $jawaban = json_decode($h['jawaban_json'], true) ?? [];
            $jawabanPerPeserta[$idx] = $jawaban;

            $skorPg = 0;
            foreach ($soalList as $soal) {
                if ($soal['jenis_soal'] !== 'pg') continue;
                $jwb = $jawaban[$soal['id']] ?? null;
                if ($jwb && !empty($jwb['is_benar'])) $skorPg++;
            }
            $skorTotalPerPeserta[$idx] = $skorPg;
        }

        // Kelompok atas/bawah
        $nilaiSorted = array_column($hasilList, 'nilai_total');
        arsort($nilaiSorted);
        $sortedIndices = array_keys($nilaiSorted);
        $nGroup = max(1, round(0.27 * $totalPeserta));
        $indeksAtas  = array_slice($sortedIndices, 0, $nGroup);
        $indeksBawah = array_slice($sortedIndices, -$nGroup);

        // Hitung statistik per soal
        $analisis = [];
        $sumPQ = 0;
        $jumlahSoalPg = 0;

        foreach ($soalList as $soal) {
            $soalId = $soal['id'];
            $opsiList = $allOpsi[$soalId] ?? [];

            $jumlahBenar = 0;
            $jumlahBenarAtas = 0;
            $jumlahBenarBawah = 0;

            $distribusi = [];
            foreach ($opsiList as $op) {
                $distribusi[$op['label']] = ['count' => 0, 'is_true' => (bool)$op['is_true'], 'teks' => $op['teks']];
            }

            foreach ($jawabanPerPeserta as $idx => $jawaban) {
                $jwb = $jawaban[$soalId] ?? null;
                $isBenar = ($jwb !== null && !empty($jwb['is_benar']));

                if ($isBenar) {
                    $jumlahBenar++;
                    if (in_array($idx, $indeksAtas)) $jumlahBenarAtas++;
                    if (in_array($idx, $indeksBawah)) $jumlahBenarBawah++;
                }

                if ($soal['jenis_soal'] === 'pg' && $jwb !== null) {
                    $label = $jwb['value'] ?? null;
                    if ($label && isset($distribusi[$label])) {
                        $distribusi[$label]['count']++;
                    }
                }
            }

            $p = $totalPeserta > 0 ? round($jumlahBenar / $totalPeserta, 4) : 0;
            $d = null;
            if ($totalPeserta >= 2) {
                $d = round(($jumlahBenarAtas / $nGroup) - ($jumlahBenarBawah / $nGroup), 4);
            }

            if ($soal['jenis_soal'] === 'pg') {
                $jumlahSoalPg++;
                $sumPQ += $p * (1 - $p);
            }

            $analisis[] = [
                'soal'       => $soal,
                'p'          => $p,
                'd'          => $d,
                'distribusi' => $distribusi,
                'benar'      => $jumlahBenar,
            ];
        }

        // KR-20
        $kr20 = null;
        if ($jumlahSoalPg > 1 && $totalPeserta > 1) {
            $mean = array_sum($skorTotalPerPeserta) / $totalPeserta;
            $sumSqDiff = 0;
            foreach ($skorTotalPerPeserta as $s) {
                $sumSqDiff += pow($s - $mean, 2);
            }
            $varians = $sumSqDiff / $totalPeserta;
            if ($varians > 0) {
                $kr20 = round(($jumlahSoalPg / ($jumlahSoalPg - 1)) * (1 - ($sumPQ / $varians)), 4);
            }
        }

        // === BUILD SPREADSHEET ===
        $spreadsheet = new Spreadsheet();

        // ========== SHEET 1: RINGKASAN ==========
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan');

        $sheet1->mergeCells('A1:F1');
        $sheet1->setCellValue('A1', 'ANALISIS BUTIR SOAL');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet1->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $infoRows = [
            ['Nama Ujian', $ujian['nama_ujian']],
            ['Bank Soal', $ujian['nama_mapel'] ?? '-'],
            ['Filter Kelas', $filterKelas ?: 'Semua Kelas'],
            ['Total Peserta', $totalPeserta],
            ['Rata-rata Nilai', round(array_sum(array_column($hasilList, 'nilai_total')) / $totalPeserta, 2)],
            ['Total Soal', count($soalList)],
            ['Total Soal PG', $jumlahSoalPg],
            ['Reliabilitas (KR-20)', $kr20 !== null ? $kr20 : 'N/A'],
            ['Klasifikasi Reliabilitas', $kr20 !== null ? $this->klasifikasiReliabilitas($kr20) : 'N/A'],
        ];

        $row = 3;
        foreach ($infoRows as $info) {
            $sheet1->setCellValue("A{$row}", $info[0]);
            $sheet1->setCellValue("C{$row}", $info[1]);
            $sheet1->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;
        }

        // Rangkuman kualitas soal
        $row += 1;
        $sheet1->setCellValue("A{$row}", 'Rangkuman Kualitas Soal');
        $sheet1->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $row++;

        $cDiterima = 0;
        $cRevisi = 0;
        $cDibuang = 0;
        foreach ($analisis as $a) {
            $status = $this->rekomendasiStatus($a['p'], $a['d']);
            if ($status === 'Diterima') $cDiterima++;
            elseif ($status === 'Perlu Revisi') $cRevisi++;
            else $cDibuang++;
        }

        $sheet1->setCellValue("A{$row}", 'Diterima');
        $sheet1->setCellValue("B{$row}", $cDiterima);
        $sheet1->getStyle("A{$row}:B{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF228B22'));
        $row++;
        $sheet1->setCellValue("A{$row}", 'Perlu Revisi');
        $sheet1->setCellValue("B{$row}", $cRevisi);
        $sheet1->getStyle("A{$row}:B{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFF8C00'));
        $row++;
        $sheet1->setCellValue("A{$row}", 'Dibuang/Diganti');
        $sheet1->setCellValue("B{$row}", $cDibuang);
        $sheet1->getStyle("A{$row}:B{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFDC143C'));

        for ($c = 0; $c <= 5; $c++) {
            $sheet1->getColumnDimension($this->colLetter($c))->setAutoSize(true);
        }

        // ========== SHEET 2: ANALISIS BUTIR ==========
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Analisis Butir Soal');

        $sheet2->mergeCells('A1:J1');
        $sheet2->setCellValue('A1', 'ANALISIS BUTIR SOAL - ' . $ujian['nama_ujian']);
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet2->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers2 = ['No', 'Jenis', 'Pertanyaan', 'Benar', 'P (Kesukaran)', 'Klasifikasi P', 'D (Daya Pembeda)', 'Klasifikasi D', 'Pengecoh Efektif', 'Status'];
        foreach ($headers2 as $i => $h) {
            $sheet2->setCellValue($this->colLetter($i) . '3', $h);
        }
        $lastCol2 = $this->colLetter(count($headers2) - 1);
        $sheet2->getStyle("A3:{$lastCol2}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet2->freezePane('A4');

        $row2 = 4;
        foreach ($analisis as $a) {
            $soal = $a['soal'];
            $status = $this->rekomendasiStatus($a['p'], $a['d']);
            $pengecohStr = 'N/A';
            if ($soal['jenis_soal'] === 'pg') {
                $totalPengecoh = max(0, count($a['distribusi']) - 1);
                $efektifCount = 0;
                foreach ($a['distribusi'] as $dist) {
                    if (!$dist['is_true'] && $totalPeserta > 0) {
                        if (($dist['count'] / $totalPeserta) * 100 >= 5) $efektifCount++;
                    }
                }
                $pengecohStr = "{$efektifCount}/{$totalPengecoh}";
            }

            $data2 = [
                $soal['soal_no'],
                strtoupper($soal['jenis_soal']),
                mb_substr(strip_tags($soal['pertanyaan']), 0, 80),
                $a['benar'] . '/' . $totalPeserta,
                $a['p'],
                $this->klasifikasiTingkatKesukaran($a['p']),
                $a['d'] !== null ? $a['d'] : 'N/A',
                $a['d'] !== null ? $this->klasifikasiDayaPembeda($a['d']) : 'N/A',
                $pengecohStr,
                $status,
            ];

            foreach ($data2 as $i => $val) {
                $sheet2->setCellValue($this->colLetter($i) . $row2, $val);
            }

            // Pewarnaan status
            $statusCol = $this->colLetter(count($headers2) - 1) . $row2;
            $statusColor = $status === 'Diterima' ? 'C6EFCE' : ($status === 'Perlu Revisi' ? 'FFF2CC' : 'F8CBAD');
            $sheet2->getStyle($statusCol)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($statusColor);

            $row2++;
        }

        $sheet2->getStyle("A3:{$lastCol2}" . ($row2 - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        for ($i = 0; $i < count($headers2); $i++) {
            $sheet2->getColumnDimension($this->colLetter($i))->setAutoSize(true);
        }

        // ========== SHEET 3: MATRIKS RESPON ==========
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Matriks Respon');

        $sheet3->mergeCells('A1:M1');
        $sheet3->setCellValue('A1', 'MATRIKS RESPON - ' . $ujian['nama_ujian']);
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet3->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $baseHeaders = ['No', 'NISN', 'Nama', 'Kelas', 'Nilai'];
        foreach ($baseHeaders as $i => $h) {
            $sheet3->setCellValue($this->colLetter($i) . '3', $h);
        }
        $startSoalCol = count($baseHeaders);
        foreach ($soalList as $i => $soal) {
            $sheet3->setCellValue($this->colLetter($startSoalCol + $i) . '3', 'Soal-' . ($soal['soal_no'] ?? ($i + 1)));
        }

        $lastCol3 = $this->colLetter($startSoalCol + count($soalList) - 1);
        $sheet3->getStyle("A3:{$lastCol3}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet3->freezePane('F4');

        $row3 = 4;
        $no = 1;
        foreach ($hasilList as $idx => $hasil) {
            $jawaban = $jawabanPerPeserta[$idx];
            $sheet3->setCellValue('A' . $row3, $no++);
            $sheet3->setCellValue('B' . $row3, $hasil['nisn'] ?? '-');
            $sheet3->setCellValue('C' . $row3, $hasil['nama_peserta'] ?? '-');
            $sheet3->setCellValue('D' . $row3, $hasil['nama_kelas'] ?? '-');
            $sheet3->setCellValue('E' . $row3, $hasil['nilai_total']);

            foreach ($soalList as $i => $soal) {
                $col = $this->colLetter($startSoalCol + $i);
                $jwb = $jawaban[$soal['id']] ?? null;

                $jawabText = '-';
                if ($jwb !== null) {
                    if (isset($jwb['value'])) $jawabText = $jwb['value'];
                    elseif (isset($jwb['text'])) $jawabText = mb_substr($jwb['text'], 0, 30);
                    elseif (isset($jwb['values'])) $jawabText = implode(',', $jwb['values']);
                }

                $sheet3->setCellValue("{$col}{$row3}", $jawabText);

                // Warna benar/salah
                if ($jwb !== null && isset($jwb['is_benar'])) {
                    $fillColor = $jwb['is_benar'] ? 'C6EFCE' : 'F8CBAD';
                    $sheet3->getStyle("{$col}{$row3}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($fillColor);
                }
            }
            $row3++;
        }

        $sheet3->getStyle("A3:{$lastCol3}" . ($row3 - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        for ($i = 0; $i <= $startSoalCol + count($soalList); $i++) {
            $sheet3->getColumnDimension($this->colLetter($i))->setAutoSize(true);
        }

        // Download
        $kelasSuffix = !empty($filterKelas) ? '_' . url_title($filterKelas, '_', true) : '';
        $fileName = 'Analisis_Butir_' . url_title($ujian['nama_ujian'], '_', true) . $kelasSuffix . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $tmp = tempnam(sys_get_temp_dir(), 'xls');
        $writer->save($tmp);

        return $this->response
            ->download($fileName, file_get_contents($tmp))
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    // === HELPER FUNCTIONS ===

    private function colLetter($index)
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr($index % 26 + 65) . $letters;
            $index = floor($index / 26) - 1;
        }
        return $letters;
    }

    private function klasifikasiTingkatKesukaran($p)
    {
        if ($p > 0.70) return 'Mudah';
        if ($p >= 0.30) return 'Sedang';
        return 'Sukar';
    }

    private function klasifikasiDayaPembeda($d)
    {
        if ($d >= 0.40) return 'Sangat Baik';
        if ($d >= 0.30) return 'Baik';
        if ($d >= 0.20) return 'Cukup';
        if ($d >= 0.00) return 'Jelek';
        return 'Negatif';
    }

    private function klasifikasiReliabilitas($kr20)
    {
        if ($kr20 >= 0.80) return 'Sangat Tinggi';
        if ($kr20 >= 0.60) return 'Tinggi';
        if ($kr20 >= 0.40) return 'Sedang';
        if ($kr20 >= 0.20) return 'Rendah';
        return 'Sangat Rendah';
    }

    private function rekomendasiStatus($p, $d)
    {
        if ($d === null) return 'N/A';
        if ($d >= 0.30 && $p >= 0.15 && $p <= 0.85) return 'Diterima';
        if ($d >= 0.20) return 'Perlu Revisi';
        return 'Dibuang';
    }

    /**
     * Tampilan cetak PDF analisis butir soal (portrait).
     */
    public function print($ujianId)
    {
        $ujian = $this->ujianModel->getWithNamaMapel($ujianId);
        if (!$ujian) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $bankSoal = $this->bankSoalModel->find($ujian['bank_soal_id']);

        // Otorisasi
        if (has_role('guru') && $bankSoal['created_by'] !== user_id()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filterKelas = $this->request->getGet('kelas');

        $data = [
            'title'       => 'Cetak Analisis Butir Soal',
            'setting'     => $this->appSetting(),
            'ujian'       => $ujian,
            'bankSoal'    => $bankSoal,
            'ujianId'     => $ujianId,
            'filterKelas' => $filterKelas,
        ];

        return view('Panel/AnalisisButir/analisis_print', $data);
    }
}
