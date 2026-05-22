<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BeritaAcaraModel;
use Ramsey\Uuid\Uuid;

class BeritaAcaraController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new BeritaAcaraModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Halaman View
     */
    public function index()
    {
        $setting = $this->appSetting();

        // Ambil data dropdown
        $ujianModel = new \App\Models\UjianModel();
        $ruangModel = new \App\Models\RuangModel();
        $sesiModel  = new \App\Models\SesiModel();

        $data = [
            'setting' => $setting,
            'title'   => 'Berita Acara Ujian',
            'ujian'   => $ujianModel->getAllUjian(),
            'ruang'   => $ruangModel->orderBy('nama', 'ASC')->findAll(),
            'sesi'    => $sesiModel->orderBy('nama', 'ASC')->findAll(),
        ];

        return view('Panel/BeritaAcara/berita_acara_view', $data);
    }

    /**
     * Ambil semua data Berita Acara
     */
    public function getAll()
    {
        if ($this->request->isAJAX()) {

            // Ambil data berita acara + relasi ujian, ruang, sesi
            $data = $this->model->getWithJoin();


            return $this->response->setJSON([
                'status' => true,
                'data'   => $data,
            ]);
        }
    }

    /**
     * Tambah data Berita Acara
     */
    public function create()
    {
        if ($this->request->isAJAX()) {

            $rules = [
                'ujian_id'  => 'required',
                'ruang_id'  => 'required',
                'sesi_id'   => 'required',
                'tanggal'   => 'required|valid_date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Ambil jenis ujian dari ujian_id
            $ujianModel = new \App\Models\UjianModel();
            $ujian = $ujianModel->find($this->request->getPost('ujian_id'));

            $jenisUjianId = $ujian['jenis_ujian_id'] ?? null;

            $id = Uuid::uuid4()->toString();

            $this->model->insert([
                'id'                        => $id,
                'ujian_id'                  => $this->request->getPost('ujian_id'),
                'jenis_ujian_id'            => $jenisUjianId, // ← otomatis
                'ruang_id'                  => $this->request->getPost('ruang_id'),
                'sesi_id'                   => $this->request->getPost('sesi_id'),
                'tanggal'                   => $this->request->getPost('tanggal'),
                'jam_mulai'                 => $this->request->getPost('jam_mulai'),
                'jam_selesai'               => $this->request->getPost('jam_selesai'),
                'jumlah_peserta_seharusnya' => $this->request->getPost('jumlah_peserta_seharusnya') ?? 0,
                'jumlah_hadir'              => $this->request->getPost('jumlah_hadir') ?? 0,
                'jumlah_tidak_hadir'        => $this->request->getPost('jumlah_tidak_hadir') ?? 0,
                'peserta_tidak_hadir'       => $this->request->getPost('peserta_tidak_hadir'),
                'catatan'                   => $this->request->getPost('catatan'),
                'proktor_nama'              => $this->request->getPost('proktor_nama'),
                'proktor_nip'               => $this->request->getPost('proktor_nip'),
                'pengawas_nama'             => $this->request->getPost('pengawas_nama'),
                'pengawas_nip'              => $this->request->getPost('pengawas_nip'),
                'kepala_sekolah_nama'       => $this->request->getPost('kepala_sekolah_nama'),
                'kepala_sekolah_nip'        => $this->request->getPost('kepala_sekolah_nip'),
                'created_at'                => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Berita acara berhasil ditambahkan.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    /**
     * Update Berita Acara
     */
    public function update($id = null)
    {
        if ($this->request->isAJAX()) {

            $rules = [
                'ujian_id'  => 'required',
                'ruang_id'  => 'required',
                'sesi_id'   => 'required',
                'tanggal'   => 'required|valid_date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Ambil jenis ujian dari tabel ujian
            $ujianModel = new \App\Models\UjianModel();
            $ujian = $ujianModel->find($this->request->getPost('ujian_id'));

            $jenisUjianId = $ujian['jenis_ujian_id'] ?? null;

            $this->model->update($id, [
                'ujian_id'                  => $this->request->getPost('ujian_id'),
                'jenis_ujian_id'            => $jenisUjianId, // ← otomatis
                'ruang_id'                  => $this->request->getPost('ruang_id'),
                'sesi_id'                   => $this->request->getPost('sesi_id'),
                'tanggal'                   => $this->request->getPost('tanggal'),
                'jam_mulai'                 => $this->request->getPost('jam_mulai'),
                'jam_selesai'               => $this->request->getPost('jam_selesai'),
                'jumlah_peserta_seharusnya' => $this->request->getPost('jumlah_peserta_seharusnya') ?? 0,
                'jumlah_hadir'              => $this->request->getPost('jumlah_hadir') ?? 0,
                'jumlah_tidak_hadir'        => $this->request->getPost('jumlah_tidak_hadir') ?? 0,
                'peserta_tidak_hadir'       => $this->request->getPost('peserta_tidak_hadir'),
                'catatan'                   => $this->request->getPost('catatan'),
                'proktor_nama'              => $this->request->getPost('proktor_nama'),
                'proktor_nip'               => $this->request->getPost('proktor_nip'),
                'pengawas_nama'             => $this->request->getPost('pengawas_nama'),
                'pengawas_nip'              => $this->request->getPost('pengawas_nip'),
                'kepala_sekolah_nama'       => $this->request->getPost('kepala_sekolah_nama'),
                'kepala_sekolah_nip'        => $this->request->getPost('kepala_sekolah_nip'),
                'updated_at'                => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Berita acara berhasil diperbarui.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    /**
     * Hapus Berita Acara
     */
    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Berita acara berhasil dihapus.'
            ]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    /**
     * Shortcut error JSON
     */
    private function fail($message)
    {
        return $this->response->setJSON([
            'status'  => false,
            'message' => $message
        ]);
    }

    public function print($id)
    {
        $setting = $this->appSetting();

        // Ambil data berita acara + relasi ujian, ruang, sesi
        $data = $this->model->getWithJoin($id);

        if (!$data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita acara tidak ditemukan.');
        }

        // ============================
        // 🔥 Format Hari, Tanggal, Bulan, Tahun
        // ============================
        $timestamp = strtotime($data['tanggal']);

        // Hari dalam bahasa Indonesia
        $hariList = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $hari = $hariList[date('l', $timestamp)];

        // Tanggal (1-31)
        $tgl = date('j', $timestamp);

        // Nama bulan Indonesia
        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $bulan = $bulanList[(int)date('n', $timestamp)];

        // Tahun 4 digit
        $tahun = date('Y', $timestamp);

        // Loloskan semua variabel ke View baru
        return view('Panel/BeritaAcara/print_view', [
            'setting' => $setting,
            'data'    => $data,
            'hari'    => $hari,
            'tgl'     => $tgl,
            'bulan'   => $bulan,
            'tahun'   => $tahun
        ]);
    }
}
