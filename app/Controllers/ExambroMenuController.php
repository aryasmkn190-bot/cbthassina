<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExambroMenuModel;

class ExambroMenuController extends BaseController
{
    protected $menuModel;
    protected $validation;

    public function __construct()
    {
        $this->menuModel = new ExambroMenuModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $data = [
            'setting' => $setting,
            'title' => 'Exambro Menu'
        ];
        return view('Panel/Exambro/menu_view', $data);
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->menuModel->orderBy('order', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'title'      => 'required|min_length[3]|max_length[100]',
                'link'       => 'required|valid_url',
                'icon'       => 'permit_empty|max_length[50]',
                'is_active'  => 'required|in_list[0,1]',
                'order'      => 'required|integer',
                'token'      => 'permit_empty|max_length[50]',
                'is_token'   => 'permit_empty|in_list[0,1]',
                'tgl_dibuka' => 'permit_empty',
                'tgl_ditutup' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }
            $tgl_dibuka = $this->request->getPost('tgl_dibuka');
            $tgl_ditutup = $this->request->getPost('tgl_ditutup');
            $data = [
                'title'      => $this->request->getPost('title'),
                'link'       => $this->request->getPost('link'),
                'icon'       => $this->request->getPost('icon') ?? null,
                'is_active'  => (int)$this->request->getPost('is_active'),
                'order'      => (int)$this->request->getPost('order'),
                'token'      => $this->request->getPost('token') ?? null,
                'is_token'   => (int)$this->request->getPost('is_token') ?? 0,
                'tgl_dibuka'  => $tgl_dibuka ? date('Y-m-d H:i:s', strtotime($tgl_dibuka)) : null,
                'tgl_ditutup' => $tgl_ditutup ? date('Y-m-d H:i:s', strtotime($tgl_ditutup)) : null,
                'created_at' => date('Y-m-d H:i:s'),

            ];

            $this->menuModel->insert($data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Menu berhasil ditambahkan.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'title'      => 'required|min_length[3]|max_length[100]',
                'link'       => 'required|valid_url',
                'icon'       => 'permit_empty|max_length[50]',
                'is_active'  => 'required|in_list[0,1]',
                'order'      => 'required|integer',
                'token'      => 'permit_empty|max_length[50]',
                'is_token'   => 'permit_empty|in_list[0,1]',
                'tgl_dibuka' => 'permit_empty',
                'tgl_ditutup' => 'permit_empty'
            ];


            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }
            $tgl_dibuka = $this->request->getPost('tgl_dibuka');
            $tgl_ditutup = $this->request->getPost('tgl_ditutup');
            $data = [
                'title'      => $this->request->getPost('title'),
                'link'       => $this->request->getPost('link'),
                'icon'       => $this->request->getPost('icon') ?? null,
                'is_active'  => (int)$this->request->getPost('is_active'),
                'order'      => (int)$this->request->getPost('order'),
                'token'      => $this->request->getPost('token') ?? null,
                'is_token'   => (int)$this->request->getPost('is_token') ?? 0,
                'tgl_dibuka'  => $tgl_dibuka ? date('Y-m-d H:i:s', strtotime($tgl_dibuka)) : null,
                'tgl_ditutup' => $tgl_ditutup ? date('Y-m-d H:i:s', strtotime($tgl_ditutup)) : null,
                'updated_at' => date('Y-m-d H:i:s'),

            ];

            $this->menuModel->update($id, $data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Menu berhasil diperbarui.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->menuModel->delete($id);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Menu berhasil dihapus.'
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
