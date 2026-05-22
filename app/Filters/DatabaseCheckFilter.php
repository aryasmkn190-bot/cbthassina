<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Throwable;

class DatabaseCheckFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = service('uri')->getSegment(1);

        // Abaikan jika sedang akses halaman install
        if (str_starts_with($uri, 'install')) {
            return;
        }

        try {
            $db = \Config\Database::connect();
            $db->initialize();
            $db->query('SELECT 1'); // Cek koneksi
        } catch (Throwable $e) {
            // ❌ Tidak bisa konek DB → tampilkan error ringan, bukan install
            echo view('errors/custom/db_error', ['error' => $e->getMessage()]);
            exit;
        }

        try {
            // ✅ DB tersambung → cek apakah table settings ada
            $setting = $db->table('settings')->getWhere(['id' => 1])->getRow();

            if (!$setting) {
                // ✅ Koneksi ok, tapi setting kosong → tampilkan instalasi awal
                echo view('Install/install_db');
                exit;
            }
        } catch (Throwable $e) {
            // ❌ Tabel settings tidak ditemukan
            echo view('Install/install_db');
            exit;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
