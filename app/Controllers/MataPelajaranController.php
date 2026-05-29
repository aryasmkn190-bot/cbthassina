<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MataPelajaranModel;
use Ramsey\Uuid\Uuid;

class MataPelajaranController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new MataPelajaranModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $data = [
            'setting' => $setting,
            'title' => 'Manajemen Mata Pelajaran',
        ];
        return view('Panel/MataPelajaran/index', $data);
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
                'kode' => 'required|max_length[50]|is_unique[mata_pelajaran.kode]',
                'nama' => 'required|max_length[100]',
                'is_active' => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'         => Uuid::uuid4()->toString(),
                'kode'       => strtoupper(trim($this->request->getPost('kode'))),
                'nama'       => trim($this->request->getPost('nama')),
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
                'kode' => "required|max_length[50]|is_unique[mata_pelajaran.kode,id,{$id}]",
                'nama' => 'required|max_length[100]',
                'is_active' => 'required|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'kode'       => strtoupper(trim($this->request->getPost('kode'))),
                'nama'       => trim($this->request->getPost('nama')),
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
            // Check if used in users (as teacher mapel)
            $db = \Config\Database::connect();
            $usedUser = $db->table('users')->where('mata_pelajaran_id', $id)->countAllResults();
            $usedBank = $db->table('bank_soal')->where('mata_pelajaran_id', $id)->countAllResults();
            $usedJadwal = $db->table('jadwal_pelajaran')->where('mata_pelajaran_id', $id)->countAllResults();
            $usedTugas = $db->table('tugas')->where('mata_pelajaran_id', $id)->countAllResults();
            $usedRapor = $db->table('rapor_nilai')->where('mata_pelajaran_id', $id)->countAllResults();

            if ($usedUser > 0 || $usedBank > 0 || $usedJadwal > 0 || $usedTugas > 0 || $usedRapor > 0) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Mata pelajaran tidak dapat dihapus karena sedang digunakan oleh data lain (Guru, Bank Soal, Jadwal, Tugas, atau Rapor Nilai).'
                ]);
            }

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
