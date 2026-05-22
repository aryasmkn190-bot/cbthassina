<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ExambroSettingModel;
use CodeIgniter\API\ResponseTrait;

class ExambroController extends BaseController
{
    use ResponseTrait;

    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new ExambroSettingModel();
    }

    private function authenticate()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (strpos($authHeader, 'Bearer ') !== 0) {
            return $this->failUnauthorized('Header Authorization tidak valid.');
        }

        $token = substr($authHeader, 7);
        $setting = $this->settingsModel->getExambroSetting(1); // ID bisa disesuaikan

        if (!$setting || $token !== $setting['secret_code']) {
            return $this->failUnauthorized('Token salah atau tidak ditemukan.');
        }

        return true;
    }

    public function getSetting()
    {
        $auth = $this->authenticate();
        if ($auth !== true) return $auth;

        $setting = $this->settingsModel->getExambroSetting(1);


        return $this->respond([
            'success' => true,
            'data' => $setting
        ]);
    }
    public function getConfig()
    {
        $auth = $this->authenticate();
        if ($auth !== true) return $auth;

        $data = $this->settingsModel->getFullConfigWithMenuAndBlock(1);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Berhasil mengambil konfigurasi',
            'data' => $data
        ]);
    }




    public function updateSetting()
    {
        $auth = $this->authenticate();
        if ($auth !== true) return $auth;

        $data = $this->request->getJSON(true); // JSON input
        $success = $this->settingsModel->updateExambroSetting(1, $data);

        if ($success) {
            return $this->respond(['message' => 'Pengaturan berhasil diperbarui.']);
        } else {
            return $this->fail('Gagal memperbarui pengaturan atau tidak ada perubahan.');
        }
    }
}
