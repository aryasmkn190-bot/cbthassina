<?php

namespace App\Controllers\Akademik;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\PesertaModel;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class AbsensiController extends BaseController
{
    protected $model;
    protected $pesertaModel;
    protected $kelasModel;
    protected $validation;

    public function __construct()
    {
        $this->model = new AbsensiModel();
        $this->pesertaModel = new PesertaModel();
        $this->kelasModel = new KelasModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $kelas = $this->kelasModel->getActiveSorted();
        $data = [
            'setting' => $setting,
            'title' => 'Absensi QR Siswa',
            'kelas' => $kelas,
        ];
        return view('Panel/Akademik/Absensi/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $kelas_id = $this->request->getGet('kelas_id');
            $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
            $data = $this->model->getWithPesertaAndKelas($kelas_id, $tanggal);
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function qrSession($kelas_id)
    {
        $setting = $this->appSetting();
        $kelas = $this->kelasModel->find($kelas_id);
        if (!$kelas) {
            return redirect()->to(base_url('panel/akademik/absensi'))->with('error', 'Kelas tidak ditemukan.');
        }

        // Generate encrypted payload: kelas_id | YYYY-MM-DD
        $today = date('Y-m-d');
        $rawPayload = $kelas_id . '|' . $today;
        
        // Simple base64 + app key encryption
        $key = $setting->key_encrypt ?? 'cbthassina';
        $encryptedPayload = base64_encode($rawPayload . $key);

        $data = [
            'setting' => $setting,
            'title' => 'Sesi Absensi QR - ' . $kelas['nama'],
            'kelas' => $kelas,
            'encryptedPayload' => $encryptedPayload,
            'today' => $today
        ];
        return view('Panel/Akademik/Absensi/qr_session', $data);
    }

    public function livePolling($kelas_id)
    {
        if ($this->request->isAJAX()) {
            $today = date('Y-m-d');
            $data = $this->model->getWithPesertaAndKelas($kelas_id, $today);
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'peserta_id' => 'required',
                'tanggal'    => 'required',
                'waktu_scan' => 'required',
                'status'     => 'required|in_list[hadir,sakit,izin,alfa]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            // Check if record exists for this student on this date
            $peserta_id = $this->request->getPost('peserta_id');
            $tanggal = $this->request->getPost('tanggal');
            $existing = $this->model->where('peserta_id', $peserta_id)->where('tanggal', $tanggal)->first();

            if ($existing) {
                return $this->response->setJSON(['status' => false, 'message' => 'Siswa sudah memiliki catatan absensi pada tanggal tersebut.']);
            }

            $this->model->insert([
                'id'         => Uuid::uuid4()->toString(),
                'peserta_id' => $peserta_id,
                'tanggal'    => $tanggal,
                'waktu_scan' => $this->request->getPost('waktu_scan'),
                'status'     => $this->request->getPost('status'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Absensi berhasil ditambahkan.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'status' => 'required|in_list[hadir,sakit,izin,alfa]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'status'     => $this->request->getPost('status'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Absensi berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Catatan absensi berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function getPesertaByKelas($kelas_id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->pesertaModel->where('kelas_id', $kelas_id)->orderBy('nama', 'ASC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}

