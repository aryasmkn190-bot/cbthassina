<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('user');
        $peserta = $session->get('peserta');

        $uri = service('uri');
        $segment1 = $uri->getSegment(1); // contoh: 'panel' atau 'peserta'

        // Jika route akses panel tapi bukan admin
        if ($segment1 === 'panel' && !$user) {
            return redirect()->to(base_url('panel'));
        }

        // Jika route akses peserta tapi bukan peserta
        if ($segment1 === 'peserta' && !$peserta) {
            return redirect()->to(base_url('/'));
        }

        // Role-based access
        $allowedRoles = $arguments ?? [];
        $roleData = $user ?? $peserta;
        $userRoles = explode(',', $roleData['role'] ?? '');

        foreach ($allowedRoles as $role) {
            if (in_array(trim($role), $userRoles)) {
                return; // Akses diperbolehkan
            }
        }

        return redirect()->to('/unauthorized');
    }



    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
