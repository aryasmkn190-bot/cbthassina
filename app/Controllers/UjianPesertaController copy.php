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
            return $this->response->setJSON(['status' => false, 'message' => 'Belum login.']);
        }

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id']);
        if (isset($validasi['error'])) {
            return $this->response->setJSON(['status' => false, 'message' => $validasi['error']]);
        }

        $ujian = $validasi['ujian'];

        $soal = $this->soalModel->getSoalByBank(
            $ujian['bank_soal_id'],
            $ujian['acak_soal'] == '1',
            $ujian['acak_opsi'] == '1'
        );

        return $this->response->setJSON(['status' => true, 'data' => $soal]);
    }

    public function mulai($ujianId)
    {
        $peserta = session()->get('peserta');
        if (!$peserta) return redirect()->to(base_url('auth/login'));

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id']);

        if (isset($validasi['error'])) {
            return redirect()->to(base_url('peserta/home'))->with('error', $validasi['error']);
        }

        if (isset($validasi['redirect'])) {
            return redirect()->to($validasi['redirect']);
        }

        $ujian = $validasi['ujian'];
        $hasil = $validasi['hasil']; // ambil data hasil ujian
        // 🔥 Pindahkan pengecekan status asli di sini, sebelum nanti hasil['status'] diubah
        $statusUjian = ($validasi['status_awal'] === null || $validasi['status_awal'] === 'belum_mulai') ? 'mulai' : 'lanjut';


        $setting = $this->appSetting();

        $data = [
            'setting' => $setting,
            'title'   => 'Sedang Ujian',
            'ujian'   => $ujian,
            'hasil'   => $hasil, // kirim hasil ke view
            'peserta' => $peserta,
            'ujianId' => $ujianId,
            'status_ujian' => $statusUjian,
        ];

        return view('Peserta/ujian_mulai', $data);
    }

    private function validasiUjianPeserta(string $ujianId, string $pesertaId)
    {
        $ujian = $this->ujianModel
            ->select('ujian.*, bank_soal.nama as nama_bank_soal')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->find($ujianId);

        if (!$ujian) {
            return ['error' => 'Ujian tidak ditemukan.'];
        }

        $now = date('Y-m-d H:i:s');
        if ($now < $ujian['waktu_mulai']) {
            return ['error' => 'Ujian belum dimulai.'];
        }
        if ($now > $ujian['waktu_selesai']) {
            return ['error' => 'Waktu ujian sudah berakhir.'];
        }

        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();
        // Jika hasil belum ada tapi token dianggap valid → hapus validasi token dari session
        if ($ujian['pakai_token'] == '1' && !$hasil['token_valid']) {
            return ['error' => 'Token belum diverifikasi.'];
        }
        if (!$hasil) {
            return ['error' => 'Data hasil ujian belum tersedia untuk peserta ini.'];
        }

        if ($hasil['status'] === 'selesai') {
            return ['error' => 'Kamu sudah menyelesaikan ujian ini.'];
        }
        $statusSebelum = $hasil['status']; // simpan dulu sebelum diubah

        if ($statusSebelum !== 'sedang_ujian') {
            $this->hasilUjianModel->update($hasil['id'], [
                'status' => 'sedang_ujian',
                'waktu_mulai' => $now
            ]);
            $hasil['status'] = 'sedang_ujian';
            $hasil['waktu_mulai'] = $now;
        }

        return [
            'ujian' => $ujian,
            'hasil' => $hasil,
            'status_awal' => $statusSebelum // <== ini penting
        ];
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

        // Cek apakah ini dari admin (POST dengan peserta_id) atau peserta (session)
        $pesertaId = $this->request->getPost('peserta_id');

        if (!$pesertaId) {
            // fallback ke session jika tidak ada input peserta_id
            $peserta = session()->get('peserta');
            if (!$peserta) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Belum login dan tidak ada peserta_id'
                ]);
            }
            $pesertaId = $peserta['id'];
        }

        // Ambil data hasil ujian
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();

        if (!$hasil) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data hasil tidak ditemukan'
            ]);
        }

        if ($hasil['status'] === 'selesai') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Ujian sudah diselesaikan sebelumnya'
            ]);
        }

        if ($hasil['status'] !== 'sedang_ujian') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status ujian bukan sedang_ujian, tidak bisa diselesaikan.'
            ]);
        }

        // PROSES KOREKSI

        // Ambil semua jawaban peserta
        $jawabanList = $this->jawabanModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId) // Tambahkan ini untuk keamanan
            ->findAll();

        $totalPoin = 0;          // Total bobot yang diperoleh peserta
        $jumlahBenar = 0;        // Jumlah soal yang dijawab benar (jika pakai penilaian benar/salah)
        $total = count($jawabanList);
        $hasilKoreksi = [];

        foreach ($jawabanList as $j) {
            $jawaban = json_decode($j['jawaban'], true);
            $soal = $this->soalModel->find($j['soal_id']);
            if (!$soal) continue;

            $jenis = $soal['jenis_soal'];
            $opsi = $this->soalOpsiModel
                ->where('soal_id', $soal['id'])
                ->findAll();

            $isCorrect = false;
            $bobot = 0;

            switch ($jenis) {
                case 'pg':
                    $kunci = array_filter($opsi, fn($o) => isset($o['is_true']) && $o['is_true']);
                    $kunciVal = $kunci ? array_values($kunci)[0]['label'] ?? null : null;
                    $isCorrect = isset($jawaban['value']) && $jawaban['value'] == $kunciVal;

                    // Ambil bobot dari jawaban peserta
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiTerpilih = array_filter($opsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot = $opsiTerpilih ? (int) array_values($opsiTerpilih)[0]['bobot'] : 0;
                    break;

                case 'mpg':
                    $kunci = array_column(array_filter($opsi, fn($o) => $o['is_true']), 'label');
                    sort($kunci);
                    $jawab = $jawaban['values'] ?? [];
                    sort($jawab);
                    $isCorrect = $jawab === $kunci;
                    $bobot = $isCorrect ? 1 : 0; // Atur bobot tetap 1 jika benar
                    break;

                case 'benar_salah':
                    $kunci = [];
                    foreach ($opsi as $o) {
                        $kunci[$o['label']] = $o['is_true'] ? 'Benar' : 'Salah';
                    }
                    $isCorrect = $jawaban == $kunci;
                    $bobot = $isCorrect ? 1 : 0;
                    break;

                case 'jodohkan':
                    $kunci = [];
                    foreach ($opsi as $o) {
                        $kunci[$o['label']] = $o['pasangan'];
                    }
                    $isCorrect = $jawaban == $kunci;
                    $bobot = $isCorrect ? 1 : 0;
                    break;

                case 'esai':
                case 'isian':
                    $isCorrect = false;
                    $bobot = 0;
                    break;
            }

            // Simpan hasil koreksi untuk debug
            $hasilKoreksi[] = [
                'soal_id' => $j['soal_id'],
                'jenis' => $jenis,
                'jawaban' => $jawaban,
                'is_benar' => $isCorrect,
                'bobot_diperoleh' => $bobot,
            ];

            $totalPoin += $bobot;
            if ($isCorrect) $jumlahBenar++;
        }

        // Ambil data ujian untuk dapatkan bank_soal_id
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Ujian tidak ditemukan.'
            ]);
        }

        $bankSoalId = $ujian['bank_soal_id'];

        // Ambil semua soal dari bank soal tersebut
        $soalUjianList = $this->soalModel
            ->where('bank_soal_id', $bankSoalId)
            ->findAll();

        $totalBobotMaks = 0;
        foreach ($soalUjianList as $soal) {
            $opsi = $this->soalOpsiModel->where('soal_id', $soal['id'])->findAll();
            foreach ($opsi as $o) {
                if (!empty($o['is_true'])) {
                    $totalBobotMaks += (int) $o['bobot'];
                }
            }
        }


        $nilai = ($totalBobotMaks > 0) ? round(($totalPoin / $totalBobotMaks) * 100, 2) : 0;
        $salah = $totalBobotMaks - $jumlahBenar;

        $jawabanArsip = [];

        foreach ($jawabanList as $j) {
            $jawabanArsip[$j['soal_id']] = json_decode($j['jawaban'], true);
        }
        // Update ke database hasil_ujian
        $this->hasilUjianModel->update($hasil['id'], [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'nilai_pg' => $nilai,
            'nilai_total' => $nilai,
            'total_poin' => $totalPoin,
            'benar' => $jumlahBenar,
            'salah' => $salah,
            'jawaban_json' => json_encode($jawabanArsip),
        ]);

        // Balikan response JSON
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Ujian Telah Selesai..',
            'jumlah_benar' => $jumlahBenar,
            'jumlah_soal' => $total,
            'total_poin' => $totalPoin,
            'total_bobot_maks' => $totalBobotMaks,
            'nilai' => $nilai,
            'koreksi_detail' => $hasilKoreksi
        ]);
    }
}
