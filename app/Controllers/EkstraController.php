<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EkstraModel;
use Ramsey\Uuid\Uuid;

class EkstraController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new EkstraModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();

        $data = [
            'setting' => $setting,
            'title'   => 'Kegiatan Ekstrakurikuler',
        ];

        return view('Panel/Ekstra/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->orderBy('nama_ekstra', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama_ekstra'  => 'required|max_length[100]',
                'pembina_nama' => 'required|max_length[100]',
                'jadwal_hari'  => 'required|max_length[50]',
                'waktu'        => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $data = [
                'nama_ekstra'  => $this->request->getPost('nama_ekstra'),
                'pembina_nama' => $this->request->getPost('pembina_nama'),
                'jadwal_hari'  => $this->request->getPost('jadwal_hari'),
                'waktu'        => $this->request->getPost('waktu'),
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->model->update($id, $data);
                $msg = 'Kegiatan ekstrakurikuler berhasil diperbarui.';
            } else {
                $data['id'] = Uuid::uuid4()->toString();
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->model->insert($data);
                $msg = 'Kegiatan ekstrakurikuler berhasil ditambahkan.';
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Kegiatan ekstrakurikuler berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
