<?php

namespace App\Controllers;

use App\Models\PesertaModel;
use CodeIgniter\Controller;
use App\Models\ExambroSettingModel;
use App\Models\ExambroMenuModel;

class AuthPesertaController extends BaseController
{


    public function login()
    {

        $exambroSettingModel = new ExambroSettingModel();
        $exambro = $exambroSettingModel->find(1);
        $menuModel = new ExambroMenuModel();
        $menus = $menuModel->getActiveMenus();
        // Validasi user agent
        if (!empty($exambro['restrict_user_agent']) && $exambro['restrict_user_agent']) {
            $allowedAgent = $exambro['user_agent'] ?? 'CandyCBTExam';
            $currentAgent = $this->request->getUserAgent();

            if (stripos($currentAgent, $allowedAgent) === false) {
                $setting = $this->appSetting();
                return view('errors/custom/blocked_user_agent', [
                    'setting' => $setting,
                    'exambro' => $exambro
                ]);
            }
        }
        if ($exambro['portal_ujian'] == 1) {
            return view('portal/index', [
                'menus' => $menus
            ]);
        }

        $setting = $this->appSetting();
        $exambrosetting = $this->exambroSetting();
        $data = [
            'exambroSetting' => $exambrosetting,
            'setting' => $setting,
            'title' => 'Login Peserta',
        ];
        return view('Auth/loginpeserta', $data);
    }


    public function doLogin()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        $identity      = $this->request->getPost('identity');
        $inputPassword = $this->request->getPost('password');

        $pesertaModel = new PesertaModel();
        $user = $pesertaModel->getByIdentity($identity);

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email/Username atau password salah.',
            ]);
        }

        // Ambil setting
        $appSetting     = $this->appSetting();
        $exambroSetting = $this->exambroSetting(); // pastikan return array, bukan object
        $secretKey      = $appSetting->key_encrypt ?? null;

        // Cek password hanya jika login_nopassword tidak aktif
        if (empty($exambroSetting['login_nopassword']) || !$exambroSetting['login_nopassword']) {
            $decryptedPassword = customDecrypt($user['password'], $secretKey);

            if ($inputPassword !== $decryptedPassword) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email/Username atau password salah.',
                ]);
            }
        }

        // Cek status aktif
        if (empty($user['is_active'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akun kamu belum aktif.',
            ]);
        }

        // Set session
        session()->set('peserta', [
            'id'       => $user['id'],
            'username' => $user['username'],
            'nama'     => $user['nama'],
            'role'     => 'peserta',
        ]);

        // Save session ID to Redis for concurrent session check
        try {
            $sessionID = session_id();
            service('cache')->save("active_session_peserta:{$user['id']}", $sessionID, 86400);
        } catch (\Throwable $e) {
            log_message('error', 'Redis save active_session_peserta failed: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => 'Login berhasil.',
            'redirect' => base_url('peserta/home')
        ]);
    }




    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}
