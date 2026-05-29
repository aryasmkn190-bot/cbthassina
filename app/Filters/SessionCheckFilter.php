<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionCheckFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $currentSessionId = session_id();

        // 1. Check for admin/teacher user
        $user = $session->get('user');
        if ($user && isset($user['id'])) {
            try {
                $cache = service('cache');
                $cachedSessionId = $cache->get("active_session:{$user['id']}");
                if ($cachedSessionId && $cachedSessionId !== $currentSessionId) {
                    // Sesi mismatch (double login)
                    $session->destroy();

                    if ($request->isAJAX()) {
                        return service('response')->setJSON([
                            'status'  => 'error',
                            'message' => 'Sesi berakhir karena akun login di perangkat lain.'
                        ])->setStatusCode(401);
                    }

                    return redirect()->to(base_url('auth/panel/login'))->with('error', 'Akun Anda telah login di perangkat lain.');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Redis SessionCheckFilter admin failed: ' . $e->getMessage());
            }
        }

        // 2. Check for student/peserta
        $peserta = $session->get('peserta');
        if ($peserta && isset($peserta['id'])) {
            try {
                $cache = service('cache');
                $cachedSessionId = $cache->get("active_session_peserta:{$peserta['id']}");
                if ($cachedSessionId && $cachedSessionId !== $currentSessionId) {
                    // Sesi mismatch (double login)
                    $session->destroy();

                    if ($request->isAJAX()) {
                        return service('response')->setJSON([
                            'status'  => 'error',
                            'message' => 'Sesi berakhir karena akun login di perangkat lain.'
                        ])->setStatusCode(401);
                    }

                    return redirect()->to(base_url('auth/login'))->with('error', 'Sesi Anda telah berakhir karena akun ini login di perangkat lain.');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Redis SessionCheckFilter peserta failed: ' . $e->getMessage());
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
