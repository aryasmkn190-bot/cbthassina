<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesertaModel;
use App\Models\PengajuanEditSiswaModel;
use Ramsey\Uuid\Uuid;

class ProfilSiswaController extends BaseController
{
    protected $pesertaModel;
    protected $pengajuanModel;
    protected $validation;

    public function __construct()
    {
        $this->pesertaModel = new PesertaModel();
        $this->pengajuanModel = new PengajuanEditSiswaModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $pesertaSession = session()->get('peserta');

        if (!$pesertaSession || !isset($pesertaSession['id'])) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Fetch full student record with class
        $peserta = $this->pesertaModel->getByIdWithKelas($pesertaSession['id']);
        
        if (!$peserta) {
            return redirect()->to('auth/login')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Fetch pending change requests
        $pendingRequest = $this->pengajuanModel->getPendingRequest($pesertaSession['id']);

        $data = [
            'setting'        => $setting,
            'title'          => 'Profil Saya',
            'peserta'        => $peserta,
            'pendingRequest' => $pendingRequest
        ];

        return view('Siswa/profil', $data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
        }

        $pesertaSession = session()->get('peserta');
        if (!$pesertaSession) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        }

        $pesertaId = $pesertaSession['id'];
        $currentData = $this->pesertaModel->find($pesertaId);

        if (!$currentData) {
            return $this->response->setJSON(['status' => false, 'message' => 'Siswa tidak ditemukan.']);
        }

        // 1. Validate General Fields (Dapodik & Password)
        $rules = [
            'telepon' => 'permit_empty|max_length[20]',
            'email'   => 'permit_empty|valid_email|max_length[100]',
            'alamat'  => 'permit_empty',
            'password'=> 'permit_empty|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
        }

        // 2. Separate restricted and unrestricted fields
        $restrictedKeys = ['nama', 'nisn', 'nik', 'nis', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin'];
        
        $unrestrictedFields = [
            'telepon', 'email', 'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
            'jenis_tinggal', 'alat_transportasi',
            'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
            'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
            'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali'
        ];

        $updateData = [];
        
        // Populate unrestricted data
        foreach ($unrestrictedFields as $field) {
            $val = $this->request->getPost($field);
            // Handle null values
            $updateData[$field] = $val !== '' ? $val : null;
        }

        // Handle password change
        $passwordInput = $this->request->getPost('password');
        if (!empty($passwordInput)) {
            // Encode using base64 (since original cbthassina uses base64 password hash for simple mock authentication)
            $updateData['password'] = base64_encode($passwordInput . 'cbthassina'); 
        }

        // Save unrestricted changes immediately
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        $this->pesertaModel->update($pesertaId, $updateData);

        // 3. Process restricted fields for approval
        $hasRestrictedChanges = false;
        $dataLama = [];
        $dataBaru = [];

        // Check if there is already a pending request
        $pendingRequest = $this->pengajuanModel->getPendingRequest($pesertaId);

        foreach ($restrictedKeys as $field) {
            $proposedVal = $this->request->getPost($field);
            if ($proposedVal !== null && $proposedVal !== '') {
                $currentVal = $currentData[$field] ?? '';
                // Compare values
                if (trim($proposedVal) != trim($currentVal)) {
                    if ($pendingRequest) {
                        return $this->response->setJSON([
                            'status'  => true,
                            'message' => 'Data alamat/kontak telah diperbarui, tetapi perubahan data penting dibatalkan karena Anda memiliki pengajuan edit data yang masih menunggu persetujuan admin.'
                        ]);
                    }
                    $dataLama[$field] = $currentVal;
                    $dataBaru[$field] = $proposedVal;
                    $hasRestrictedChanges = true;
                }
            }
        }

        if ($hasRestrictedChanges) {
            // Insert request into queue
            $this->pengajuanModel->insert([
                'id'            => Uuid::uuid4()->toString(),
                'peserta_id'    => $pesertaId,
                'data_lama'     => json_encode($dataLama),
                'data_baru'     => json_encode($dataBaru),
                'status'        => 'menunggu',
                'catatan_admin' => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Profil berhasil diperbarui. Pengajuan perubahan data penting (Nama, NISN, NIK, dll.) telah dicatat dan menunggu verifikasi admin.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data profil berhasil diperbarui.'
        ]);
    }
}
