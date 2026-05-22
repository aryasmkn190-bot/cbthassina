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
        $data = [
            'setting' => $setting,
            'title'   => 'Daftar Ujian Saya',
        ];
        return view('Peserta/ujian', $data);
    }
    public function getAllUjian()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Hanya bisa diakses via AJAX.');
        }

        $pesertaId = session()->get('peserta')['id'];
        $data = $this->ujianModel->getAllPeserta($pesertaId);

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
            $hasil = $this->hasilUjianModel
                ->where('ujian_id', $ujian['id'])
                ->where('peserta_id', $pesertaId)
                ->first();

            if ($hasil) {
                if ($hasil['status'] === 'selesai') {
                    $ujian['status_peserta'] = 'selesai';
                } elseif ($hasil['status'] === 'sedang_ujian') {
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

        // Ambil data ujian tanpa validasi status atau token
        $ujian = $this->ujianModel
            ->select('ujian.*, bank_soal.nama as nama_bank_soal')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->find($ujianId);

        if (!$ujian) {
            return $this->fail('Ujian tidak ditemukan.');
        }

        // Ambil hasil ujian peserta
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasil) {
            return $this->fail('Hasil ujian peserta tidak ditemukan.');
        }

        return $this->response->setJSON([
            'status' => true,
            'ujian'  => $ujian,
            'hasil'  => $hasil,
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

        // Gunakan mode 'getsoal' agar tidak mengubah status atau waktu ujian
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

        $soal = $this->soalModel->getSoalByUrutan($soalIds, $opsiUrutan);

        // Hilangkan kunci jawaban
        foreach ($soal as &$s) {
            foreach ($s['opsi'] as &$op) {
                unset($op['is_true']);
            }
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

        $jawabanModel = new JawabanModel();
        $rows = $jawabanModel->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->findAll();

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

        foreach ($jawabanInput as $soalId => $jawaban) {
            // Pastikan jawaban selalu dalam bentuk JSON object
            if (!is_array($jawaban)) {
                continue; // skip jika tidak valid
            }

            $data = [
                'id' => Uuid::uuid4()->toString(),
                'ujian_id' => $ujianId,
                'peserta_id' => $peserta['id'],
                'soal_id' => $soalId,
                'jawaban' => json_encode($jawaban, JSON_UNESCAPED_UNICODE),
                'skor' => 0, // skor awal 0, dinilai nanti
            ];

            // Gunakan save atau replace tergantung strategi penyimpanan
            $this->jawabanModel->saveJawaban($data);
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
        $pesertaId = $this->request->getPost('peserta_id') ?? session('peserta')['id'] ?? null;
        if (!$pesertaId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Belum login']);
        }

        $hasilUjian = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();

        if (!$hasilUjian || $hasilUjian['status'] !== 'sedang_ujian') {
            return $this->response->setJSON(['status' => false, 'message' => 'Status tidak valid']);
        }

        $ujian = $this->ujianModel->find($ujianId);
        $jawabanPesertaList = $this->jawabanModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->findAll();

        $koreksi = (new \App\Libraries\KoreksiService())->koreksiPeserta($ujian, $hasilUjian, $jawabanPesertaList);

        $this->hasilUjianModel->update($hasilUjian['id'], [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'nilai_pg' => $koreksi['nilai'],
            'nilai_total' => $koreksi['nilai'],
            'poin_benar' => $koreksi['poin_benar'],
            'poin_salah' => $koreksi['poin_salah'],
            'poin_maksimal' => $koreksi['total_bobot'],
            'soal_benar' => $koreksi['soal_benar'],
            'soal_salah' => $koreksi['soal_salah'],
            'jawaban_json' => json_encode($koreksi['arsip_jawaban']),
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Ujian selesai',
            'nilai' => $koreksi['nilai'],
            'koreksi_detail' => $koreksi['koreksi_detail']
        ]);
    }

    public function selesaiSemua($ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ujian tidak ditemukan']);
        }

        $pesertaList = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('status', 'sedang_ujian')
            ->where('platform !=', 'android') // 🔹 hanya non-android
            ->findAll();

        if (!$pesertaList) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada peserta aktif']);
        }

        $service = new \App\Libraries\KoreksiService();
        $count = 0;

        foreach ($pesertaList as $peserta) {
            $jawabanPesertaList = $this->jawabanModel
                ->where('ujian_id', $ujianId)
                ->where('peserta_id', $peserta['peserta_id'])
                ->findAll();

            $koreksi = $service->koreksiPeserta($ujian, $peserta, $jawabanPesertaList);

            $this->hasilUjianModel->update($peserta['id'], [
                'status' => 'selesai',
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'nilai_pg' => $koreksi['nilai'],
                'nilai_total' => $koreksi['nilai'],
                'poin_benar' => $koreksi['poin_benar'],
                'poin_salah' => $koreksi['poin_salah'],
                'poin_maksimal' => $koreksi['total_bobot'],
                'soal_benar' => $koreksi['soal_benar'],
                'soal_salah' => $koreksi['soal_salah'],
                'jawaban_json' => json_encode($koreksi['arsip_jawaban']),
            ]);

            $count++;
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "Berhasil menyelesaikan {$count} peserta"
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
