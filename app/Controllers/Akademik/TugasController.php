<?php

namespace App\Controllers\Akademik;

use App\Controllers\BaseController;
use App\Models\TugasModel;
use App\Models\TugasJawabanModel;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class TugasController extends BaseController
{
    protected $model;
    protected $jawabanModel;
    protected $kelasModel;
    protected $validation;

    public function __construct()
    {
        $this->model = new TugasModel();
        $this->jawabanModel = new TugasJawabanModel();
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
            'title' => 'Manajemen Tugas & PR',
            'kelas' => $kelas,
            'mapels' => $mapels,
        ];
        return view('Panel/Akademik/Tugas/index', $data);
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
                'judul'             => 'required|max_length[255]',
                'deskripsi'         => 'permit_empty',
                'tenggat_waktu'     => 'required',
                'mata_pelajaran_id' => 'required|min_length[36]|max_length[36]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'                => Uuid::uuid4()->toString(),
                'kelas_id'          => $this->request->getPost('kelas_id'),
                'judul'             => $this->request->getPost('judul'),
                'deskripsi'         => $this->request->getPost('deskripsi'),
                'tenggat_waktu'     => str_replace('T', ' ', $this->request->getPost('tenggat_waktu')),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Tugas berhasil ditambahkan.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kelas_id'          => 'required',
                'judul'             => 'required|max_length[255]',
                'deskripsi'         => 'permit_empty',
                'tenggat_waktu'     => 'required',
                'mata_pelajaran_id' => 'required|min_length[36]|max_length[36]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'kelas_id'          => $this->request->getPost('kelas_id'),
                'judul'             => $this->request->getPost('judul'),
                'deskripsi'         => $this->request->getPost('deskripsi'),
                'tenggat_waktu'     => str_replace('T', ' ', $this->request->getPost('tenggat_waktu')),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'updated_at'        => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Tugas berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Tugas berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function detail($id)
    {
        $setting = $this->appSetting();
        $tugas = $this->model->select('tugas.*, kelas.nama AS nama_kelas, mata_pelajaran.nama AS mata_pelajaran')
            ->join('kelas', 'kelas.id = tugas.kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id', 'left')
            ->where('tugas.id', $id)
            ->first();

        if (!$tugas) {
            return redirect()->to(base_url('panel/akademik/tugas'))->with('error', 'Tugas tidak ditemukan.');
        }

        $submissions = $this->jawabanModel->getSubmissions($id);

        $data = [
            'setting' => $setting,
            'title' => 'Detail & Pengumpulan Tugas',
            'tugas' => $tugas,
            'submissions' => $submissions,
        ];
        return view('Panel/Akademik/Tugas/detail', $data);
    }

    public function gradeSubmission($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'nilai' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'catatan_guru' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->jawabanModel->update($id, [
                'nilai' => $this->request->getPost('nilai'),
                'catatan_guru' => $this->request->getPost('catatan_guru'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Nilai dan catatan berhasil disimpan.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
