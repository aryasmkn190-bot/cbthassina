<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'appname'           => 'Candy CBT',
            'nama_sekolah'      => 'SMK Contoh Mandiri',
            'npsn'              => '12345678',
            'nss'               => '987654321',
            'jenjang'           => 'SMK',
            'alamat_sekolah'    => 'Jl. Pendidikan No.1',
            'website'           => 'https://smkcontoh.sch.id',
            'email'             => 'admin@smkcontoh.sch.id',
            'kelurahan'         => 'Kelurahan Contoh',
            'kecamatan'         => 'Kecamatan Mandiri',
            'kota'              => 'Kota Edukasi',
            'provinsi'          => 'Provinsi Belajar',
            'nama_kepsek'       => 'Ibu Guru Hebat',
            'nip_kepsek'        => '196512301990032001',
            'tahunpelajaran'    => '2024/2025',
            'logo'              => 'logo.png',
            'logokementrian'    => 'logokem.png',
            'favicon'           => 'favicon.ico',
            'kop_surat'         => 'kop.png',
            'key_encrypt'       => 'secretkey123456',
            'kementrian'        => 'Kementerian Pendidikan',
            'appversion'        => '1.0.4',
            'verifywa_template' => 'Halo {nama}, akun Anda telah diverifikasi untuk ujian.',
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $this->db->table('settings')->insert($data);
    }
}
