<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExambroBlockModel;

class ExambroBlockController extends BaseController
{
    protected $blockModel;
    protected $validation;

    public function __construct()
    {
        $this->blockModel = new ExambroBlockModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $data = [

            'setting' => $setting,
            'title' => 'Exambro Blokir'
        ];
        return view('Panel/Exambro/block_view', $data); // siapkan view-nya nanti
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->blockModel->orderBy('id', 'DESC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'package_name' => 'required|min_length[3]|max_length[255]',
                'app_name'     => 'permit_empty|max_length[100]',
                'category'     => 'permit_empty|max_length[50]',
                'is_blocked'   => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'package_name' => $this->request->getPost('package_name'),
                'app_name'     => $this->request->getPost('app_name') ?? '',
                'category'     => $this->request->getPost('category') ?? '',
                'is_blocked'   => (int)$this->request->getPost('is_blocked'),
                'created_by'   => session('user_id') ?? null,
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            $this->blockModel->insert($data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data berhasil ditambahkan.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'package_name' => 'required|min_length[3]|max_length[255]',
                'app_name'     => 'permit_empty|max_length[100]',
                'category'     => 'permit_empty|max_length[50]',
                'is_blocked'   => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'package_name' => $this->request->getPost('package_name'),
                'app_name'     => $this->request->getPost('app_name') ?? '',
                'category'     => $this->request->getPost('category') ?? '',
                'is_blocked'   => (int)$this->request->getPost('is_blocked'),
                'updated_by'   => session('user_id') ?? null,
                'updated_at'   => date('Y-m-d H:i:s'),
            ];

            $this->blockModel->update($id, $data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data berhasil diperbarui.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->blockModel->delete($id);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON([
            'status'  => false,
            'message' => $message
        ]);
    }
}
