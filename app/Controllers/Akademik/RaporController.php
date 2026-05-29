<?php

namespace App\Controllers\Akademik;

use App\Controllers\BaseController;
use App\Models\RaporNilaiModel;
use App\Models\PesertaModel;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class RaporController extends BaseController
{
    protected $model;
    protected $pesertaModel;
    protected $kelasModel;
    protected $validation;

    public function __construct()
    {
        $this->model = new RaporNilaiModel();
        $this->pesertaModel = new PesertaModel();
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
            'title' => 'Rapor Pelajar',
            'kelas' => $kelas,
            'mapels' => $mapels,
        ];
        return view('Panel/Akademik/Rapor/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $peserta_id = $this->request->getGet('peserta_id');
            $semester = $this->request->getGet('semester');
            $tahun_ajaran = $this->request->getGet('tahun_ajaran');

            $query = $this->model
                ->select('rapor_nilai.*, mata_pelajaran.nama as mata_pelajaran_nama')
                ->join('mata_pelajaran', 'mata_pelajaran.id = rapor_nilai.mata_pelajaran_id', 'left')
                ->where('peserta_id', $peserta_id);
            if ($semester) {
                $query->where('semester', $semester);
            }
            if ($tahun_ajaran) {
                $query->where('tahun_ajaran', $tahun_ajaran);
            }

            $data = $query->orderBy('mata_pelajaran.nama', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function save()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'peserta_id'        => 'required',
                'mata_pelajaran_id' => 'required|min_length[36]|max_length[36]',
                'nilai'             => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'semester'          => 'required|max_length[20]',
                'tahun_ajaran'      => 'required|max_length[20]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $nilai = (int) $this->request->getPost('nilai');
            
            // Automatic grade conversion
            $grade = 'E';
            if ($nilai >= 85) {
                $grade = 'A';
            } elseif ($nilai >= 75) {
                $grade = 'B';
            } elseif ($nilai >= 60) {
                $grade = 'C';
            } elseif ($nilai >= 45) {
                $grade = 'D';
            }

            $data = [
                'peserta_id'        => $this->request->getPost('peserta_id'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'nilai'             => $nilai,
                'grade'             => $grade,
                'semester'          => $this->request->getPost('semester'),
                'tahun_ajaran'      => $this->request->getPost('tahun_ajaran'),
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->model->update($id, $data);
                return $this->response->setJSON(['status' => true, 'message' => 'Nilai rapor berhasil diperbarui.']);
            } else {
                // Check duplicate mata_pelajaran for the same student, semester and school year
                $exists = $this->model
                    ->where('peserta_id', $data['peserta_id'])
                    ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
                    ->where('semester', $data['semester'])
                    ->where('tahun_ajaran', $data['tahun_ajaran'])
                    ->first();

                if ($exists) {
                    return $this->response->setJSON(['status' => false, 'message' => ['mata_pelajaran_id' => 'Nilai untuk mata pelajaran ini sudah diinput pada semester dan tahun ajaran tersebut.']]);
                }

                $data['id'] = Uuid::uuid4()->toString();
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->model->insert($data);
                return $this->response->setJSON(['status' => true, 'message' => 'Nilai rapor berhasil ditambahkan.']);
            }
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Nilai rapor berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
