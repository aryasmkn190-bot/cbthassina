<?php

namespace App\Controllers\Akademik;

use App\Controllers\BaseController;
use App\Models\JadwalPelajaranModel;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class JadwalController extends BaseController
{
    protected $model;
    protected $kelasModel;
    protected $validation;

    public function __construct()
    {
        $this->model = new JadwalPelajaranModel();
        $this->kelasModel = new KelasModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $kelas = $this->kelasModel->getActiveSorted();
        $mapelModel = new \App\Models\MataPelajaranModel();
        $mapels = $mapelModel->getActiveSorted();
        $data = [
            'setting' => $setting,
            'title' => 'Jadwal Pelajaran',
            'kelas' => $kelas,
            'mapels' => $mapels,
        ];
        return view('Panel/Akademik/Jadwal/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->getWithKelas();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kelas_id'          => 'required',
                'mata_pelajaran_id' => 'required|min_length[36]|max_length[36]',
                'hari'              => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
                'waktu_mulai'       => 'required',
                'waktu_selesai'     => 'required',
                'guru_id'           => 'required|min_length[36]|max_length[36]',
                'ruangan'           => 'required|max_length[50]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $userModel = new \App\Models\UserModel();
            $teacher = $userModel->find($this->request->getPost('guru_id'));
            $guruNama = $teacher ? $teacher['full_name'] : '-';

            $this->model->insert([
                'id'                => Uuid::uuid4()->toString(),
                'kelas_id'          => $this->request->getPost('kelas_id'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'hari'              => $this->request->getPost('hari'),
                'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
                'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
                'guru_id'           => $this->request->getPost('guru_id'),
                'guru_nama'         => $guruNama,
                'ruangan'           => $this->request->getPost('ruangan'),
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Jadwal pelajaran berhasil ditambahkan.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kelas_id'          => 'required',
                'mata_pelajaran_id' => 'required|min_length[36]|max_length[36]',
                'hari'              => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
                'waktu_mulai'       => 'required',
                'waktu_selesai'     => 'required',
                'guru_id'           => 'required|min_length[36]|max_length[36]',
                'ruangan'           => 'required|max_length[50]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $userModel = new \App\Models\UserModel();
            $teacher = $userModel->find($this->request->getPost('guru_id'));
            $guruNama = $teacher ? $teacher['full_name'] : '-';

            $this->model->update($id, [
                'kelas_id'          => $this->request->getPost('kelas_id'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'hari'              => $this->request->getPost('hari'),
                'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
                'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
                'guru_id'           => $this->request->getPost('guru_id'),
                'guru_nama'         => $guruNama,
                'ruangan'           => $this->request->getPost('ruangan'),
                'updated_at'        => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Jadwal pelajaran berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Jadwal pelajaran berhasil dihapus.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function getTeachersBySubject($subjectId)
    {
        if ($this->request->isAJAX()) {
            $userModel = new \App\Models\UserModel();
            $teachers = $userModel->select('id, full_name')
                ->where('mata_pelajaran_id', $subjectId)
                ->groupStart()
                    ->like('roles', 'guru')
                    ->orLike('roles', 'wali_kelas')
                ->groupEnd()
                ->orderBy('full_name', 'ASC')
                ->findAll();

            return $this->response->setJSON(['status' => true, 'data' => $teachers]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }
}
