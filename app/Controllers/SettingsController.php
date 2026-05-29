<?php

namespace App\Controllers;

use App\Models\SettingsModel;


class SettingsController extends BaseController
{
    public function __construct()
    {
        $this->Settings_model = new SettingsModel();
    }
    private $Settings_model;
    public function index()
    {

        $setting = $this->appSetting();
        $data = [

            'setting' => $setting,
            'title' => 'Pengaturan'
        ];
        return view('Panel/Settings/settingssekolah', $data);
    }
    public function getData()
    {
        $setting = $this->Settings_model->get_by_id(1);

        return $this->response->setJSON($setting);
    }

    public function update($id = 1)
    {
        $appName = $this->request->getPost('appname');
        $logo = $this->request->getFile('logo');
        $logomentri = $this->request->getFile('logomentri');
        $kopsurat = $this->request->getFile('kopsurat');
        // Inisialisasi data awal
        $data = [
            'appname' => $appName,
            'nama_sekolah' => $this->request->getPost('nama_sekolah'),
            'jenjang' => $this->request->getPost('jenjang'),
            'alamat_sekolah' => $this->request->getPost('alamat_sekolah'),
            'kelurahan' => $this->request->getPost('kelurahan'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'kota' => $this->request->getPost('kota'),
            'provinsi' => $this->request->getPost('provinsi'),
            'website' => $this->request->getPost('website'),
            'email' => $this->request->getPost('email'),
            'npsn' => $this->request->getPost('npsn'),
            'nss' => $this->request->getPost('nss'),
            'nama_kepsek' => $this->request->getPost('nama_kepsek'),
            'kementrian' => $this->request->getPost('kementrian'),
            'nip_kepsek' => $this->request->getPost('nip_kepsek'),
            'key_encrypt' => $this->request->getPost('key_encrypt'),
            'api_token' => $this->request->getPost('api_token')
        ];
        // Validasi untuk Logo
        if (!empty($logo->getName())) {
            if ($logo->isValid() && ($logo->getClientMimeType() === 'image/png' || $logo->getClientMimeType() === 'image/jpeg')) {
                $maxSize = 2 * 1024 * 1024; // Ukuran maksimum 2MB
                if ($logo->getSize() > $maxSize) {
                    $response = ['message' => 'Ukuran file logo terlalu besar.'];
                    return $this->response->setJSON($response)->setStatusCode(400);
                }
                // Baca data gambar
                $imageData = file_get_contents($logo->getTempName());

                // Konversi gambar ke base64
                $base64Image = base64_encode($imageData);
                // Pindahkan logo ke folder uploads
                $logo->move(FCPATH . 'assets/img');

                $data['logo'] = $logo->getName();
            } else {
                $response = ['message' => 'Jenis file logo tidak valid. Harap unggah file PNG atau JPEG.'];
                return $this->response->setJSON($response)->setStatusCode(400);
            }
        }
        if (!empty($logomentri->getName())) {
            if ($logomentri->isValid() && ($logomentri->getClientMimeType() === 'image/png' || $logomentri->getClientMimeType() === 'image/jpeg')) {
                $maxSize = 2 * 1024 * 1024; // Ukuran maksimum 2MB
                if ($logomentri->getSize() > $maxSize) {
                    $response = ['message' => 'Ukuran file logo terlalu besar.'];
                    return $this->response->setJSON($response)->setStatusCode(400);
                }
                // Mendapatkan nama sementara file
                $tempPath = $logomentri->getTempName();
                // Pindahkan logo ke folder uploads
                $newFileName = "logomentri." . $logomentri->getExtension();
                //$logomentri->move(FCPATH . 'assets/img', $newFileName, true);
                $path = FCPATH . 'assets/img/' . $newFileName;
                $this->compressImage($tempPath, $path, 80);
                $imageData = file_get_contents($path);
                $base64Image = base64_encode($imageData);
                $data['logokementrian'] = $newFileName;
            } else {
                $response = ['message' => 'Jenis file logo tidak valid. Harap unggah file PNG atau JPEG.'];
                return $this->response->setJSON($response)->setStatusCode(400);
            }
        }
        if (!empty($kopsurat->getName())) {
            if ($kopsurat->isValid() && ($kopsurat->getClientMimeType() === 'image/png' || $kopsurat->getClientMimeType() === 'image/jpeg')) {
                $maxSize = 2 * 1024 * 1024; // Ukuran maksimum 2MB
                if ($kopsurat->getSize() > $maxSize) {
                    $response = ['message' => 'Ukuran file kopsurat terlalu besar.'];
                    return $this->response->setJSON($response)->setStatusCode(400);
                }
                $newFileName = "kop_surat." . $kopsurat->getExtension();
                // Pindahkan logo ke folder uploads
                $kopsurat->move(FCPATH . 'assets/img', $newFileName, true);
                // Perbarui nilai logo di $data jika logo valid
                $data['kop_surat'] = $newFileName;
            } else {
                $response = ['message' => 'Jenis file kop tidak valid. Harap unggah file PNG atau JPEG.' . $kopsurat->getName()];
                return $this->response->setJSON($response)->setStatusCode(400);
            }
        }

        $exec = $this->Settings_model->settings_update(['id' => $id], $data);

        if ($exec) {
            try {
                service('cache')->delete('app_setting');
            } catch (\Throwable $e) {
                log_message('error', 'Redis delete app_setting failed: ' . $e->getMessage());
            }
            return $this->response->setJSON(['success' => true, 'message' => 'User update successfully']);
        } else {
            //return $this->response->setJSON($error);
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data yang mengalami perubahan']);
        }
    }
    private function compressImage($sourcePath, $targetPath, $quality)
    {
        $image = \Config\Services::image(); // Menginisialisasi objek Image Manipulation
        $image->withFile($sourcePath)
            ->fit(200, 200, 'center')
            ->save($targetPath, $quality); // Simpan gambar dengan kualitas kompresi yang ditentukan
    }
}
