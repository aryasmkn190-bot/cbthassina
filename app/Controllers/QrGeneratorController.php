<?php

namespace App\Controllers;

use App\Models\ExambroSettingModel;
use App\Controllers\BaseController;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;





class QrGeneratorController extends BaseController
{

    public function index()
    {

        $setting = $this->appSetting();

        $qrImageUri = $this->qrserver();


        $data = [

            'setting' => $setting,
            'title' => 'Qr Generator',
            'qrImageUri' => $qrImageUri
        ];
        return view('Panel/QrGenerator/qr_generator_view', $data);
    }
    public function ujian()
    {

        $setting = $this->appSetting();

        $qrImageUri = $this->qrserver();


        $data = [

            'setting' => $setting,
            'title' => 'Qr Link Ujian',
            'qrImageUri' => $qrImageUri
        ];
        return view('Panel/QrGenerator/qr_generator_view_ujian', $data);
    }

    public function generateprint()
    {

        // Ambil URL dari input form
        $serverUrl = $this->request->getPost('url_server') ?: $this->getServerUrl();

        $namaUjian   = $this->request->getPost('nama_ujian');
        $jumlahRuang = (int) $this->request->getPost('jumlah_ruang');

        // Ambil secret_code
        $settingModel = new ExambroSettingModel();
        $setting = $settingModel->find(1);
        if (!$setting || empty($setting['secret_code'])) {
            throw new \Exception('Secret code belum diset di tabel settings_exambro.');
        }
        $secretCode = $setting['secret_code'];

        helper('encryption');

        // 🔹 Generate QR untuk setiap ruang supaya unik
        $qrCodes = [];
        for ($i = 1; $i <= $jumlahRuang; $i++) {
            $payload = [
                'url' => rtrim($serverUrl, '/'),
                'secret_code' => $secretCode,
                'ruang' => $i // Tambahkan nomor ruang supaya QR tiap ruang unik
            ];

            $encryptedData = encryptData(json_encode($payload, JSON_UNESCAPED_SLASHES));

            $writer = new PngWriter();
            $qrCode = new QrCode(
                data: $encryptedData,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $logoPath = FCPATH . 'assets/img/' . $this->appSetting()->logo;
            $logo = file_exists($logoPath)
                ? new Logo(path: $logoPath, resizeToWidth: 50, punchoutBackground: true)
                : null;
            $label = new Label(
                text: $serverUrl,
                textColor: new Color(255, 0, 0)
            );
            $result = $writer->write($qrCode, $logo, $label);
            $qrCodes[] = $result; // simpan tiap QR per ruang
        }

        $setting = $this->appSetting();

        return view('Panel/QrGenerator/qr_print_view', [
            'qr_codes'     => $qrCodes,   // array QR per ruang
            'setting'      => $setting,
            'nama_ujian'   => $namaUjian,
            'jumlah_ruang' => $jumlahRuang
        ]);
    }
    public function generateprintlink()
    {

        // Ambil URL dari input form
        $serverUrl = $this->request->getPost('url_server') ?: $this->getServerUrl();

        $namaUjian   = $this->request->getPost('nama_ujian');
        $jumlahRuang = (int) $this->request->getPost('jumlah_ruang');
        $encryptedData = encryptData($serverUrl);

        // 🔹 Generate QR untuk setiap ruang supaya unik
        $qrCodes = [];
        for ($i = 1; $i <= $jumlahRuang; $i++) {

            $writer = new PngWriter();
            $qrCode = new QrCode(
                data: $encryptedData,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $logoPath = FCPATH . 'assets/img/' . $this->appSetting()->logo;
            $logo = file_exists($logoPath)
                ? new Logo(path: $logoPath, resizeToWidth: 50, punchoutBackground: true)
                : null;
            $label = new Label(
                text: $serverUrl,
                textColor: new Color(255, 0, 0)
            );
            $result = $writer->write($qrCode, $logo, $label);
            $qrCodes[] = $result; // simpan tiap QR per ruang
        }

        $setting = $this->appSetting();

        return view('Panel/QrGenerator/qr_print_view', [
            'qr_codes'     => $qrCodes,   // array QR per ruang
            'setting'      => $setting,
            'nama_ujian'   => $namaUjian,
            'jumlah_ruang' => $jumlahRuang
        ]);
    }

    /**
     * Helper untuk menentukan URL server otomatis jika tidak dikirim via POST
     */
    private function getServerUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        if (in_array($host, ['localhost', '127.0.0.1'])) {
            $ip = gethostbyname(gethostname());
            return $protocol . $ip . ':' . $_SERVER['SERVER_PORT'];
        }
        return $protocol . $host;
    }


    public function qrserver()
    {
        $serverUrl = base_url();

        // 🔹 Ambil secret_code dari settings_exambro
        $settingModel = new ExambroSettingModel();
        $setting = $settingModel->find(1);

        if (!$setting || empty($setting['secret_code'])) {
            return $this->response->setStatusCode(404)
                ->setBody('Secret code belum diset di tabel settings_exambro.');
        }

        $secretCode = $setting['secret_code'];

        // 🔹 Siapkan payload terenkripsi (URL + secret code)
        $payload = [
            'url' => $serverUrl,
            'secret_code' => $secretCode
        ];

        $encryptedData = encryptData(json_encode($payload, JSON_UNESCAPED_SLASHES));

        // 🔹 Generate QR code
        $writer = new PngWriter();

        $qrCode = new QrCode(
            data: $encryptedData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $logo = new Logo(
            path: FCPATH . 'assets/img/' . $this->appSetting()->logo,
            resizeToWidth: 50,
            punchoutBackground: true
        );

        $label = new Label(
            text: $serverUrl,
            textColor: new Color(255, 0, 0)
        );

        $result = $writer->write($qrCode, $logo, $label);

        // 🔹 Return QR code sebagai Data URI
        return $result->getDataUri();
    }
    public function generateserver()
    {

        $serverUrl = $this->request->getPost('url_server');

        // 🔹 Ambil secret_code dari settings_exambro
        $settingModel = new ExambroSettingModel();
        $setting = $settingModel->find(1);

        if (!$setting || empty($setting['secret_code'])) {
            return $this->response->setStatusCode(404)
                ->setBody('Secret code belum diset di tabel settings_exambro.');
        }

        $secretCode = $setting['secret_code'];

        // 🔹 Siapkan payload terenkripsi (URL + secret code)
        $payload = [
            'url' => $serverUrl,
            'secret_code' => $secretCode
        ];

        $encryptedData = encryptData(json_encode($payload, JSON_UNESCAPED_SLASHES));

        // 🔹 Generate QR code
        $writer = new PngWriter();

        $qrCode = new QrCode(
            data: $encryptedData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $logo = new Logo(
            path: FCPATH . 'assets/img/' . $this->appSetting()->logo,
            resizeToWidth: 50,
            punchoutBackground: true
        );

        $label = new Label(
            text: $serverUrl,
            textColor: new Color(255, 0, 0)
        );

        $result = $writer->write($qrCode, $logo, $label);

        // 🔹 Return QR code sebagai Data URI
        return $result->getDataUri();
    }
    public function qrujian()
    {
        $linkujian = $this->request->getGet('link');
        $writer = new PngWriter();
        $encryptedUrl = encryptData($linkujian);

        $qrCode = new QrCode(
            data: $encryptedUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $logo = new Logo(
            path: FCPATH . 'assets/img/' . $this->appSetting()->logo,
            resizeToWidth: 50,
            punchoutBackground: true
        );

        // $label = new Label(
        //     text: $serverUrl,
        //     textColor: new Color(255, 0, 0)
        // );

        $result = $writer->write($qrCode, $logo);

        return $this->response->setJSON([
            'qr' => $result->getDataUri()
        ]);
    }
}
