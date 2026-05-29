<?php

namespace App\Controllers;

use App\Models\ExambroSettingModel;

class ExambroSettingController extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new ExambroSettingModel();
    }

    public function index($id = 1)
    {
        $setting = $this->appSetting();
        return view('Panel/Exambro/setting_view', [
            'setting' => $setting,
            'title'   => 'Pengaturan Exambro'
        ]);
    }

    public function informasi()
    {
        $setting = $this->appSetting();
        return view('Panel/Exambro/informasi_view', [
            'setting' => $setting,
            'title'   => 'Halaman Informasi'
        ]);
    }

    public function getData($id = 1)
    {
        $setting = $this->settingsModel->getExambroSetting($id);
        return $this->response->setJSON($setting);
    }

    public function update($id = 1)
    {
        try {
            $allowed = [
                'logo_resource',
                'banner_img',
                'default_brightness',
                'bell_sound',
                'exit_sound',
                'app_volume',
                'school_name',
                'app_name',
                'version',
                'password_exit',
                'secret_code',
                'menu_url',
                'menu_scanqr',
                'bluetooth',
                'theme_color',
                'restrict_user_agent',
                'portal_ujian',
                'login_nopassword',
                'user_agent',
                'informasi',
                'headset'
            ];

            $input = $this->request->getPost() ?? [];
            $data  = array_intersect_key($input, array_flip($allowed));

            // Pastikan nilai boolean dikonversi dengan benar
            foreach (['menu_url', 'menu_scanqr', 'bluetooth', 'headset', 'restrict_user_agent', 'portal_ujian', 'login_nopassword'] as $booleanField) {
                $data[$booleanField] = $this->request->getPost($booleanField) ? 1 : 0;
            }

            // Upload file jika ada
            $this->handleImageUpload($data, 'logo_resource', 'assets/img');
            $this->handleImageUpload($data, 'banner_img', 'assets/img');
            $this->handleSoundUpload($data, 'bell_sound', 'assets/sound');
            $this->handleSoundUpload($data, 'exit_sound', 'assets/sound');

            $data['updated_at'] = date('Y-m-d H:i:s');

            if (!empty($data) && $this->settingsModel->updateExambroSetting($id, $data)) {
                service('cache')->delete('exambro_setting');
                return $this->response->setJSON(['success' => true, 'message' => 'Pengaturan berhasil diperbarui.']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada perubahan yang disimpan.']);
        } catch (\Throwable $e) {
            // Tangkap error supaya tidak langsung 500
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    private function handleImageUpload(&$data, $fieldName, $uploadPath, $fileNameOverride = null)
    {
        $file = $this->request->getFile($fieldName);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mime = $file->getClientMimeType();
            if (in_array($mime, ['image/png', 'image/jpeg'])) {
                if ($file->getSize() <= 2 * 1024 * 1024) {
                    $newFileName = $fileNameOverride
                        ? $fileNameOverride . '.' . $file->getExtension()
                        : $file->getRandomName();

                    $path = FCPATH . $uploadPath;
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                    $file->move($path, $newFileName, true);
                    $data[$fieldName] = $newFileName;
                }
            }
        }
    }

    private function handleSoundUpload(&$data, $fieldName, $uploadPath, $fileNameOverride = null)
    {
        $file = $this->request->getFile($fieldName);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mime = $file->getClientMimeType();
            if (in_array($mime, ['audio/mpeg', 'audio/wav'])) {
                if ($file->getSize() <= 1 * 1024 * 1024) {
                    $newFileName = $fileNameOverride
                        ? $fileNameOverride . '.' . $file->getExtension()
                        : $file->getRandomName();

                    $path = FCPATH . $uploadPath;
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                    $file->move($path, $newFileName, true);
                    $data[$fieldName] = $newFileName;
                }
            }
        }
    }

    public function downloadConfig($id = 1)
    {
        try {
            // Ambil konfigurasi lengkap termasuk blocked apps & menus
            $configData = $this->settingsModel->getFullConfigWithMenuAndBlock($id);

            if (!$configData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data konfigurasi tidak ditemukan.'
                ]);
            }

            // Encode JSON dalam format rapi
            $jsonData = json_encode($configData, JSON_PRETTY_PRINT);

            // Nama file konfigurasi
            $fileName = 'exam_config.candy';

            return $this->response
                ->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setHeader('Content-Length', strlen($jsonData))
                ->setBody($jsonData);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
