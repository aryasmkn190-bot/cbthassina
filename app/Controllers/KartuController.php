<?php

namespace App\Controllers;

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
use App\Models\KelasModel;
use App\Models\ExambroSettingModel;
use App\Models\PesertaModel;

class KartuController extends BaseController
{

    public function index()
    {

        $setting = $this->appSetting();

        $qrImageUri = $this->qrserver();
        $kelasModel = new KelasModel();

        $data = [
            'kelas' => $kelasModel->getSorted(),
            'setting' => $setting,
            'title' => 'Kartu Peserta',
            'qrImageUri' => $qrImageUri
        ];
        return view('Panel/Kartu/kartu_view', $data);
    }
    public function preview()
    {
        $namaUjian = $this->request->getPost('nama_ujian');
        $kelasId = $this->request->getPost('kelas_id');

        // Join dengan tabel kelas
        $peserta = (new PesertaModel())
            ->select('peserta.*, kelas.nama AS nama_kelas, jurusan.nama AS nama_jurusan')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = peserta.jurusan_id', 'left')
            ->where('peserta.kelas_id', $kelasId)
            ->orderBy('peserta.username')
            ->first();

        if (!$peserta) {
            return '<div class="alert alert-warning">Tidak ada peserta di kelas ini.</div>';
        }
        $qrImageUri = $this->qrserver();
        return view('Panel/Kartu/kartu_preview', [
            'setting' => $this->appSetting(),
            'peserta' => $peserta,
            'qrImageUri' => $qrImageUri,
            'nama_ujian' => $namaUjian
        ]);
    }
    public function print()
    {
        $namaUjian = $this->request->getGet('nama_ujian');
        $kelasId = $this->request->getGet('kelas_id');

        if (!$kelasId || !$namaUjian) {
            return redirect()->back()->with('error', 'Parameter kelas dan nama ujian harus diisi.');
        }

        $peserta = (new PesertaModel())
            ->select('peserta.*, kelas.nama AS nama_kelas, jurusan.nama AS nama_jurusan')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = peserta.jurusan_id', 'left')
            ->where('peserta.kelas_id', $kelasId)
            ->orderBy('peserta.username')
            ->findAll();

        if (!$peserta) {
            return '<div class="alert alert-warning">Tidak ada peserta ditemukan.</div>';
        }
        $qrImageUri = $this->qrserver();
        return view('Panel/Kartu/kartu_print_view', [
            'setting' => $this->appSetting(),
            'pesertaList' => $peserta,
            'nama_ujian' => $namaUjian,
            'qrImageUri' => $qrImageUri,
        ]);
    }


    public function generate()
    {
        $link = $this->request->getPost('link_ujian');
        if ($link) {
            $serverUrl = $link;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = gethostbyname(gethostname());
            $serverUrl = $protocol . $host . ':' . $_SERVER['SERVER_PORT'];
        }

        // $serverUrl   = $this->request->getPost('server_url') ?? site_url();
        $namaUjian   = $this->request->getPost('nama_ujian');
        $jumlahRuang = (int) $this->request->getPost('jumlah_ruang');
        $writer = new PngWriter();
        $encryptedUrl = encryptData($serverUrl);

        // Create QR code
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

        // Create generic logo
        $logo = new Logo(
            path: FCPATH . 'assets/img/' . $this->appSetting()->logo,
            resizeToWidth: 50,
            punchoutBackground: true
        );

        // Create generic label
        // $label = new Label(
        //     text: 'Qr Code Server',
        //     textColor: new Color(255, 0, 0)
        // );

        $result = $writer->write($qrCode, $logo);

        $setting = $this->appSetting();

        return view('Panel/QrGenerator/qr_print_view', [
            'qr_code'      => $result,
            'setting' => $setting,
            'nama_ujian'   => $namaUjian,
            'jumlah_ruang' => $jumlahRuang
        ]);
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


        $result = $writer->write($qrCode, $logo);

        // 🔹 Return QR code sebagai Data URI
        return $result->getDataUri();
    }
}
