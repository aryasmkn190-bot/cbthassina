<?php

namespace App\Controllers;


use CodeIgniter\HTTP\Files\UploadedFile;
use ZipArchive;

class PatchApp extends BaseController
{
    // Lokasi folder aplikasi CI4
    private $appFolder;

    // Direktori sementara untuk ekstraksi patch
    private $tempDir;

    public function __construct()
    {
        // Menentukan lokasi folder aplikasi CI4
        $this->appFolder = APPPATH;

        // Membuat direktori sementara jika belum ada
        $this->tempDir = WRITEPATH . 'patches/';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function index()
    {
        $setting = $this->appSetting();
        $data = [
            // 'nama_user' => $user->nama,
            'setting' => $setting,
            'title' => 'Patch Aplikasi',
        ];
        return view('Panel/Settings/patchapp', $data);
    }

    public function update()
    {
        $response = [
            'step' => '',
            'success' => false,
            'message' => '',
            'details' => []
        ];

        // 1. Cek file patch
        $patchZip = request()->getFile('patch_zip');
        if (!$patchZip || !$patchZip->isValid()) {
            $response['step'] = 'cek_file';
            $response['message'] = 'File patch tidak ditemukan atau rusak.';
            return $this->response->setJSON($response);
        }
        $response['details'][] = 'File diterima: ' . $patchZip->getName();
        $response['details'][] = 'MIME type: ' . $patchZip->getClientMimeType();

        // 2. Validasi
        $rules = [
            // MIME type fleksibel untuk ZIP
            'patch_zip' => 'uploaded[patch_zip]|max_size[patch_zip,10240]|mime_in[patch_zip,application/zip,application/x-zip,application/x-zip-compressed,application/octet-stream]'
        ];

        if (!$this->validate($rules)) {
            $response['step'] = 'validasi';
            $response['message'] = 'Validasi file gagal.';
            $response['details'][] = $this->validator->getErrors();
            return $this->response->setJSON($response);
        }
        $response['details'][] = 'Validasi berhasil';

        // 3. Ekstrak ZIP
        $tempDir = $this->tempDir . '/' . uniqid('patch_');
        $zip = new \ZipArchive();
        if ($zip->open($patchZip->getTempName()) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            $response['details'][] = 'ZIP berhasil diekstrak: ' . $tempDir;
        } else {
            $response['step'] = 'ekstrak_zip';
            $response['message'] = 'Gagal membuka ZIP patch.';
            return $this->response->setJSON($response);
        }

        // 4. Replace files
        try {
            $this->replaceFiles($tempDir);
            $response['details'][] = 'File berhasil direplace';
        } catch (\Exception $e) {
            $response['step'] = 'replace_file';
            $response['message'] = 'Gagal mereplace file: ' . $e->getMessage();
            return $this->response->setJSON($response);
        }

        // 5. Update database
        $dbResult = $this->updateDatabase($tempDir);
        $response['details'][] = 'Database update result: ' . json_encode($dbResult);

        // 6. Hapus temporary
        $this->deleteDirectory($tempDir);
        $response['details'][] = 'Temporary folder dihapus';

        $response['step'] = 'selesai';
        $response['success'] = $dbResult['success'];
        $response['message'] = $dbResult['message'];

        return $this->response->setJSON($response);
    }
    private function updateDatabase($sourceDir)
    {
        // Cari file SQL di dalam direktori sumber
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getExtension() === 'sql') {
                // Baca isi file SQL
                $sql = file_get_contents($item->getPathname());

                // Pisahkan file SQL menjadi beberapa pernyataan individu
                $statements = explode(';', $sql);

                // Jalankan setiap pernyataan SQL untuk memperbarui struktur database
                $db = db_connect();
                $db->transBegin();
                $allSuccess = true;

                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $success = $db->query($statement);
                        if (!$success) {
                            $allSuccess = false;
                            $errorInfo = $db->error(); // Dapatkan pesan kesalahan SQL
                            $db->transRollback();
                            $db->close();
                            return ['success' => false, 'message' => 'Gagal memperbarui database: ' . $errorInfo['message']];
                        }
                    }
                }

                if ($allSuccess) {
                    $db->transCommit();
                    $db->close();
                    return ['success' => true, 'message' => 'Berhasil memperbarui database.']; // Berhasil memperbarui database
                } else {
                    $db->transRollback();
                    $db->close();
                    return ['success' => false, 'message' => 'Gagal memperbarui database.'];
                }
            }
        }
        return json_encode(['success' => false, 'message' => 'Tidak ditemukan file SQL.']);
    }


    private function replaceFiles($sourceDir)
    {
        $sourceDir = rtrim($sourceDir, '/');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            // Mendapatkan sub-path manual
            $subPath = substr($item->getPathname(), strlen($sourceDir) + 1);

            $targetPath = $this->appFolder . DIRECTORY_SEPARATOR . $subPath;
            if ($item->isDir()) {
                @mkdir($targetPath);
            } else {
                copy($item, $targetPath);
            }
        }
    }


    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
