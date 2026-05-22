<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;

class SebConfigController extends BaseController
{
    public function index()
    {
        $setting = $this->appSetting();
        $model   = new \App\Models\ExambroSettingModel();
        $config  = $model->getExambroSetting(1); // ambil record ID=1

        $start_url     = base_url() ?? 'https://domainujian.com/start';
        $quit_password = $config['password_exit'] ?? '';
        $user_agent    = $config['user_agent'] ?? 'SEB';
        $file_exam     = $config['file_exam_config'] ?? null;
        $file_exam_upload     = $config['file_exam_config_upload'] ?? null;
        $qrCode        = null;
        $qrCodeUpload        = null;
        $fileUrl       = null;
        $fileUrlUpload       = null;

        // Jika file SEB sudah ada, generate QR Code langsung
        if ($file_exam_upload && file_exists(FCPATH . 'uploads/' . $file_exam_upload)) {
            $fileUrlUpload = base_url('uploads/' . $file_exam_upload);

            // Gunakan fungsi generateQrCode untuk konsistensi
            $qrCodeUpload = $this->generateQrCode($fileUrlUpload);
        }
        // Jika file SEB sudah ada, generate QR Code langsung
        if ($file_exam && file_exists(FCPATH . 'uploads/' . $file_exam)) {
            $fileUrl = base_url('uploads/' . $file_exam);

            // Gunakan fungsi generateQrCode untuk konsistensi
            $qrCode = $this->generateQrCode($fileUrl);
        }

        $data = [
            'start_url'     => $start_url,
            'quit_password' => $quit_password,
            'user_agent'    => $user_agent,
            'setting'       => $setting,
            'title'         => 'Generate SEB Config',
            'fileUrl'       => $fileUrl,  // Jika ada file lama
            'fileUrlUpload' => $fileUrlUpload,
            'qrCode'        => $qrCode,   // Jika ada QR Code
            'qrCodeUpload'        => $qrCodeUpload,   // Jika ada QR Code
        ];

        return view('Panel/SebConfig/form_generate', $data);
    }



    // ------------------------
    // GENERATE SEB
    // ------------------------
    public function generate()
    {
        $startUrl     = $this->request->getPost('start_url');
        $quitPassword = $this->request->getPost('quit_password');
        $userAgent    = $this->request->getPost('user_agent') ?? 'SEB';
        $allowQuit    = $this->request->getPost('allow_quit') ? true : false; // checkbox

        if (!$startUrl || !$quitPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start URL dan Quit Password wajib diisi'
            ]);
        }

        // Config dasar
        $config = [
            'startURL'         => $startUrl,
            'quitPassword'     => $quitPassword,
            'browserUserAgent' => $userAgent,
            'allowQuit'        => $allowQuit,
        ];

        $model = new \App\Models\ExambroSettingModel();
        $oldConfig = $model->getExambroSetting(1);
        $oldFile   = $oldConfig['file_exam_config'] ?? null;

        if ($oldFile) {
            $oldFilePath = FCPATH . 'uploads/' . $oldFile;
            if (file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $filename   = 'seb_config_' . time() . '.seb';
        $outputFile = FCPATH . 'uploads/' . $filename;

        $this->generateSebFile($config, $quitPassword, $outputFile);
        $model->updateExamConfig(1, $filename);

        $fileUrl = base_url('uploads/' . $filename);
        $qrCode  = $this->generateQrCode($fileUrl);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'SEB config berhasil digenerate',
            'fileUrl' => $fileUrl,
            'qrCode'  => $qrCode
        ]);
    }

    // ------------------------
    // UPLOAD SEB
    // ------------------------
    public function upload()
    {
        $file = $this->request->getFile('seb_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File SEB tidak ditemukan atau tidak valid.'
            ]);
        }

        if ($file->getClientExtension() !== 'seb') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya file .seb yang diperbolehkan.'
            ]);
        }

        $model = new \App\Models\ExambroSettingModel();
        $oldConfig = $model->getExambroSetting(1);
        $oldFile   = $oldConfig['file_exam_config_upload'] ?? null;

        if ($oldFile) {
            $oldFilePath = FCPATH . 'uploads/' . $oldFile;
            if (file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $filename   = 'seb_upload_' . time() . '.seb';
        $file->move(FCPATH . 'uploads/', $filename);

        $model->updateExamConfigUpload(1, $filename);
        $fileUrl = base_url('uploads/' . $filename);
        $qrCode  = $this->generateQrCode($fileUrl);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'File SEB berhasil diupload',
            'fileUrl' => $fileUrl,
            'qrCode'  => $qrCode
        ]);
    }

    // ------------------------
    // PRIVATE: GENERATE SEB FILE
    // ------------------------
    private function generateSebFile(array $config, string $password, string $outputFile, string $platform = 'ios')
    {
        $iterations = 100000;
        $salt = random_bytes(16);

        // PBKDF2-SHA256
        $hash = hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true);

        $hashB64 = base64_encode($hash);
        $saltB64 = base64_encode($salt);

        // Pilih key sesuai platform
        if ($platform === 'ios') {
            $quitKeys = [
                'quitPasswordHash'           => $hashB64,
                'quitPasswordSalt'           => $saltB64,
                'quitPasswordHashIterations' => $iterations,
                'quitPasswordEnabled'        => true,
                'showQuitButton'             => true,
                'enableSingleAppMode'        => true,   // Kiosk key (iOS)
                'forceSingleAppMode'         => true,
                'allowLeaving'               => false,
                'singleAppModeConfirm'       => true,
            ];
        } else { // Windows
            $quitKeys = [
                'hashedQuitPassword'              => $hashB64,
                'hashedQuitPasswordSalt'          => $saltB64,
                'hashedQuitPasswordHashIterations' => $iterations,
                'quitPasswordEnabled'             => true,
                'allowQuit'                       => $config['allowQuit'] ?? false,
                'showQuitButton'                  => true,
            ];
        }

        $plistArray = array_merge([
            'startURL'         => $config['startURL'],
            'browserUserAgent' => $config['browserUserAgent'] ?? 'SEB',

            // Exam mode
            'browserExamMode'  => true,
            'fullscreen'       => true,
            'enableBrowser'    => true,

            // Security
            'allowPrintScreen' => false,
            'allowSwitchApps'  => false,
            'disableContextMenu' => true,
        ], $quitKeys);

        file_put_contents($outputFile, $this->arrayToPlist($plistArray));
    }

    private function arrayToPlist(array $array): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" ";
        $xml .= "\"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n";
        $xml .= "<plist version=\"1.0\">\n<dict>\n";

        foreach ($array as $key => $value) {
            $xml .= "  <key>{$key}</key>\n";

            if (in_array($key, ['quitPasswordHash', 'quitPasswordSalt', 'hashedQuitPassword', 'hashedQuitPasswordSalt'])) {
                $xml .= "  <data>{$value}</data>\n";
            } elseif (is_int($value)) {
                $xml .= "  <integer>{$value}</integer>\n";
            } elseif (is_bool($value)) {
                $xml .= $value ? "  <true/>\n" : "  <false/>\n";
            } elseif (is_array($value)) {
                $xml .= "  <array>\n";
                foreach ($value as $item) {
                    $xml .= "    <string>{$item}</string>\n";
                }
                $xml .= "  </array>\n";
            } else {
                $xml .= "  <string>{$value}</string>\n";
            }
        }

        $xml .= "</dict>\n</plist>\n";
        return $xml;
    }

    // ------------------------
    // PRIVATE: GENERATE QR CODE
    // ------------------------
    private function generateQrCode(string $fileUrl): string
    {
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $fileUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $logoPath = FCPATH . 'assets/img/' . $this->appSetting()->logo;
        $result   = file_exists($logoPath)
            ? $writer->write($qrCode, new Logo($logoPath, 50))
            : $writer->write($qrCode);

        return $result->getDataUri();
    }
}
