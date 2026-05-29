<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\PesertaModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UjianController extends BaseController
{
    protected $ujianModel;
    protected $pesertaModel;
    protected $validation;

    public function __construct()
    {
        $this->ujianModel = new UjianModel();
        $this->pesertaModel = new pesertaModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect(); // ✅ Tambahkan baris ini
    }

    public function index()
    {
        helper('auth'); // jika pakai helper session

        $setting        = $this->appSetting();
        $userId         = user_id();
        $role           = user_role();

        $tingkatModel   = new \App\Models\TingkatModel();
        $kelasModel     = new \App\Models\KelasModel();
        $jurusanModel   = new \App\Models\JurusanModel();
        $agamaModel     = new \App\Models\AgamaModel();
        $guruModel      = new \App\Models\UserModel();
        $bankSoalModel  = new \App\Models\BankSoalModel();
        $jenisUjianModel = new \App\Models\JenisUjianModel(); // ➕ Tambahan

        // Ambil data guru dan bank soal berdasarkan role
        if (has_role('admin')) {
            $guruList = $guruModel->where('roles', 'guru')->findAll();
            $bankList = $bankSoalModel->findAll();
        } else {
            $guruList = $guruModel->where(['id' => $userId, 'roles' => 'guru'])->findAll();
            $bankList = $bankSoalModel->where('created_by', $userId)->findAll();
        }

        $data = [
            'title'        => 'Manajemen Ujian',
            'setting'      => $setting,
            'gurus'        => $guruList,
            'banks'        => $bankList,
            'tingkat'      => $tingkatModel->where('is_active', 1)->findAll(),
            'kelas'        => $kelasModel->getActiveSorted(),
            'jurusan'      => $jurusanModel->where('is_active', 1)->findAll(),
            'agama'        => $agamaModel->where('is_active', 1)->findAll(),

            'jenisUjian'   => $jenisUjianModel->where('is_active', 1)->findAll(), // ➕ Tambahan
        ];

        return view('Panel/Ujian/ujian_view', $data);
    }




    public function list()
    {

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        if (!is_logged_in()) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $data = has_role('admin')
            ? $this->ujianModel->getAllUjian()
            : $this->ujianModel->getAllUjian(user_id());

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }


    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'bank_soal_id'   => 'required',
                'nama_ujian'     => 'required|min_length[3]',
                'kode_ujian'     => 'required|is_unique[ujian.kode_ujian]',
                'waktu_mulai'    => 'required|valid_date',
                'waktu_selesai'  => 'required|valid_date',
            ];

            $messages = [
                'kode_ujian' => [
                    'required'   => 'Kode ujian wajib diisi.',
                    'is_unique'  => 'Kode ujian sudah digunakan, silakan pilih kode lain.'
                ]
            ];

            if (!$this->validate($rules, $messages)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'id'                  => Uuid::uuid4()->toString(),
                'jenis_ujian_id'      => $this->request->getPost('jenis_ujian_id'), // ➕ Tambahan
                'bank_soal_id'        => $this->request->getPost('bank_soal_id'),
                'nama_ujian'          => $this->request->getPost('nama_ujian'),
                'deskripsi'           => $this->request->getPost('deskripsi'),
                'kode_ujian'          => $this->request->getPost('kode_ujian'),
                'token'               => $this->request->getPost('token'),
                'acak_soal'           => (int)$this->request->getPost('acak_soal'),
                'acak_opsi'           => (int)$this->request->getPost('acak_opsi'),
                'pakai_token'         => (int)$this->request->getPost('pakai_token'),
                'durasi_ujian'        => (int)$this->request->getPost('durasi_ujian'),
                'minimal_durasi'      => (int)$this->request->getPost('minimal_durasi'),
                'tampil_nilai'        => (int)$this->request->getPost('tampil_nilai'),
                'tampil_pembahasan'   => (int)$this->request->getPost('tampil_pembahasan'),
                'pakai_webcam'        => (int)$this->request->getPost('pakai_webcam'),
                'perangkat_terkunci'  => (int)$this->request->getPost('perangkat_terkunci'),
                'single_login'        => (int)$this->request->getPost('single_login'),
                'created_by'          => $this->request->getPost('created_by'),
                'waktu_mulai'         => $this->request->getPost('waktu_mulai'),
                'waktu_selesai'       => $this->request->getPost('waktu_selesai'),
                'is_active'           => (int)$this->request->getPost('is_active'),
                'created_at'          => date('Y-m-d H:i:s')
            ];

            $this->ujianModel->insert($data);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Ujian berhasil ditambahkan.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'jenis_ujian_id' => 'required', // ➕ Tambahan
                'bank_soal_id'   => 'required',
                'nama_ujian'     => 'required|min_length[3]',
                'kode_ujian' => "required|is_unique[ujian.kode_ujian,id,{$id}]",

                'waktu_mulai'    => 'required|valid_date',
                'waktu_selesai'  => 'required|valid_date',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'jenis_ujian_id'      => $this->request->getPost('jenis_ujian_id'), // ➕ Tambahan
                'bank_soal_id'        => $this->request->getPost('bank_soal_id'),
                'nama_ujian'          => $this->request->getPost('nama_ujian'),
                'deskripsi'           => $this->request->getPost('deskripsi'),
                'kode_ujian'          => $this->request->getPost('kode_ujian'),
                'token'               => $this->request->getPost('token'),
                'acak_soal'           => (int)$this->request->getPost('acak_soal'),
                'acak_opsi'           => (int)$this->request->getPost('acak_opsi'),
                'pakai_token'         => (int)$this->request->getPost('pakai_token'),
                'durasi_ujian'        => (int)$this->request->getPost('durasi_ujian'),
                'minimal_durasi'      => (int)$this->request->getPost('minimal_durasi'),
                'tampil_nilai'        => (int)$this->request->getPost('tampil_nilai'),
                'tampil_pembahasan'   => (int)$this->request->getPost('tampil_pembahasan'),
                'pakai_webcam'        => (int)$this->request->getPost('pakai_webcam'),
                'perangkat_terkunci'  => (int)$this->request->getPost('perangkat_terkunci'),
                'single_login'        => (int)$this->request->getPost('single_login'),
                'created_by'          => $this->request->getPost('created_by'),
                'waktu_mulai'         => $this->request->getPost('waktu_mulai'),
                'waktu_selesai'       => $this->request->getPost('waktu_selesai'),
                'is_active'           => (int)$this->request->getPost('is_active'),
                'updated_at'          => date('Y-m-d H:i:s')
            ];

            $this->ujianModel->update($id, $data);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Ujian berhasil diperbarui.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->ujianModel->delete($id);
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Ujian berhasil dihapus.'
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
    public function updateShare($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Akses tidak valid'
            ]);
        }

        $option = $this->request->getPost('option');
        $value  = $this->request->getPost('value');

        if (!$id || !$option) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // whitelist kolom yang boleh diubah
        $allowedOptions = ['butuh_login', 'dibagikan'];

        if (!in_array($option, $allowedOptions)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Opsi tidak valid'
            ]);
        }

        try {
            $this->ujianModel->update($id, [
                $option => (int) $value
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Opsi berhasil diperbarui',
                'data' => [
                    'id' => $id,
                    'option' => $option,
                    'value' => $value
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ]);
        }
    }
    private $db;
    //========================= PESERTA ============================
    public function filterPeserta($ujianId)
    {
        if ($this->request->isAJAX()) {
            $pesertaModel = new \App\Models\PesertaModel();

            $filters = $this->request->getGet();

            $peserta = $pesertaModel
                ->select('peserta.id, peserta.nama, peserta.nisn')
                ->whereNotIn('peserta.id', function ($builder) use ($ujianId) {
                    return $builder->select('peserta_id')
                        ->from('hasil_ujian')
                        ->where('ujian_id', $ujianId)
                        ->where('peserta_id IS NOT NULL'); // fix bug guest_id
                });

            if (!empty($filters['tingkat_id'])) {
                $peserta->where('tingkat_id', $filters['tingkat_id']);
            }
            if (!empty($filters['kelas_id'])) {
                $peserta->where('kelas_id', $filters['kelas_id']);
            }
            if (!empty($filters['jurusan_id'])) {
                $peserta->where('jurusan_id', $filters['jurusan_id']);
            }
            if (!empty($filters['agama_id'])) {
                $peserta->where('agama_id', $filters['agama_id']);
            }

            $result = $peserta->orderBy('nama')->findAll();

            return $this->response->setJSON([
                'status' => true,
                'data'   => $result,
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    public function pesertaUjian($ujianId)
    {
        if ($this->request->isAJAX()) {
            $builder = $this->db->table('hasil_ujian h');
            $builder->select("
            COALESCE(p.id, h.guest_id) AS id,
            CASE 
                WHEN p.id IS NOT NULL THEN p.nama
                ELSE CONCAT('Guest - ', LEFT(h.guest_id, 8)) 
            END AS nama,
            p.nisn
        ");
            $builder->join('peserta p', 'p.id = h.peserta_id', 'left'); // biar guest ikut
            $builder->where('h.ujian_id', $ujianId);
            $builder->orderBy('nama', 'ASC');

            $data = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'status' => true,
                'data'   => $data,
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }



    public function addPeserta()
    {
        if ($this->request->isAJAX()) {
            $ujianId = $this->request->getPost('ujian_id');
            $pesertaIds = $this->request->getPost('peserta_id');

            if (!$ujianId || empty($pesertaIds)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak lengkap.'
                ]);
            }

            $builder = $this->db->table('hasil_ujian');

            // Cek peserta yang sudah ada agar tidak insert duplikat
            $existing = $builder->select('peserta_id')->where('ujian_id', $ujianId)->get()->getResultArray();
            $existingIds = array_column($existing, 'peserta_id');

            $insertData = [];
            foreach ($pesertaIds as $id) {
                if (in_array($id, $existingIds)) continue;

                $insertData[] = [
                    'id'         => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                    'ujian_id'   => $ujianId,
                    'peserta_id' => $id,
                    'status'     => 'belum_mulai',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            if (!empty($insertData)) {
                $builder->insertBatch($insertData);
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Peserta berhasil ditambahkan.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function cekPesertaDiuji()
    {
        if ($this->request->isAJAX()) {
            $ujianId    = $this->request->getPost('ujian_id');
            $pesertaIds = $this->request->getPost('peserta_id'); // bisa id peserta atau guest

            if (!$ujianId || !is_array($pesertaIds)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak lengkap.'
                ]);
            }

            // Cek ke peserta_id
            $queryPeserta = $this->db->table('hasil_ujian')
                ->select('peserta_id')
                ->where('ujian_id', $ujianId)
                ->whereIn('peserta_id', $pesertaIds)
                ->get()
                ->getResultArray();

            $sudahAda = array_column($queryPeserta, 'peserta_id');

            // Cek juga ke guest_id
            $queryGuest = $this->db->table('hasil_ujian')
                ->select('guest_id')
                ->where('ujian_id', $ujianId)
                ->whereIn('guest_id', $pesertaIds)
                ->get()
                ->getResultArray();

            $sudahAdaGuest = array_column($queryGuest, 'guest_id');

            // Gabungkan
            $sudahAda = array_merge($sudahAda, $sudahAdaGuest);
            $belumAda = array_diff($pesertaIds, $sudahAda);

            return $this->response->setJSON([
                'status' => true,
                'data'   => [
                    'sudahAda' => array_values($sudahAda),
                    'belumAda' => array_values($belumAda)
                ]
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function removePeserta()
    {
        if ($this->request->isAJAX()) {
            $ujianId    = $this->request->getPost('ujian_id');
            $pesertaIds = $this->request->getPost('peserta_id');

            if (!$ujianId || !is_array($pesertaIds)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak lengkap.'
                ]);
            }

            // Hapus dari peserta_id
            $this->db->table('hasil_ujian')
                ->where('ujian_id', $ujianId)
                ->whereIn('peserta_id', $pesertaIds)
                ->delete();

            // Hapus dari guest_id
            $this->db->table('hasil_ujian')
                ->where('ujian_id', $ujianId)
                ->whereIn('guest_id', $pesertaIds)
                ->delete();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Peserta (termasuk guest) berhasil dihapus dari ujian beserta nilai-nilainya.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function updateMassal()
    {
        $post = $this->request->getJSON(true);

        // Validasi data
        if (empty($post['ujian_ids']) || !is_array($post['ujian_ids'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Tidak ada ujian yang dipilih.'
            ]);
        }

        // Susun data yang akan diupdate
        $dataUpdate = [];

        if (!empty($post['waktu_mulai'])) {
            $dataUpdate['waktu_mulai'] = $post['waktu_mulai'];
        }
        if (!empty($post['waktu_selesai'])) {
            $dataUpdate['waktu_selesai'] = $post['waktu_selesai'];
        }
        if (isset($post['token_aktif'])) {
            $dataUpdate['pakai_token'] = (int) $post['token_aktif'];
        }
        if (!empty($post['ubah_token'])) {
            if (empty($post['token'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Token tidak boleh kosong jika opsi Ubah Token aktif.'
                ]);
            }
            $tokenClean = strtoupper(trim($post['token']));
            if (!preg_match('/^[A-Z0-9]{3,10}$/', $tokenClean)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Token harus alfanumerik dan memiliki panjang 3-10 karakter.'
                ]);
            }
            $dataUpdate['token'] = $tokenClean;
        }

        if (empty($dataUpdate)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Tidak ada perubahan yang dikirim.'
            ]);
        }

        // Jalankan update massal
        try {
            $this->ujianModel
                ->whereIn('id', $post['ujian_ids'])
                ->set($dataUpdate)
                ->update();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan ujian berhasil diperbarui.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[updateMassal] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data ujian.'
            ]);
        }
    }

    public function exportBelumUjian()
    {
        $ids = $this->request->getGet('ids');
        if (!$ids) {
            return $this->response->setJSON(['error' => 'Parameter ujian tidak valid']);
        }

        $ujianIds = explode(',', $ids);

        $ujians = $this->ujianModel
            ->whereIn('id', $ujianIds)
            ->orderBy('nama_ujian', 'ASC')
            ->findAll();

        if (!$ujians) {
            return $this->response->setJSON(['error' => 'Data ujian tidak ditemukan']);
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // hapus default sheet

        $rekapAllData = []; // untuk sheet rekap semua

        foreach ($ujians as $ujian) {
    		$sheet = $spreadsheet->createSheet();
   				 // 1. Bersihkan karakter terlarang
		    $namaBersih = str_replace(['*', ':', '/', '\\', '?', '[', ']'], ' ', $ujian['nama_ujian']);
    			// 2. Potong maksimal 28-30 karakter (Excel max 31, kita ambil aman)
   			$sheet->setTitle(substr($namaBersih, 0, 28));

            $pesertaBelum = $this->pesertaModel
                ->select('peserta.id, peserta.nama, peserta.username, kelas.nama as kelas')
                ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
                ->join('hasil_ujian hu', "hu.peserta_id = peserta.id", 'inner')
                ->where('hu.ujian_id', $ujian['id'])
                ->where('hu.status', 'belum_mulai')
                ->orderBy('kelas.nama', 'ASC')
                ->orderBy('peserta.nama', 'ASC')
                ->findAll();

            // Tambahkan ke rekapAllData
            foreach ($pesertaBelum as $p) {
                $rekapAllData[] = [
                    'nama' => $p['nama'],
                    'username' => $p['username'],
                    'kelas' => $p['kelas'],
                    'nama_ujian' => $ujian['nama_ujian'],
                ];
            }

            // Header gaya elegan
            $sheet->mergeCells('A1:D1');
            $sheet->setCellValue('A1', 'Daftar Peserta Belum Ujian');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->mergeCells('A2:D2');
            $sheet->setCellValue('A2', $ujian['nama_ujian'] . ' (Token: ' . ($ujian['pakai_token'] == 1 ? $ujian['token'] : '-') . ')');
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '555555']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Header tabel
            $sheet->fromArray(['No', 'Nama Peserta', 'Kelas', 'Username'], null, 'A4');
            $sheet->getStyle('A4:D4')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '007BFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Isi data
            $row = 5;
            $no = 1;
            foreach ($pesertaBelum as $p) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $p['nama']);
                $sheet->setCellValue("C{$row}", $p['kelas']);
                $sheet->setCellValue("D{$row}", $p['username']);
                $row++;
            }

            // Styling border tabel
            $sheet->getStyle("A4:D" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]
                ],
            ]);

            // Auto width
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // 🔹 Tambahkan sheet rekap semua dengan kolom Kelas
        $sheetRekap = $spreadsheet->createSheet();
        $sheetRekap->setTitle('Rekap Semua Belum Ujian');

        // Header
        $sheetRekap->fromArray(['No', 'Nama Peserta', 'Username', 'Kelas', 'Nama Ujian'], null, 'A1');
        $sheetRekap->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '28a745']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($rekapAllData as $r) {
            $sheetRekap->setCellValue("A{$row}", $no++);
            $sheetRekap->setCellValue("B{$row}", $r['nama']);
            $sheetRekap->setCellValue("C{$row}", $r['username']);
            $sheetRekap->setCellValue("D{$row}", $r['kelas']);
            $sheetRekap->setCellValue("E{$row}", $r['nama_ujian']);
            $row++;
        }

        // Border
        $sheetRekap->getStyle("A1:E" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]
            ],
        ]);

        foreach (range('A', 'E') as $col) {
            $sheetRekap->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Jadikan sheet rekap sebagai active sheet
        $spreadsheet->setActiveSheetIndexByName('Rekap Semua Belum Ujian');

        // Output file
        $filename = 'Peserta_Belum_Ujian_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
