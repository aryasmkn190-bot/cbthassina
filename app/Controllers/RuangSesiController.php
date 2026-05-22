<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\PesertaModel;
use App\Models\RuangModel;
use App\Models\SesiModel;

class RuangSesiController extends BaseController
{
    protected $kelasModel;
    protected $pesertaModel;
    protected $ruangModel;
    protected $sesiModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->pesertaModel = new PesertaModel();
        $this->ruangModel = new RuangModel();
        $this->sesiModel = new SesiModel();
    }

    /**
     * Halaman utama manajemen ruang & sesi
     */
    public function index()
    {
        $setting = $this->appSetting();
        $jumlahPesertaPerRuang = [];
        $ruang = $this->ruangModel->findAll();
        foreach ($ruang as $r) {
            $jumlah = $this->pesertaModel
                ->where('ruang_id', $r['id'])
                ->countAllResults();
            $jumlahPesertaPerRuang[$r['id']] = $jumlah;
        }
        $data = [
            'setting' => $setting,
            'title' => 'Ruang dan Sesi',
            'kelas' => $this->kelasModel->getSorted(),
            'ruang' => $ruang,
            'sesi'  => $this->sesiModel->findAll(),
            'jumlahPesertaPerRuang' => $jumlahPesertaPerRuang
        ];

        return view('Panel/RuangSesi/ruang-sesi_view', $data);
    }

    /**
     * Ambil peserta berdasarkan kelas dan kirim HTML langsung
     */
    public function getPesertaByKelas()
    {
        $kelasId = $this->request->getPost('kelas_id');
        if (!$kelasId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kelas tidak valid',
                'data' => null
            ]);
        }

        $peserta = $this->pesertaModel
            ->where('kelas_id', $kelasId)
            ->orderBy('nama', 'ASC')
            ->findAll();

        $ruang = $this->ruangModel->findAll();
        $sesi  = $this->sesiModel->findAll();
        // Hitung jumlah peserta per ruang

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'peserta' => $peserta,
                'ruang'   => $ruang,
                'sesi'    => $sesi,

            ]
        ]);
    }

    /**
     * Simpan ruang & sesi peserta
     */
    public function simpanRuangSesi()
    {
        $data = $this->request->getPost('data'); // array of {peserta_id, ruang_id, sesi_id}

        if (!$data || !is_array($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        $updated = 0;
        foreach ($data as $item) {
            if (!isset($item['peserta_id'])) continue;

            $this->pesertaModel->update($item['peserta_id'], [
                'ruang_id' => $item['ruang_id'] ?? null,
                'sesi_id'  => $item['sesi_id'] ?? null,
            ]);
            $updated++;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$updated peserta berhasil diperbarui"
        ]);
    }
    public function printDenah()
    {
        $ruangId = $this->request->getGet('ruang_id');
        $sesiId = $this->request->getGet('sesi_id');
        $cols = (int) $this->request->getGet('cols') ?: 5; // default 5 kolom jika tidak dikirim

        if (!$ruangId || !$sesiId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ruang atau sesi tidak ditemukan.');
        }

        $ruang = $this->ruangModel->find($ruangId);
        $sesi = $this->sesiModel->find($sesiId);

        // Ambil peserta dengan join kelas
        $peserta = $this->pesertaModel
            ->select('peserta.*, kelas.nama as nama_kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('ruang_id', $ruangId)
            ->where('sesi_id', $sesiId)
            ->orderBy('nama', 'ASC')
            ->findAll();
        $setting = $this->appSetting();
        return view('Panel/RuangSesi/denah_print', [
            'ruang' => $ruang,
            'sesi' => $sesi,
            'peserta' => $peserta,
            'cols' => $cols,
            'setting' => $setting,
        ]);
    }


    public function print()
    {
        $ruangId = $this->request->getGet('ruang_id');
        $sesiId  = $this->request->getGet('sesi_id');

        $pesertaModel = new PesertaModel();
        $ruangModel   = new RuangModel();
        $sesiModel    = new SesiModel();

        $ruang = $ruangModel->find($ruangId);
        $sesi  = $sesiModel->find($sesiId);
        $peserta = $pesertaModel
            ->select('peserta.*, kelas.nama AS nama_kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('peserta.ruang_id', $ruangId)
            ->where('peserta.sesi_id', $sesiId)
            ->orderBy('peserta.nama', 'ASC')
            ->findAll();

        $setting = $this->appSetting();
        return view('Panel/RuangSesi/daftar_hadir_print', [
            'ruang' => $ruang,
            'sesi' => $sesi,
            'peserta' => $peserta,
            'setting' => $setting,
        ]);
    }
}
