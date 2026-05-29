<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\HasilUjianModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\JawabanModel;
use Ramsey\Uuid\Uuid;

class UjianPesertaController extends BaseController
{
    protected $ujianModel;
    protected $hasilUjianModel;
    protected $soalModel;
    protected $soalOpsiModel;
    protected $jawabanModel;
    public function __construct()
    {
        $this->ujianModel = new UjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->soalModel = new SoalModel();
        $this->soalOpsiModel = new SoalOpsiModel();
        $this->jawabanModel = new JawabanModel();
    }
    public function index()
    {
        $setting = $this->appSetting(); // jika ada pengaturan global

        $pesertaData = [
            'nama'  => '',
            'nisn'  => '',
            'kelas' => ''
        ];

        $pesertaSession = session('peserta');
        $pesertaId = $pesertaSession['id'] ?? null;

        if ($pesertaId) {
            $pesertaModel = new \App\Models\PesertaModel();

            // Ambil data peserta termasuk nama kelas via join
            $peserta = $pesertaModel
                ->select('peserta.nama, peserta.nisn, kelas.nama as kelas')
                ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
                ->find($pesertaId);

            if ($peserta) {
                $pesertaData = [
                    'nama'  => $peserta['nama'] ?? '',
                    'nisn'  => $peserta['nisn'] ?? '',
                    'kelas' => $peserta['kelas'] ?? '',
                ];
            }
        }

        $data = [
            'setting' => $setting,
            'title'   => 'Daftar Ujian Saya',
            'peserta' => $pesertaData
        ];

        return view('Peserta/ujian', $data);
    }

    public function getAllUjian()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Hanya bisa diakses via AJAX.');
        }

        $pesertaId = session()->get('peserta')['id'];

        // Ambil semua ujian peserta sekaligus
        $data = $this->ujianModel->getAllPeserta($pesertaId);

        // Ambil semua hasil ujian peserta sekaligus
        $hasilList = $this->hasilUjianModel
            ->select('ujian_id, status')
            ->where('peserta_id', $pesertaId)
            ->whereIn('ujian_id', array_column($data, 'id'))
            ->findAll();

        // Buat mapping ujian_id => hasil
        $hasilMap = [];
        foreach ($hasilList as $h) {
            $hasilMap[$h['ujian_id']] = $h;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($data as &$ujian) {
            $waktuMulai = $ujian['waktu_mulai'];
            $waktuSelesai = $ujian['waktu_selesai'];

            // Status waktu ujian
            if ($now < $waktuMulai) {
                $ujian['status_waktu'] = 'belum_mulai';
            } elseif ($now >= $waktuMulai && $now <= $waktuSelesai) {
                $ujian['status_waktu'] = 'dibuka';
            } else {
                $ujian['status_waktu'] = 'terlambat';
            }

            // Status aktivitas peserta
            if (isset($hasilMap[$ujian['id']])) {
                $status = $hasilMap[$ujian['id']]['status'];
                if ($status === 'selesai') {
                    $ujian['status_peserta'] = 'selesai';
                } elseif ($status === 'sedang_ujian') {
                    $ujian['status_peserta'] = 'sedang_mengerjakan';
                } else {
                    $ujian['status_peserta'] = 'belum_mulai';
                }
            } else {
                $ujian['status_peserta'] = 'belum_mulai';
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $data
        ]);
    }


    public function cekToken()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses tidak valid.']);
        }

        $ujianId = $this->request->getPost('ujian_id');
        $tokenInput = $this->request->getPost('token');
        $peserta = session()->get('peserta');
        $ujian = $this->ujianModel->find($ujianId);

        if (!$ujian) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ujian tidak ditemukan.']);
        }

        if (strtoupper(trim($ujian['token'])) !== strtoupper(trim($tokenInput))) {
            return $this->response->setJSON(['status' => false, 'message' => 'Token salah.']);
        }
        // Set token_valid = 1 di hasil_ujian
        $hasil = $this->hasilUjianModel
            ->select('id')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if ($hasil) {
            $this->hasilUjianModel->update($hasil['id'], [
                'token_valid' => 1
            ]);
        }

        return $this->response->setJSON(['status' => true]);
    }

    public function apiGetUjian($ujianId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Hanya bisa diakses via AJAX.');
        }

        $peserta = session()->get('peserta');
        if (!$peserta) {
            return $this->fail('Belum login.');
        }

        $pesertaId = $peserta['id'];

        // Ambil ujian + hasil peserta sekaligus (join hasil_ujian)
        $builder = $this->ujianModel
            ->select('
            ujian.id as ujian_id,
            ujian.nama_ujian,
            ujian.kode_ujian,
            ujian.durasi_ujian,
            ujian.acak_soal,
            ujian.acak_opsi,
            ujian.pakai_token,
            ujian.single_login,
            bank_soal.id as bank_soal_id,
            bank_soal.nama as nama_bank_soal,
            hu.id as hasil_id,
            hu.status as hasil_status,
            hu.waktu_mulai as hasil_waktu_mulai
        ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id', 'left')
            ->join('hasil_ujian hu', "hu.ujian_id = ujian.id AND hu.peserta_id = '{$pesertaId}'", 'left')
            ->where('ujian.id', $ujianId);

        $data = $builder->get()->getRowArray();

        if (!$data || !$data['hasil_id']) {
            return $this->fail('Ujian atau hasil peserta tidak ditemukan.');
        }

        // Format response ringan
        $ujian = [
            'id' => $data['ujian_id'],
            'nama_ujian' => $data['nama_ujian'],
            'kode_ujian' => $data['kode_ujian'],
            'durasi_ujian' => $data['durasi_ujian'],
            'acak_soal' => $data['acak_soal'],
            'acak_opsi' => $data['acak_opsi'],
            'pakai_token' => $data['pakai_token'],
            'single_login' => $data['single_login'],
            'bank_soal' => [
                'id' => $data['bank_soal_id'],
                'nama' => $data['nama_bank_soal']
            ]
        ];

        $hasil = [
            'id' => $data['hasil_id'],
            'status' => $data['hasil_status'],
            'waktu_mulai' => $data['hasil_waktu_mulai']
        ];

        return $this->response->setJSON([
            'status' => true,
            'ujian' => $ujian,
            'hasil' => $hasil
        ]);
    }

    public function getSoal($ujianId)
    {
        $peserta = session('peserta');
        if (!$peserta) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Belum login.'
            ]);
        }

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'getsoal');
        if (isset($validasi['error'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $validasi['error']
            ]);
        }

        $hasil = $validasi['hasil'];
        $soalIds = json_decode($hasil['urutan_soal'], true);
        $opsiUrutan = json_decode($hasil['urutan_opsi'], true);

        if (!is_array($soalIds) || !is_array($opsiUrutan)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data urutan soal atau opsi tidak valid.'
            ]);
        }

        $soal = $this->soalModel->getSoalByUrutanCached($validasi['ujian']['bank_soal_id'], $soalIds, $opsiUrutan);

        // Hilangkan kunci jawaban & hitung max_select untuk MPG
        foreach ($soal as &$s) {
            $maxSelect = 0;
            if ($s['jenis_soal'] === 'mpg' && !empty($s['opsi'])) {
                foreach ($s['opsi'] as &$op) {
                    if (!empty($op['is_true'])) $maxSelect++;
                    unset($op['is_true']);
                }
            } else {
                foreach ($s['opsi'] as &$op) {
                    unset($op['is_true']);
                }
            }
            if ($maxSelect > 0) $s['max_select'] = $maxSelect;
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $soal
        ]);
    }



    public function mulai($ujianId)
    {
        $peserta = session()->get('peserta');
        if (!$peserta) return redirect()->to(base_url('auth/login'));

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'mulai');

        if (isset($validasi['error'])) {
            return redirect()->to(base_url('peserta/home'))->with('error', $validasi['error']);
        }

        // ✅ Set session flashdata status agar diketahui bahwa ini baru mulai
        session()->setFlashdata('status_ujian', 'mulai');

        // ✅ Redirect ke /lanjut
        return redirect()->to(base_url('peserta/ujian/lanjut/' . $ujianId));
    }

    public function lanjut($ujianId)
    {
        $peserta = session()->get('peserta');
        if (!$peserta) return redirect()->to(base_url('auth/login'));

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'lanjut');

        if (isset($validasi['error'])) {
            return redirect()->to(base_url('peserta/home'))->with('error', $validasi['error']);
        }

        $ujian = $validasi['ujian'];
        $hasil = $validasi['hasil'];
        $setting = $this->appSetting();

        $data = [
            'setting' => $setting,
            'title' => 'Lanjut Ujian',
            'ujian' => $ujian,
            'hasil' => $hasil,
            'peserta' => $peserta,
            'ujianId' => $ujianId,
            'status_ujian' => session()->getFlashdata('status_ujian') ?? 'lanjut',

        ];

        return view('Peserta/ujian_mulai', $data);
    }



    private function validasiUjianPeserta(string $ujianId, string $pesertaId, string $mode = 'mulai')
    {
        $ujian = $this->ujianModel
            ->select('ujian.*, bank_soal.nama as nama_bank_soal')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->find($ujianId);

        if (!$ujian) return ['error' => 'Ujian tidak ditemukan.'];

        $now = date('Y-m-d H:i:s');
        if ($now < $ujian['waktu_mulai']) return ['error' => 'Ujian belum dimulai.'];
        if ($now > $ujian['waktu_selesai']) return ['error' => 'Waktu ujian sudah berakhir.'];

        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();

        if (!$hasil) return ['error' => 'Data hasil ujian belum tersedia untuk peserta ini.'];

        if ($hasil['status'] === 'selesai') {
            return ['error' => 'Kamu sudah menyelesaikan ujian ini.'];
        }

        if ($ujian['pakai_token'] == '1' && !$hasil['token_valid']) {
            return ['error' => 'Token belum diverifikasi.'];
        }

        if ($mode === 'mulai') {
            if ($hasil['status'] === 'sedang_ujian') {
                return ['error' => 'Ujian sudah dimulai sebelumnya.'];
            }
        }

        if ($mode === 'lanjut') {
            if ($hasil['status'] !== 'sedang_ujian') {
                return ['error' => 'Ujian belum dimulai.'];
            }
        }

        $statusSebelum = $hasil['status'];
        $perluUpdate = [];

        // ✅ Atur urutan soal jika belum ada
        $soalList = json_decode($hasil['urutan_soal'] ?? '[]', true);
        if (!is_array($soalList) || count($soalList) === 0) {
            $soalList = $this->soalModel->getSoalIdsByBank($ujian['bank_soal_id']);
            if ($ujian['acak_soal'] == '1') shuffle($soalList);
            $perluUpdate['urutan_soal'] = json_encode($soalList);
            $hasil['urutan_soal'] = $perluUpdate['urutan_soal'];
        }

        // ✅ Atur urutan opsi jika belum ada
        $opsiMap = json_decode($hasil['urutan_opsi'] ?? '{}', true);
        if (!is_array($opsiMap) || count($opsiMap) === 0) {
            $opsiMap = $this->soalModel->getOpsiOrderMap($ujian['bank_soal_id'], $ujian['acak_opsi'] == '1');
            $perluUpdate['urutan_opsi'] = json_encode($opsiMap);
            $hasil['urutan_opsi'] = $perluUpdate['urutan_opsi'];
        }

        // ✅ Jika mulai, atur status & waktu mulai
        if ($mode === 'mulai' && $statusSebelum !== 'sedang_ujian') {
            $perluUpdate['status'] = 'sedang_ujian';
            $perluUpdate['platform'] = 'web';
            $perluUpdate['waktu_mulai'] = $now;
            $hasil['status'] = 'sedang_ujian';
            $hasil['waktu_mulai'] = $now;
        }

        // ✅ Validasi device ID jika single login
        $deviceId = $this->request->getCookie('device_id') ?? null;
        if ($ujian['single_login'] == '1') {
            if (!$hasil['device_id'] && $deviceId) {
                $perluUpdate['device_id'] = $deviceId;
                $hasil['device_id'] = $deviceId;
            } elseif ($hasil['device_id'] && $hasil['device_id'] !== $deviceId) {
                return ['error' => 'Akses dari perangkat berbeda. Hubungi panitia.'];
            }
        }

        if (!empty($perluUpdate)) {
            $this->hasilUjianModel->update($hasil['id'], $perluUpdate);
        }

        return [
            'ujian' => $ujian,
            'hasil' => $hasil,
            'status_awal' => $statusSebelum
        ];
    }

    public function getJawabanPeserta($ujianId)
    {
        $pesertaId = session('peserta')['id'];

        $rows = $this->jawabanModel->getJawabanCached($ujianId, $pesertaId);

        $data = [];

        foreach ($rows as $row) {
            $rawJawaban = $row['jawaban'];

            // Coba decode JSON, jika gagal kembalikan string as-is
            $decoded = json_decode($rawJawaban, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$row['soal_id']] = $decoded;
            } else {
                $data[$row['soal_id']] = $rawJawaban;
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $data
        ]);
    }
    public function simpanJawaban()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Akses hanya via AJAX');
        }

        $peserta = session()->get('peserta');
        if (!$peserta) {
            return $this->fail('Peserta belum login');
        }

        $post = $this->request->getPost();
        $ujianId = $post['ujian_id'] ?? null;
        $jawabanInput = $post['jawaban'] ?? null;

        if (!$ujianId || !is_array($jawabanInput)) {
            return $this->fail('Data tidak lengkap atau salah format');
        }

        $validJawaban = [];
        foreach ($jawabanInput as $soalId => $jawaban) {
            if (is_array($jawaban)) {
                $validJawaban[$soalId] = $jawaban;
            }
        }

        if (!empty($validJawaban)) {
            $this->jawabanModel->saveJawabanCached($ujianId, $peserta['id'], $validJawaban);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }





    /**
     * Simpan status selesai ujian
     */
    public function simpanSelesai()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Akses hanya via AJAX');
        }

        $peserta = session()->get('peserta');
        if (!$peserta) {
            return $this->fail('Peserta belum login');
        }

        $post = $this->request->getPost();
        $ujianId = $post['ujian_id'] ?? null;

        if (!$ujianId) {
            return $this->fail('ID ujian tidak ditemukan');
        }

        // Update status hasil ujian menjadi selesai
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasil) {
            return $this->fail('Data hasil ujian tidak ditemukan');
        }

        if ($hasil['status'] === 'selesai') {
            return $this->fail('Ujian sudah selesai');
        }

        $now = date('Y-m-d H:i:s');

        $this->hasilUjianModel->update($hasil['id'], [
            'status' => 'selesai',
            'waktu_selesai' => $now,
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Ujian selesai']);
    }

    private function fail($message)
    {
        return $this->response->setJSON([
            'status' => false,
            'message' => $message
        ]);
    }


    //========================= KOREKSI UJIAN =====================================================
    public function selesaiUjian($ujianId)
    {
        // 1️⃣ Ambil ID peserta dari POST atau session
        $pesertaId = $this->request->getPost('peserta_id') ?? session('peserta')['id'] ?? null;
        if (!$pesertaId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Peserta belum login atau sesi berakhir.'
            ]);
        }

        // 2️⃣ Ambil hasil ujian aktif (tanpa load kolom berat)
        $hasilUjian = $this->hasilUjianModel
            ->select('id, ujian_id, peserta_id, status')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();

        if (!$hasilUjian) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data hasil ujian tidak ditemukan.'
            ]);
        }

        if ($hasilUjian['status'] !== 'sedang_ujian') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status ujian tidak valid.'
            ]);
        }

        // 3️⃣ Ambil data ujian
        $ujian = $this->ujianModel
            ->select('id, bank_soal_id, nama_ujian, tampil_nilai')
            ->find($ujianId);

        if (!$ujian) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data ujian tidak ditemukan.'
            ]);
        }

        // 4️⃣ Ambil semua jawaban peserta (ringan) dari Redis
        $jawabanPesertaList = array_values($this->jawabanModel->getJawabanCached($ujianId, $pesertaId));

        // 5️⃣ Jalankan koreksi otomatis
        $koreksiService = new \App\Libraries\KoreksiService();
        $koreksi = $koreksiService->koreksiPeserta($ujian, $hasilUjian, $jawabanPesertaList);

        // 6️⃣ Susun data update hasil ujian
        $updateData = [
            'status'         => 'selesai',
            'waktu_selesai'  => date('Y-m-d H:i:s'),
            'nilai_pg'       => $koreksi['nilai'],
            'nilai_total'    => $koreksi['nilai'],
            'poin_benar'     => $koreksi['poin_benar'],
            'poin_salah'     => $koreksi['poin_salah'],
            'poin_maksimal'  => $koreksi['total_bobot'],
            'soal_benar'     => $koreksi['soal_benar'],
            'soal_salah'     => $koreksi['soal_salah'],
        ];

        if (!empty($koreksi['arsip_jawaban'])) {
            $updateData['jawaban_json'] = json_encode(
                $koreksi['arsip_jawaban'],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        // 7️⃣ Update hasil ujian
        $updateSuccess = $this->hasilUjianModel->update($hasilUjian['id'], $updateData);

        // 8️⃣ Hapus jawaban hanya jika update berhasil
        if ($updateSuccess) {
            $this->jawabanModel->deleteJawabanCached($ujianId, $pesertaId);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menyelesaikan ujian. Silakan coba lagi.'
            ]);
        }

        // 9️⃣ Return hasil ke frontend
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Ujian selesai, Semoga hasilnya memuaskan.',
            'nilai' => $koreksi['nilai'],
            'poin_maksimal'   => $koreksi['total_bobot'],
            'poin_benar' => $koreksi['poin_benar'],
            'poin_salah' => $koreksi['poin_salah'],
            'soal_benar' => $koreksi['soal_benar'],
            'soal_salah' => $koreksi['soal_salah'],
            'tampil_nilai' => $ujian['tampil_nilai'],
            'koreksi_detail' => $koreksi['koreksi_detail'] ?? [],
        ]);
    }

    public function koreksiUlang($hasilUjianId)
    {
        try {
            // 1️⃣ Ambil hasil ujian
            $hasilUjian = $this->hasilUjianModel
                ->select('id, ujian_id, peserta_id, status, jawaban_json')
                ->find($hasilUjianId);

            if (!$hasilUjian) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data hasil ujian tidak ditemukan.'
                ]);
            }

            if ($hasilUjian['status'] !== 'selesai') {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Hanya ujian dengan status selesai yang bisa dikoreksi ulang.'
                ]);
            }

            // 2️⃣ Ambil data ujian
            $ujian = $this->ujianModel
                ->select('id, bank_soal_id, nama_ujian, tampil_nilai')
                ->find($hasilUjian['ujian_id']);

            if (!$ujian) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data ujian tidak ditemukan.'
                ]);
            }

            // 3️⃣ Ambil jawaban peserta dari jawaban_json
            $jawabanPesertaList = json_decode($hasilUjian['jawaban_json'], true);
            if (!is_array($jawabanPesertaList)) {
                $jawabanPesertaList = [];
            }

            // 4️⃣ Jalankan koreksi ulang menggunakan koreksiPesertaUlang
            $koreksiService = new \App\Libraries\KoreksiService();
            $koreksi = $koreksiService->koreksiPesertaUlang($ujian, $hasilUjian, $jawabanPesertaList);

            // 5️⃣ Susun data update hasil ujian
            $updateData = [
                'nilai_pg'      => $koreksi['nilai_pg'],
                'nilai_esai'      => $koreksi['nilai_esai'],
                'nilai_total'   => $koreksi['nilai_total'],
                'poin_benar'    => $koreksi['poin_benar'],
                'poin_salah'    => $koreksi['poin_salah'],
                'poin_maksimal' => $koreksi['total_bobot'],
                'soal_benar'    => $koreksi['soal_benar'],
                'soal_salah'    => $koreksi['soal_salah'],
            ];

            // Jika ada arsip jawaban baru, update jawaban_json
            if (!empty($koreksi['arsip_jawaban'])) {
                $updateData['jawaban_json'] = json_encode(
                    $koreksi['arsip_jawaban'],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
            }

            // 6️⃣ Update hasil ujian
            $updateSuccess = $this->hasilUjianModel->update($hasilUjian['id'], $updateData);

            if (!$updateSuccess) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal melakukan koreksi ulang. Silakan coba lagi.'
                ]);
            }

            // 7️⃣ Return hasil ke frontend
            return $this->response->setJSON([
                'status'          => true,
                'message'         => 'Koreksi ulang berhasil.',
                'nilai'           => $koreksi['nilai_total'],
                'poin_maksimal'   => $koreksi['total_bobot'],
                'poin_benar'      => $koreksi['poin_benar'],
                'poin_salah'      => $koreksi['poin_salah'],
                'soal_benar'      => $koreksi['soal_benar'],
                'soal_salah'      => $koreksi['soal_salah'],
                'tampil_nilai'    => $ujian['tampil_nilai'],
                'koreksi_detail'  => $koreksi['koreksi_detail'] ?? [],
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    public function koreksiEssai()
    {
        try {
            // 🔹 Ambil input (mendukung POST biasa maupun JSON)
            $ujianId = $this->request->getVar('ujian_id');
            $pesertaId = $this->request->getVar('peserta_id');
            $soalId = $this->request->getVar('soal_id');
            $nilaiBaru = floatval($this->request->getVar('nilai') ?? $this->request->getVar('poin') ?? 0);

            if (!$ujianId || !$pesertaId || !$soalId) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Parameter tidak lengkap (ujian_id, peserta_id, soal_id, nilai).'
                ]);
            }

            // 🔹 Ambil hasil ujian peserta
            $hasilUjian = $this->hasilUjianModel
                ->where('ujian_id', $ujianId)
                ->where('peserta_id', $pesertaId)
                ->first();

            if (!$hasilUjian) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data hasil ujian tidak ditemukan.'
                ]);
            }

            // 🔹 Ambil data ujian
            $ujian = $this->ujianModel
                ->select('id, bank_soal_id, nama_ujian, tampil_nilai')
                ->find($ujianId);

            if (!$ujian) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data ujian tidak ditemukan.'
                ]);
            }

            // 🔹 Decode jawaban_json secara aman
            $jsonRaw = $hasilUjian['jawaban_json'];
            $jawabanPesertaList = json_decode($jsonRaw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal parse jawaban_json: ' . json_last_error_msg(),
                    'raw_json' => $jsonRaw
                ]);
            }

            if (!is_array($jawabanPesertaList)) {
                $jawabanPesertaList = [];
            }

            if (!isset($jawabanPesertaList[$soalId])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Jawaban untuk soal ini tidak ditemukan di jawaban_json.'
                ]);
            }

            // 🔹 Update nilai esai
            $jawabanPesertaList[$soalId]['poin']     = $nilaiBaru;
            $jawabanPesertaList[$soalId]['is_benar'] = true;

            // 🔹 Jalankan koreksi ulang
            $koreksiService = new \App\Libraries\KoreksiService();
            $koreksi = $koreksiService->koreksiPesertaUlang($ujian, $hasilUjian, $jawabanPesertaList);

            // 🔹 Update hasil ujian
            $updateData = [
                'nilai_pg'      => $koreksi['nilai_pg'],
                'nilai_esai'      => $koreksi['nilai_esai'],
                'nilai_total'   => $koreksi['nilai_total'],
                'poin_benar'    => $koreksi['poin_benar'],
                'poin_salah'    => $koreksi['poin_salah'],
                'poin_maksimal' => $koreksi['total_bobot'],
                'soal_benar'    => $koreksi['soal_benar'],
                'soal_salah'    => $koreksi['soal_salah'],
                'jawaban_json'  => json_encode($jawabanPesertaList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];

            $this->hasilUjianModel->update($hasilUjian['id'], $updateData);

            // 🔹 Kirim respons ke frontend
            return $this->response->setJSON([
                'status'         => true,
                'message'        => 'Nilai esai berhasil diperbarui dan hasil ujian dikoreksi ulang.',
                'soal_id'        => $soalId,
                'nilai_baru'     => $nilaiBaru,
                'nilai_total'    => $koreksi['nilai_total'],
                'poin_maksimal'  => $koreksi['total_bobot'],
                'poin_benar'     => $koreksi['poin_benar'],
                'poin_salah'     => $koreksi['poin_salah'],
                'soal_benar'     => $koreksi['soal_benar'],
                'soal_salah'     => $koreksi['soal_salah'],
                'koreksi_detail' => $koreksi['koreksi_detail'] ?? [],
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[KOREKSI ESAI ERROR] ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    public function selesaiSemua($ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ujian tidak ditemukan']);
        }

        // 🔹 Ambil semua peserta non-Android yang masih "sedang_ujian"
        $pesertaList = $this->hasilUjianModel
            ->select('id, peserta_id')
            ->where('ujian_id', $ujianId)
            ->where('status', 'sedang_ujian')
            ->where('platform !=', 'android')
            ->findAll();

        if (empty($pesertaList)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada peserta aktif']);
        }

        $service = new \App\Libraries\KoreksiService();
        $now = date('Y-m-d H:i:s');
        $totalSelesai = 0;

        // 🔹 Ambil semua jawaban sekaligus agar tidak query per peserta
        $jawabanSemua = $this->jawabanModel
            ->where('ujian_id', $ujianId)
            ->orderBy('peserta_id', 'asc')
            ->findAll();

        // 🔹 Grouping jawaban per peserta_id agar cepat diakses
        $jawabanMap = [];
        foreach ($jawabanSemua as $row) {
            $jawabanMap[$row['peserta_id']][] = $row;
        }

        // 🔹 Siapkan batch update (biar tidak update per satu baris)
        $batchUpdate = [];

        foreach ($pesertaList as $peserta) {
            $pesertaId = $peserta['peserta_id'];
            $jawabanPesertaList = $jawabanMap[$pesertaId] ?? [];

            // kalau gak ada jawaban, skip aja biar gak berat
            if (empty($jawabanPesertaList)) {
                continue;
            }

            $koreksi = $service->koreksiPeserta($ujian, $peserta, $jawabanPesertaList);

            $batchUpdate[] = [
                'id' => $peserta['id'],
                'status' => 'selesai',
                'waktu_selesai' => $now,
                'nilai_pg' => $koreksi['nilai'],
                'nilai_total' => $koreksi['nilai'],
                'poin_benar' => $koreksi['poin_benar'],
                'poin_salah' => $koreksi['poin_salah'],
                'poin_maksimal' => $koreksi['total_bobot'],
                'soal_benar' => $koreksi['soal_benar'],
                'soal_salah' => $koreksi['soal_salah'],
                'jawaban_json' => json_encode($koreksi['arsip_jawaban']),
            ];

            $totalSelesai++;
        }

        // 🔹 Update hasil_ujian sekaligus dalam batch
        if (!empty($batchUpdate)) {
            $this->hasilUjianModel->updateBatch($batchUpdate, 'id');
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "Berhasil menyelesaikan {$totalSelesai} peserta",
            'total' => $totalSelesai
        ]);
    }



    public function hasil($ujianId)
    {
        $peserta = session('peserta');
        if (!$peserta) {
            return redirect()->to(base_url('auth/login'));
        }

        // Ambil data hasil ujian peserta
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasil || $hasil['status'] !== 'selesai') {
            return redirect()->to(base_url('peserta/home'))->with('error', 'Ujian belum diselesaikan.');
        }

        // Ambil data ujian
        $ujian = $this->ujianModel
            ->select('ujian.*, bank_soal.nama as nama_bank_soal')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->find($ujianId);

        // Kalau nilai tidak ditampilkan
        if ($ujian['tampil_nilai'] != 1) {
            return redirect()->to(base_url('peserta/home'))->with('error', 'Nilai ujian belum dapat ditampilkan.');
        }

        // Ambil semua soal + opsi
        $soalList = $this->soalModel->getSoalWithOpsi($ujian['bank_soal_id']);
        $jawabanMap = json_decode($hasil['jawaban_json'], true) ?? [];
        $setting = $this->appSetting();



        // Kirim ke view
        return view('Peserta/ujian_hasil', [
            'setting' => $setting,
            'title' => 'Hasil Ujian',
            'ujian' => $ujian,
            'hasil' => $hasil,
            'soalList' => $soalList,
            'jawabanMap' => $jawabanMap,
            'peserta' => $peserta
        ]);
    }
}
