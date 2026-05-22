<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Controllers\BaseController;
use App\Models\PesertaModel;
use App\Models\TingkatModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\AgamaModel;
use Ramsey\Uuid\Uuid;

class PesertaController extends BaseController
{
    protected $pesertaModel;
    protected $validation;

    public function __construct()
    {
        $this->pesertaModel = new PesertaModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'title'    => 'Manajemen Peserta',
            'setting'  => $this->appSetting(),
            'tingkat'  => (new TingkatModel())->where('is_active', 1)->findAll(),
            'kelas'    => (new KelasModel())->where('is_active', 1)->findAll(),
            'jurusan'  => (new JurusanModel())->where('is_active', 1)->findAll(),
            'agama'    => (new AgamaModel())->where('is_active', 1)->findAll(),
        ];

        return view('Panel/Peserta/peserta_view', $data);
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->pesertaModel->getWithRelations();

            // olah dulu password jadi plaintext hasil decrypt
            foreach ($data as &$row) {
                $row['password'] = customDecrypt($row['password'], $this->appSetting()->key_encrypt);
            }

            return $this->response->setJSON([
                'status' => true,
                'data'   => $data
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama'        => 'required|min_length[3]',
                'nisn'        => 'required|numeric',
                'tingkat_id'  => 'required',
                'kelas_id'    => 'required',
                'jurusan_id'  => 'required',
                'agama_id'    => 'required',
                'username'    => 'required|is_unique[peserta.username]',
                'password'    => 'required|min_length[4]',
                'is_active'   => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $data = [
                'id'         => Uuid::uuid4()->toString(),
                'nama'       => $this->request->getPost('nama'),
                'nisn'       => $this->request->getPost('nisn'),
                'tingkat_id' => $this->request->getPost('tingkat_id'),
                'kelas_id'   => $this->request->getPost('kelas_id'),
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'agama_id'   => $this->request->getPost('agama_id'),
                'username'   => $this->request->getPost('username'),
                'password'   => customEncrypt($this->request->getPost('password'), $this->appSetting()->key_encrypt),
                'is_active'  => (int)$this->request->getPost('is_active'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->pesertaModel->insert($data);
            return $this->response->setJSON(['status' => true, 'message' => 'Peserta berhasil ditambahkan.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama'        => 'required|min_length[3]',
                'nisn'        => 'required|numeric',
                'tingkat_id'  => 'required',
                'kelas_id'    => 'required',
                'jurusan_id'  => 'required',
                'agama_id'    => 'required',
                'username'    => 'required',
                'is_active'   => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $data = [
                'nama'       => $this->request->getPost('nama'),
                'nisn'       => $this->request->getPost('nisn'),
                'tingkat_id' => $this->request->getPost('tingkat_id'),
                'kelas_id'   => $this->request->getPost('kelas_id'),
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'agama_id'   => $this->request->getPost('agama_id'),
                'username'   => $this->request->getPost('username'),
                'is_active'  => (int)$this->request->getPost('is_active'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->request->getPost('password')) {
                $data['password'] =  customEncrypt($this->request->getPost('password'), $this->appSetting()->key_encrypt);
            }

            $this->pesertaModel->update($id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Peserta berhasil diperbarui.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->pesertaModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Peserta berhasil dihapus.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }
    public function downloadTemplateUpdatePassword()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NISN');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Password Baru');

        // Ambil data peserta + join kelas
        $pesertaList = $this->pesertaModel
            ->select('peserta.nisn, peserta.nama, kelas.nama as kelas_nama')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->orderBy('kelas.nama', 'ASC')
            ->findAll();

        $rowNumber = 2;
        foreach ($pesertaList as $index => $p) {
            $sheet->setCellValue("A{$rowNumber}", $index + 1);
            $sheet->setCellValue("B{$rowNumber}", $p['nisn']);
            $sheet->setCellValue("C{$rowNumber}", $p['nama']);
            $sheet->setCellValue("D{$rowNumber}", $p['kelas_nama']);
            $sheet->setCellValue("E{$rowNumber}", ''); // password kosong, diisi admin
            $rowNumber++;
        }

        // Auto width
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Update_Password_Peserta.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function updatePasswordByExcel()
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $rows = $spreadsheet->getActiveSheet()->toArray();

            $berhasil = 0;
            $gagal = [];

            foreach ($rows as $i => $row) {
                if ($i == 0) continue; // skip header

                $nisn     = trim($row[1] ?? ''); // kolom B
                $password = trim($row[4] ?? ''); // kolom E (Password Baru)

                if ($nisn && $password) {
                    $peserta = $this->pesertaModel->where('nisn', $nisn)->first();

                    if ($peserta) {
                        $this->pesertaModel->update($peserta['id'], [
                            'password'   => customEncrypt($password, $this->appSetting()->key_encrypt),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $berhasil++;
                    } else {
                        $gagal[] = [
                            'baris'  => $i + 1,
                            'alasan' => "NISN '$nisn' tidak ditemukan"
                        ];
                    }
                } else {
                    $gagal[] = [
                        'baris'  => $i + 1,
                        'alasan' => 'NISN atau Password kosong'
                    ];
                }
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => "Update password selesai. Berhasil: {$berhasil}",
                'gagal'   => $gagal
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()
            ]);
        }
    }



    public function import()
    {
        if ($this->request->isAJAX()) {
            $file = $this->request->getFile('file');

            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return $this->fail('File tidak valid atau sudah diproses.');
            }

            try {
                $spreadsheet = IOFactory::load($file->getTempName());
                $sheet = $spreadsheet->getActiveSheet()->toArray();

                if (count($sheet) <= 1) {
                    return $this->fail('File kosong atau tidak memiliki data.');
                }

                array_shift($sheet); // hapus header

                $tingkatModel = new TingkatModel();
                $kelasModel   = new KelasModel();
                $jurusanModel = new JurusanModel();
                $agamaModel   = new AgamaModel();

                $inserted = 0;
                $failed = [];

                foreach ($sheet as $i => $row) {
                    if (count($row) < 10 || trim($row[1]) === '') {
                        $failed[] = ['baris' => $i + 2, 'alasan' => 'Data tidak lengkap atau nama kosong'];
                        continue;
                    }

                    list($no, $nama, $nisn, $username, $password, $tingkat, $kelas, $jurusan, $agama, $is_active) = $row;

                    // Cek duplikat username
                    if ($this->pesertaModel->where('username', $username)->first()) {
                        $failed[] = ['baris' => $i + 2, 'alasan' => "Username '$username' sudah digunakan"];
                        continue;
                    }

                    $tingkatId = $this->findOrInsert($tingkatModel, $tingkat);
                    $kelasId   = $this->findOrInsert($kelasModel, $kelas);
                    $jurusanId = $this->findOrInsert($jurusanModel, $jurusan);
                    $agamaId   = $this->findOrInsert($agamaModel, $agama);

                    $this->pesertaModel->insert([
                        'id'         => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                        'nama'       => $nama,
                        'nisn'       => $nisn,
                        'username'   => $username,
                        'password'   => customEncrypt($password, $this->appSetting()->key_encrypt),
                        'tingkat_id' => $tingkatId,
                        'kelas_id'   => $kelasId,
                        'jurusan_id' => $jurusanId,
                        'agama_id'   => $agamaId,
                        'is_active'  => (int)$is_active,
                    ]);

                    $inserted++;
                }

                return $this->response->setJSON([
                    'status' => true,
                    'message' => "$inserted peserta berhasil diimpor.",
                    'gagal' => $failed
                ]);
            } catch (\Throwable $e) {
                return $this->fail('Terjadi kesalahan saat membaca file: ' . $e->getMessage());
            }
        }

        return $this->fail('Request bukan AJAX.');
    }

    private function findOrInsert($model, $nama)
    {
        $data = $model->where('nama', $nama)->first();
        if ($data) return $data['id'];

        $id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $model->insert(['id' => $id, 'nama' => $nama, 'is_active' => 1]);
        return $id;
    }

    public function downloadExcel()
    {
        // Ambil semua data peserta beserta relasinya
        $pesertaList = $this->pesertaModel
            ->select('
            peserta.nisn,
            peserta.nama,
            peserta.username,
            peserta.password,
            tingkat.nama AS tingkat,
            kelas.nama AS kelas,
            jurusan.nama AS jurusan,
            agama.nama AS agama,
            peserta.is_active
        ')
            ->join('tingkat', 'tingkat.id = peserta.tingkat_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = peserta.jurusan_id', 'left')
            ->join('agama', 'agama.id = peserta.agama_id', 'left')
            ->orderBy('kelas.nama', 'ASC')
            ->orderBy('peserta.nama', 'ASC')
            ->findAll();

        // Buat spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'A1' => 'No',
            'B1' => 'NISN',
            'C1' => 'Nama',
            'D1' => 'Username',
            'E1' => 'Password',
            'F1' => 'Tingkat',
            'G1' => 'Kelas',
            'H1' => 'Jurusan',
            'I1' => 'Agama',
            'J1' => 'Status Aktif'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Isi data
        $rowNum = 2;
        foreach ($pesertaList as $index => $p) {
            $sheet->setCellValue("A{$rowNum}", $index + 1);
            $sheet->setCellValue("B{$rowNum}", $p['nisn']);
            $sheet->setCellValue("C{$rowNum}", $p['nama']);
            $sheet->setCellValue("D{$rowNum}", $p['username']);
            $sheet->setCellValue("E{$rowNum}", customDecrypt($p['password'], $this->appSetting()->key_encrypt));
            $sheet->setCellValue("F{$rowNum}", $p['tingkat']);
            $sheet->setCellValue("G{$rowNum}", $p['kelas']);
            $sheet->setCellValue("H{$rowNum}", $p['jurusan']);
            $sheet->setCellValue("I{$rowNum}", $p['agama']);
            $sheet->setCellValue("J{$rowNum}", $p['is_active'] ? 'Aktif' : 'Nonaktif');
            $rowNum++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nama file
        $filename = 'Data_Peserta_' . date('Ymd_His') . '.xlsx';

        // Header HTTP untuk unduh file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
