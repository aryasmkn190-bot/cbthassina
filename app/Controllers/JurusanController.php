<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JurusanModel;
use Ramsey\Uuid\Uuid;

class JurusanController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new JurusanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $data = [
            'setting' => $setting,
            'title' => 'Manajemen Jurusan',
        ];
        return view('Panel/Jurusan/jurusan_view', $data);
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama' => 'required|max_length[100]|is_unique[jurusan.nama]',
                'is_active' => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'         => Uuid::uuid4()->toString(),
                'nama'       => $this->request->getPost('nama'),
                'is_active'  => $this->request->getPost('is_active'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil ditambahkan.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama' => "required|max_length[100]|is_unique[jurusan.nama,id,{$id}]",
                'is_active' => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'nama'       => $this->request->getPost('nama'),
                'is_active'  => $this->request->getPost('is_active'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }
}
