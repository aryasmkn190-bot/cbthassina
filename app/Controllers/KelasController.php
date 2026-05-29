<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class KelasController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new KelasModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $userModel = new \App\Models\UserModel();
        $waliKelas = $userModel->like('roles', 'wali_kelas')->findAll();

        $data = [
            'setting'   => $setting,
            'title'     => 'Manajemen Kelas',
            'waliKelas' => $waliKelas,
        ];
        return view('Panel/Kelas/kelas_view', $data);
    }

    public function getAll()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->getSorted();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama'          => 'required|max_length[100]|is_unique[kelas.nama]',
                'is_active'     => 'required|in_list[0,1]',
                'wali_kelas_id' => 'permit_empty|max_length[36]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'            => Uuid::uuid4()->toString(),
                'nama'          => $this->request->getPost('nama'),
                'is_active'     => $this->request->getPost('is_active'),
                'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null,
                'created_at'    => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil ditambahkan.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nama'          => "required|max_length[100]|is_unique[kelas.nama,id,{$id}]",
                'is_active'     => 'required|in_list[0,1]',
                'wali_kelas_id' => 'permit_empty|max_length[36]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'nama'          => $this->request->getPost('nama'),
                'is_active'     => $this->request->getPost('is_active'),
                'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null,
                'updated_at'    => date('Y-m-d H:i:s')
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

    public function getStudents($kelasId)
    {
        if ($this->request->isAJAX()) {
            $pesertaModel = new \App\Models\PesertaModel();
            $data = $pesertaModel->where('kelas_id', $kelasId)->orderBy('nama', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function getEligibleStudents()
    {
        if ($this->request->isAJAX()) {
            $pesertaModel = new \App\Models\PesertaModel();
            $data = $pesertaModel->select('peserta.id, peserta.nama, peserta.nisn, kelas.nama AS kelas_nama')
                ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
                ->where('peserta.is_active', 1)
                ->orderBy('peserta.nama', 'ASC')
                ->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function addStudents()
    {
        if ($this->request->isAJAX()) {
            $kelasId = $this->request->getPost('kelas_id');
            $pesertaIds = $this->request->getPost('peserta_ids');

            if (!$kelasId || empty($pesertaIds)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap.']);
            }

            $pesertaModel = new \App\Models\PesertaModel();
            $pesertaModel->whereIn('id', $pesertaIds)->set(['kelas_id' => $kelasId, 'updated_at' => date('Y-m-d H:i:s')])->update();

            return $this->response->setJSON(['status' => true, 'message' => 'Siswa berhasil ditambahkan ke kelas.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function removeStudent()
    {
        if ($this->request->isAJAX()) {
            $pesertaId = $this->request->getPost('peserta_id');

            if (!$pesertaId) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap.']);
            }

            $pesertaModel = new \App\Models\PesertaModel();
            $pesertaModel->update($pesertaId, ['kelas_id' => null, 'updated_at' => date('Y-m-d H:i:s')]);

            return $this->response->setJSON(['status' => true, 'message' => 'Siswa berhasil dihapus dari kelas.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }
}
