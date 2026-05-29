<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PpdbModel;
use Ramsey\Uuid\Uuid;

class PpdbController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new PpdbModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();

        // Calculate statistics
        $stats = [
            'total'    => $this->model->countAllResults(),
            'menunggu' => $this->model->where('status', 'menunggu')->countAllResults(),
            'proses'   => $this->model->where('status', 'proses')->countAllResults(),
            'diterima' => $this->model->where('status', 'diterima')->countAllResults(),
            'ditolak'  => $this->model->where('status', 'ditolak')->countAllResults(),
        ];

        $data = [
            'setting' => $setting,
            'title'   => 'Penerimaan Peserta Didik Baru (PPDB)',
            'stats'   => $stats,
        ];

        return view('Panel/Ppdb/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->orderBy('created_at', 'DESC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama'         => 'required|max_length[255]',
                'nisn'         => 'required|numeric|max_length[20]',
                'email'        => 'permit_empty|valid_email|max_length[100]',
                'telepon'      => 'permit_empty|numeric|max_length[20]',
                'sekolah_asal' => 'permit_empty|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $data = [
                'nama'         => $this->request->getPost('nama'),
                'nisn'         => $this->request->getPost('nisn'),
                'email'        => $this->request->getPost('email') ?: null,
                'telepon'      => $this->request->getPost('telepon') ?: null,
                'sekolah_asal' => $this->request->getPost('sekolah_asal') ?: null,
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->model->update($id, $data);
                $msg = 'Pendaftar berhasil diperbarui.';
            } else {
                $nomorDaftar = 'PPDB-' . date('Ymd') . '-' . rand(100, 999);
                $data['id'] = Uuid::uuid4()->toString();
                $data['nomor_daftar'] = $nomorDaftar;
                $data['status'] = 'menunggu';
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->model->insert($data);
                $msg = 'Pendaftaran PPDB berhasil ditambahkan.';
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function updateStatus()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $status = $this->request->getPost('status');

            if (!$id || !in_array($status, ['menunggu', 'proses', 'diterima', 'ditolak'])) {
                return $this->response->setJSON(['status' => false, 'message' => 'Parameter tidak valid.']);
            }

            $this->model->update($id, [
                'status'     => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Status pendaftaran berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Pendaftar berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
