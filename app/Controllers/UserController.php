<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends BaseController
{
    protected $userModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting(); // opsional
        $mapelModel = new \App\Models\MataPelajaranModel();
        $mapels = $mapelModel->getActiveSorted();
        return view('Panel/User/user_view', [
            'setting' => $setting,
            'title'   => 'Manajemen Pengguna',
            'mapels'  => $mapels
        ]);
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->userModel
                ->select('users.*, mata_pelajaran.nama as mata_pelajaran_nama')
                ->join('mata_pelajaran', 'mata_pelajaran.id = users.mata_pelajaran_id', 'left')
                ->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'username'          => 'required|min_length[4]|max_length[50]|is_unique[users.username]',
                'email'             => 'required|valid_email|is_unique[users.email]',
                'full_name'         => 'permit_empty|max_length[100]',
                'password'          => 'required|min_length[6]',
                'roles'             => 'permit_empty',
                'mata_pelajaran_id' => 'permit_empty|max_length[36]',
                'is_active'         => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->userModel->insert([
                'id'                => Uuid::uuid4()->toString(),
                'username'          => $this->request->getPost('username'),
                'email'             => $this->request->getPost('email'),
                'full_name'         => $this->request->getPost('full_name'),
                'password'          => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'roles'             => $this->request->getPost('roles'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
                'is_active'         => $this->request->getPost('is_active'),
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Pengguna berhasil ditambahkan.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'username'          => "required|min_length[4]|max_length[50]|is_unique[users.username,id,{$id}]",
                'email'             => "required|valid_email|is_unique[users.email,id,{$id}]",
                'full_name'         => 'permit_empty|max_length[100]',
                'password'          => 'permit_empty|min_length[6]',
                'roles'             => 'permit_empty',
                'mata_pelajaran_id' => 'permit_empty|max_length[36]',
                'is_active'         => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $data = [
                'username'          => $this->request->getPost('username'),
                'email'             => $this->request->getPost('email'),
                'full_name'         => $this->request->getPost('full_name'),
                'roles'             => $this->request->getPost('roles'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
                'is_active'         => $this->request->getPost('is_active'),
                'updated_at'        => date('Y-m-d H:i:s')
            ];

            if ($this->request->getPost('password')) {
                $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Pengguna berhasil diperbarui.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->userModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Pengguna berhasil dihapus.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }

    public function importExcel()
    {
        if ($this->request->isAJAX()) {
            $file = $this->request->getFile('file_excel');
            if (!$file->isValid()) {
                return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid']);
            }

            // Baca file Excel
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $dataInsert   = [];
            $duplicateRows = [];

            foreach ($rows as $index => $row) {
                // Lewati header
                if ($index === 0) continue;

                $username = $row[0] ?? null;
                $email    = $row[1] ?? null;

                // Cek duplicate di database
                $existing = $this->userModel
                    ->where('username', $username)
                    ->orWhere('email', $email)
                    ->first();

                if ($existing) {
                    $duplicateRows[] = [
                        'row'      => $index + 1,
                        'username' => $username,
                        'email'    => $email
                    ];
                    continue; // lewati baris ini
                }

                $dataInsert[] = [
                    'id'        => Uuid::uuid4()->toString(),
                    'username'  => $username,
                    'email'     => $email,
                    'full_name' => $row[2] ?? null,
                    'password'  => password_hash($row[3] ?? '123456', PASSWORD_DEFAULT),
                    'roles'     => $row[4] ?? 'guru',
                    'is_active' => $row[5] ?? 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            // Insert data valid
            if (!empty($dataInsert)) {
                $this->userModel->insertBatch($dataInsert);
            }

            $response = [
                'status' => true,
                'message' => 'Import selesai',
                'inserted' => count($dataInsert),
                'duplicates' => $duplicateRows
            ];

            if (empty($dataInsert) && !empty($duplicateRows)) {
                $response['status'] = false;
                $response['message'] = 'Semua data duplikat, tidak ada yang diimport';
            }

            return $this->response->setJSON($response);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }
}
