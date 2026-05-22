<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Seeding\Seeder; // tambahkan ini di atas jika belum
use Ramsey\Uuid\Uuid;

class InstallController extends BaseController
{
    public function index()
    {
        try {
            $db = \Config\Database::connect();
            $db->initialize();

            // Jika database bisa terkoneksi, langsung arahkan ke panel
            return redirect()->to(base_url('panel'));
        } catch (DatabaseException $e) {
            // Jika tidak bisa konek, tampilkan view instalasi
            return view('Install/install_db');
        }
    }

    public function saveDb()
    {
        $data = $this->request->getPost();

        $envPath = ROOTPATH . '.env';
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        $envContent = preg_replace('/^database\.default\.hostname\s*=.*$/m', 'database.default.hostname = ' . $data['hostname'], $envContent);
        $envContent = preg_replace('/^database\.default\.database\s*=.*$/m', 'database.default.database = ' . $data['database'], $envContent);
        $envContent = preg_replace('/^database\.default\.username\s*=.*$/m', 'database.default.username = ' . $data['username'], $envContent);
        $envContent = preg_replace('/^database\.default\.password\s*=.*$/m', 'database.default.password = ' . $data['password'], $envContent);

        // Jika baris tidak ada, tambahkan
        if (!str_contains($envContent, 'database.default.hostname')) {
            $envContent .= "\n\n";
            $envContent .= 'database.default.hostname = ' . $data['hostname'] . "\n";
            $envContent .= 'database.default.database = ' . $data['database'] . "\n";
            $envContent .= 'database.default.username = ' . $data['username'] . "\n";
            $envContent .= 'database.default.password = ' . $data['password'] . "\n";
        }

        // Tulis ulang file
        if (!write_file($envPath, $envContent)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Gagal menyimpan file .env.'
            ]);
        }

        // Cek koneksi DB setelah update
        try {
            $db = \Config\Database::connect();
            $db->initialize();
            $db->query("SELECT 1");

            return $this->response->setJSON([
                'success' => true,
                'message' => '✅ Database terkoneksi dengan baik.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '⚠️ File disimpan, tapi gagal konek DB: ' . $e->getMessage()
            ]);
        }
    }

    public function saveAdmin()
    {
        $nama = $this->request->getPost('nama');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$nama || !$email || !$password) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Semua field wajib diisi.'
            ]);
        }

        session()->set('admin_install', [
            'nama'     => $nama,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => '✅ Admin disimpan di session.'
        ]);
    }

    public function migrate()
    {
        try {
            // Jalankan migrasi
            $migrate = \Config\Services::migrations();
            $migrate->latest();

            // ✅ Perbaiki pemanggilan seeder
            $seeder = \Config\Database::seeder();  // GANTI: jangan pakai \Config\Services::seeder()
            $seeder->call('SettingsSeeder');
            $seeder->call('SettingsExambroSeeder');

            // Tambah akun admin dari session
            $admin = session()->get('admin_install');
            if ($admin) {
                $db = db_connect();
                $db->table('users')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'full_name'  => $admin['nama'],
                    'username'   => 'admin',
                    'email'      => $admin['email'],
                    'password'   => $admin['password'],
                    'roles'      => 'admin',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            session()->remove('admin_install');

            return $this->response->setJSON([
                'success' => true,
                'message' => '✅ Migrasi dan seeding berhasil.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Gagal migrasi: ' . $e->getMessage()
            ]);
        }
    }
}
