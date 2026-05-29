<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventarisModel;
use Ramsey\Uuid\Uuid;

class InventarisController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new InventarisModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();

        $data = [
            'setting' => $setting,
            'title'   => 'Inventaris & Aset Sekolah',
        ];

        return view('Panel/Inventaris/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->orderBy('nama_barang', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama_barang' => 'required|max_length[255]',
                'kode_barang' => 'required|max_length[100]',
                'jumlah'      => 'required|integer|greater_than_equal_to[0]',
                'kondisi'     => 'required|in_list[baik,rusak]',
                'lokasi'      => 'permit_empty|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $data = [
                'nama_barang' => $this->request->getPost('nama_barang'),
                'kode_barang' => $this->request->getPost('kode_barang'),
                'jumlah'      => $this->request->getPost('jumlah'),
                'kondisi'     => $this->request->getPost('kondisi'),
                'lokasi'      => $this->request->getPost('lokasi') ?: null,
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->model->update($id, $data);
                $msg = 'Barang inventaris berhasil diperbarui.';
            } else {
                $data['id'] = Uuid::uuid4()->toString();
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->model->insert($data);
                $msg = 'Barang inventaris berhasil ditambahkan.';
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Barang inventaris berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
