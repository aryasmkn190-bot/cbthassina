<?php

namespace App\Controllers\Api;

use App\Models\SettingsModel;
use App\Models\PesertaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthApiController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        if (!$this->request->is('post')) {
            return $this->fail('Hanya menerima POST request.', ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        $identity = $this->request->getPost('identity');
        $password = $this->request->getPost('password');

        if (empty($identity) || empty($password)) {
            return $this->failValidationErrors('Kolom tidak boleh kosong.');
        }

        $pesertaModel = new PesertaModel();
        $user = $pesertaModel->getByIdentity($identity);

        if (!$user) {
            return $this->failUnauthorized('Email/Username atau password salah.');
        }
        $settingModel = new SettingsModel();
        $setting = $settingModel->get_by_id(1);
        $secretKey = $setting->key_encrypt ?? null;

        if (!$secretKey) {
            return $this->failServerError('Kunci rahasia tidak tersedia di pengaturan.');
        }
        // Verifikasi password dengan customDecrypt
        $decryptedPassword = customDecrypt($user['password'], $secretKey);
        if ($decryptedPassword === false) {
            return $this->failServerError('Gagal mendekripsi password. Cek key atau format.');
        }
        if ($decryptedPassword !== $password) {
            return $this->failUnauthorized('password salah.');
        }

        if (!$user['is_active']) {
            return $this->failForbidden('Akun kamu belum aktif.');
        }

        // Generate token
        $token = bin2hex(random_bytes(32)); // 64 karakter acak
        // Simpan token ke database
        $pesertaModel->update($user['id'], ['api_token' => $token]);

        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'nama'     => $user['nama'],
                'kelas'    => $user['nama_kelas'],
                'jurusan'  => $user['nama_jurusan'],
                'token'    => $token,
            ]
        ]);
    }
}
