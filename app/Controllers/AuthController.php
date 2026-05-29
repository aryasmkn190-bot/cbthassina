<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function login()
    {
        $setting = $this->appSetting();
        $data = [
            'setting' => $setting,
            'title' => 'Login Panel',
        ];
        return view('Auth/login', $data); // Menampilkan halaman login
    }

    public function doLogin()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Hanya bisa diakses via AJAX.']);
        }

        $identity = $this->request->getPost('identity');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->getByIdentity($identity);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email/Username atau password salah.',
            ]);
        }

        if (!$user['is_active']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akun kamu belum aktif.',
            ]);
        }

        session()->set('user', [
            'id'       => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email'    => $user['email'],
            'role'     => $user['roles'],
        ]);

        // Regenerate session ID for security
        session()->regenerate();

        // Save session ID to Redis for concurrent session check
        try {
            $sessionID = session_id();
            service('cache')->save("active_session:{$user['id']}", $sessionID, 86400);
        } catch (\Throwable $e) {
            log_message('error', 'Redis save active_session failed: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'redirect' => base_url('panel/home')
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/panel/login');
    }
}
