<?php

namespace App\Controllers;

use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\HasilUjianModel;
use Ramsey\Uuid\Uuid;
use App\Models\JawabanModel;

class UjianShareController extends BaseController
{
    protected $ujianModel;
    protected $hasilUjianModel;
    protected $soalModel;
    protected $soalOpsiModel;
    protected $jawabanModel;

    public function __construct()
    {
        $this->ujianModel      = new UjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->soalModel       = new SoalModel();
        $this->soalOpsiModel   = new SoalOpsiModel();
        $this->jawabanModel    = new JawabanModel();
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }


    public function play($kode = null)
    {
        $ujian = $this->ujianModel->where('token', $kode)->first();
        if (!$ujian || $ujian['dibagikan'] != 1) {
            return redirect()->to(base_url('peserta/home'))
                ->with('error', 'Ujian tidak tersedia untuk dibagikan.');
        }

        // Kalau butuh login tapi belum login
        if ($ujian['butuh_login'] == 1 && !session()->get('peserta')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peserta = session()->get('peserta');
        $pesertaId = $peserta['id'] ?? null;

        // Guest mode → pakai cookie/session ID
        $guestId = $this->request->getCookie('guest_id') ?? session()->get('guest_id');
        if (!$peserta && $ujian['butuh_login'] == 0) {
            if (!$guestId) {
                $guestId = Uuid::uuid4()->toString();
                session()->set('guest_id', $guestId);
                setcookie('guest_id', $guestId, time() + (86400 * 30), "/");
            }
        }

        // Cek hasil ujian existing
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujian['id'])
            ->groupStart()
            ->where('peserta_id', $pesertaId)
            ->orWhere('guest_id', $guestId)
            ->groupEnd()
            ->first();

        // Generate token sekali pakai
        $startToken = bin2hex(random_bytes(16));
        session()->set('start_token_' . $ujian['id'], $startToken);

        $data = [
            'setting'    => $this->appSetting(),
            'title'      => 'Konfirmasi Ujian',
            'ujian'      => $ujian,
            'peserta'    => $peserta,
            'guestId'    => $guestId,
            'hasil'      => $hasil, // bisa null kalau belum pernah ikut
            'startToken' => $startToken
        ];

        return view('Peserta/ujian_confirm', $data);
    }

    /**
     * Mulai atau ulangi ujian
     */
    public function mulai($ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian || $ujian['dibagikan'] != 1) {
            return redirect()->to(base_url('peserta/home'))
                ->with('error', 'Ujian tidak tersedia.');
        }

        // 🔹 Cek token
        $token = $this->request->getGet('token');
        $sessionToken = session()->get('start_token_' . $ujianId);

        if (!$token || $token !== $sessionToken) {
            return redirect()->to(base_url('share/ujian/play/' . $ujian['token']))
                ->with('error', 'Silakan konfirmasi ujian terlebih dahulu.');
        }

        // Hapus token agar tidak bisa di-reuse
        session()->remove('start_token_' . $ujianId);

        $peserta   = session()->get('peserta');
        $pesertaId = $peserta['id'] ?? null;
        $guestId   = $this->request->getCookie('guest_id') ?? session()->get('guest_id');

        // 🔹 Jika sudah pernah ikut, hapus dulu kalau ingin ulang
        $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->groupStart()
            ->where('peserta_id', $pesertaId)
            ->orWhere('guest_id', $guestId)
            ->groupEnd()
            ->delete();

        // Ambil & acak soal
        $soal = $this->soalModel
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->findAll();

        $soalIds = array_column($soal, 'id');
        shuffle($soalIds);

        $opsiUrutan = [];
        foreach ($soalIds as $soalId) {
            $opsi = $this->soalOpsiModel
                ->where('soal_id', $soalId)
                ->findAll();

            $opsiIds = array_column($opsi, 'id');
            shuffle($opsiIds);
            $opsiUrutan[$soalId] = $opsiIds;
        }

        // Insert hasil baru
        $hasilId = $this->hasilUjianModel->insert([
            'id'          => Uuid::uuid4()->toString(),
            'ujian_id'    => $ujianId,
            'peserta_id'  => $pesertaId,
            'guest_id'    => $guestId,
            'urutan_soal' => json_encode($soalIds),
            'urutan_opsi' => json_encode($opsiUrutan),
            'status'      => 'sedang_ujian',
            'waktu_mulai' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('status_ujian', 'mulai');
        return redirect()->to(base_url('share/ujian/lanjut/' . $ujianId));
    }

    public function lanjut($ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian || $ujian['dibagikan'] != 1) {
            return redirect()->to(base_url('peserta/home'))
                ->with('error', 'Ujian tidak tersedia.');
        }

        $peserta = session()->get('peserta');
        $pesertaId = $peserta['id'] ?? null;

        // Ambil guest_id dari cookie atau session
        $guestId = $this->request->getCookie('guest_id') ?? session()->get('guest_id');

        // Cari hasil ujian (peserta atau guest)
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->groupStart()
            ->where('peserta_id', $pesertaId)
            ->orWhere('guest_id', $guestId)
            ->groupEnd()
            ->first();

        if (!$hasil) {
            return redirect()->to(base_url('peserta/home'))
                ->with('error', 'Data hasil ujian belum tersedia.');
        }

        // 🔹 Cek status ujian, kalau sudah selesai arahkan ke halaman konfirmasi/nilai
        if ($hasil['status'] === 'selesai') {
            return redirect()->to(base_url('share/ujian/play/' . $ujian['token']))
                ->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }

        $data = [
            'title'        => 'Lanjut Ujian',
            'setting'      => $this->appSetting(),
            'ujian'        => $ujian,
            'hasil'        => $hasil,
            'peserta'      => $peserta,
            'guestId'      => $guestId,
            'ujianId'      => $ujianId,
            'status_ujian' => session()->getFlashdata('status_ujian') ?? 'lanjut',
        ];

        return view('Peserta/ujian_guest', $data);
    }




    public function getSoal($ujianId)
    {
        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $peserta['id'] ?? null;

        if (!$pesertaId && !$guestId) {
            return $this->fail('Peserta tidak terdeteksi (login/guest).');
        }

        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->groupStart()
            ->where('peserta_id', $pesertaId)
            ->orWhere('guest_id', $guestId)
            ->groupEnd()
            ->first();

        if (!$hasil) {
            return $this->fail('Hasil ujian tidak ditemukan.');
        }

        $soalIds    = json_decode($hasil['urutan_soal'], true);
        $opsiUrutan = json_decode($hasil['urutan_opsi'], true);

        if (!is_array($soalIds) || !is_array($opsiUrutan)) {
            return $this->fail('Data urutan soal atau opsi tidak valid.');
        }

        $soal = $this->soalModel->getSoalByUrutanFinal($soalIds, $opsiUrutan);

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

    public function apiGetUjian($ujianId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Hanya bisa diakses via AJAX.');
        }

        $ujian = $this->ujianModel
            ->select('ujian.*, bank_soal.nama as nama_bank_soal')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->find($ujianId);

        if (!$ujian) {
            return $this->fail('Ujian tidak ditemukan.');
        }

        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $peserta['id'] ?? null;

        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->groupStart()
            ->where('peserta_id', $pesertaId)
            ->orWhere('guest_id', $guestId)
            ->groupEnd()
            ->first();

        if (!$hasil) {
            return $this->fail('Hasil ujian belum tersedia.');
        }

        return $this->response->setJSON([
            'status' => true,
            'ujian'  => $ujian,
            'hasil'  => $hasil,
        ]);
    }
    public function simpanJawaban()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Akses hanya via AJAX');
        }

        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $peserta['id'] ?? null;

        if (!$pesertaId && !$guestId) {
            return $this->fail('Peserta atau Guest belum terdeteksi');
        }

        $post = $this->request->getPost();
        $ujianId = $post['ujian_id'] ?? null;
        $jawabanInput = $post['jawaban'] ?? null;

        if (!$ujianId || !is_array($jawabanInput)) {
            return $this->fail('Data tidak lengkap atau salah format');
        }

        foreach ($jawabanInput as $soalId => $jawaban) {
            if (!is_array($jawaban)) {
                continue;
            }

            $data = [
                'id'         => Uuid::uuid4()->toString(),
                'ujian_id'   => $ujianId,
                'peserta_id' => $pesertaId,
                'guest_id'   => $guestId,
                'soal_id'    => $soalId,
                'jawaban'    => json_encode($jawaban, JSON_UNESCAPED_UNICODE),
                'skor'       => 0,
            ];

            $this->jawabanModel->saveJawaban($data);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }

    public function getJawabanPeserta($ujianId)
    {
        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $peserta['id'] ?? null;

        if (!$pesertaId && !$guestId) {
            return $this->fail('Peserta atau Guest belum terdeteksi');
        }

        $jawabanModel = new JawabanModel();
        $rows = $jawabanModel->where('ujian_id', $ujianId);

        if ($pesertaId) {
            $rows = $rows->where('peserta_id', $pesertaId);
        } else {
            $rows = $rows->where('guest_id', $guestId);
        }

        $rows = $rows->findAll();

        $data = [];
        foreach ($rows as $row) {
            $rawJawaban = $row['jawaban'];
            $decoded = json_decode($rawJawaban, true);

            $data[$row['soal_id']] = (json_last_error() === JSON_ERROR_NONE)
                ? $decoded
                : $rawJawaban;
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function simpanSelesai()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Akses hanya via AJAX');
        }

        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $peserta['id'] ?? null;

        if (!$pesertaId && !$guestId) {
            return $this->fail('Peserta atau Guest belum terdeteksi');
        }

        $post = $this->request->getPost();
        $ujianId = $post['ujian_id'] ?? null;

        if (!$ujianId) {
            return $this->fail('ID ujian tidak ditemukan');
        }

        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId);

        if ($pesertaId) {
            $hasil = $hasil->where('peserta_id', $pesertaId);
        } else {
            $hasil = $hasil->where('guest_id', $guestId);
        }

        $hasil = $hasil->first();

        if (!$hasil) {
            return $this->fail('Data hasil ujian tidak ditemukan');
        }

        if ($hasil['status'] === 'selesai') {
            return $this->fail('Ujian sudah selesai');
        }

        $now = date('Y-m-d H:i:s');

        $this->hasilUjianModel->update($hasil['id'], [
            'status'        => 'selesai',
            'waktu_selesai' => $now,
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Ujian selesai'
        ]);
    }

    public function selesaiUjian($ujianId)
    {
        $peserta   = session()->get('peserta');
        $guestId   = session()->get('guest_id');
        $pesertaId = $this->request->getPost('peserta_id') ?? ($peserta['id'] ?? null);

        if (!$pesertaId && !$guestId) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Belum login dan tidak ada peserta_id / guest_id'
            ]);
        }

        // Ambil hasil ujian sesuai mode
        $hasilUjian = $this->hasilUjianModel
            ->where('ujian_id', $ujianId);

        if ($pesertaId) {
            $hasilUjian = $hasilUjian->where('peserta_id', $pesertaId);
        } else {
            $hasilUjian = $hasilUjian->where('guest_id', $guestId);
        }

        $hasilUjian = $hasilUjian->first();

        if (!$hasilUjian || $hasilUjian['status'] !== 'sedang_ujian') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data hasil tidak ditemukan atau status tidak valid'
            ]);
        }

        $ujian = $this->ujianModel->find($ujianId);
        $daftarSoal = $this->soalModel->where('bank_soal_id', $ujian['bank_soal_id'])->findAll();

        // Ambil jawaban sesuai mode
        $jawabanPesertaList = $this->jawabanModel
            ->where('ujian_id', $ujianId);

        if ($pesertaId) {
            $jawabanPesertaList = $jawabanPesertaList->where('peserta_id', $pesertaId);
        } else {
            $jawabanPesertaList = $jawabanPesertaList->where('guest_id', $guestId);
        }

        $jawabanPesertaList = $jawabanPesertaList->findAll();

        // Validasi durasi minimal
        $waktuMulai = strtotime($hasilUjian['waktu_mulai']);
        $waktuSekarang = time();
        $durasiMenit = ($waktuSekarang - $waktuMulai) / 60;
        $minimalDurasi = (int) ($ujian['minimal_durasi'] ?? 0);

        if ($minimalDurasi > 0 && $durasiMenit < $minimalDurasi) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => "Belum saatnya menyelesaikan ujian. Silakan baca/pelajari soal lebih lama."
            ]);
        }

        // Mapping jawaban peserta
        $jawabanMap = [];
        foreach ($jawabanPesertaList as $jawaban) {
            $jawabanMap[$jawaban['soal_id']] = json_decode($jawaban['jawaban'], true);
        }

        $totalPoinBenar   = 0;
        $totalBobotMaks   = 0;
        $jumlahSoalBenar  = 0;
        $koreksiPerSoal   = [];
        $arsipJawaban     = [];

        foreach ($daftarSoal as $soal) {
            $soalId     = $soal['id'];
            $jenis      = $soal['jenis_soal'];
            $daftarOpsi = $this->soalOpsiModel->where('soal_id', $soalId)->findAll();
            $jawaban    = $jawabanMap[$soalId] ?? null;

            $skorDiperoleh = 0;
            $isBenar = false;

            switch ($jenis) {
                case 'pg':
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiDipilih  = array_filter($daftarOpsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot        = $opsiDipilih ? (int) array_values($opsiDipilih)[0]['bobot'] : 0;
                    $skorDiperoleh = $bobot;

                    $opsiBenar = array_filter($daftarOpsi, fn($o) => !empty($o['is_true']));
                    $labelBenar = $opsiBenar ? array_values($opsiBenar)[0]['label'] ?? null : null;
                    $isBenar = $labelJawaban === $labelBenar;
                    break;

                case 'mpg':
                    $jawabanList = $jawaban['values'] ?? [];
                    foreach ($jawabanList as $label) {
                        $opsi = array_values(array_filter($daftarOpsi, fn($o) => $o['label'] === $label));
                        if ($opsi) {
                            $skorDiperoleh += (int) $opsi[0]['bobot'];
                        }
                    }
                    $labelBenar = array_column(array_filter($daftarOpsi, fn($o) => !empty($o['is_true'])), 'label');
                    sort($labelBenar);
                    $labelJawaban = $jawabanList;
                    sort($labelJawaban);
                    $isBenar = $labelBenar === $labelJawaban;
                    break;

                case 'benar_salah':
                    $skorDiperoleh = 0;
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $label = $opsi['label'];
                        $jawabanPeserta = $jawaban[$label] ?? null;
                        $jawabanBenar   = $opsi['is_true'] ? 'Benar' : 'Salah';
                        $bobot          = (int) $opsi['bobot'];
                        if ($jawabanPeserta === $jawabanBenar) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'jodohkan':
                    $skorDiperoleh = 0;
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $jawab = $jawaban[$opsi['label']] ?? null;
                        $bobot = (int) $opsi['bobot'];
                        if ($jawab === $opsi['pasangan']) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'esai':
                case 'isian':
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;
                    $kunciJawaban = json_decode($soal['jawaban'], true) ?? [];
                    $isBenar = $this->cocokIsian($jawabanUser, $kunciJawaban);
                    $skorDiperoleh = $isBenar ? (int) $soal['bobot'] : 0;
                    $totalBobotMaks += (int) $soal['bobot'];
                    break;
            }

            // Tambah bobot maksimal
            if ($jenis === 'jodohkan' || $jenis === 'benar_salah') {
                foreach ($daftarOpsi as $opsi) {
                    if ((int) $opsi['bobot'] > 0) $totalBobotMaks += (int) $opsi['bobot'];
                }
            } else {
                foreach ($daftarOpsi as $opsi) {
                    if (!empty($opsi['is_true']) && (int) $opsi['bobot'] > 0) {
                        $totalBobotMaks += (int) $opsi['bobot'];
                    }
                }
            }

            $totalPoinBenar += $skorDiperoleh;
            if ($isBenar) $jumlahSoalBenar++;

            $koreksiPerSoal[] = [
                'soal_id'        => $soalId,
                'jenis'          => $jenis,
                'jawaban'        => $jawaban,
                'is_benar'       => $isBenar,
                'bobot_diperoleh' => $skorDiperoleh
            ];

            if ($jawaban !== null) {
                $arsipJawaban[$soalId] = is_array($jawaban)
                    ? array_merge($jawaban, [
                        'is_benar' => $isBenar,
                        'poin'     => $skorDiperoleh
                    ])
                    : [
                        'value'    => $jawaban,
                        'is_benar' => $isBenar,
                        'poin'     => $skorDiperoleh
                    ];
            }
        }

        $nilaiAkhir      = $totalBobotMaks > 0 ? round(($totalPoinBenar / $totalBobotMaks) * 100, 2) : 0;
        $jumlahSoalSalah = count($daftarSoal) - $jumlahSoalBenar;
        $totalPoinSalah  = $totalBobotMaks - $totalPoinBenar;

        $this->hasilUjianModel->update($hasilUjian['id'], [
            'status'        => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'nilai_pg'      => $nilaiAkhir,
            'nilai_total'   => $nilaiAkhir,
            'poin_benar'    => $totalPoinBenar,
            'poin_salah'    => $totalPoinSalah,
            'poin_maksimal' => $totalBobotMaks,
            'soal_benar'    => $jumlahSoalBenar,
            'soal_salah'    => $jumlahSoalSalah,
            'jawaban_json'  => json_encode($arsipJawaban),
        ]);

        // 🔥 Hapus jawaban peserta/guest dari tabel jawaban
        $deleteQuery = $this->jawabanModel->where('ujian_id', $ujianId);
        if ($pesertaId) {
            $deleteQuery->where('peserta_id', $pesertaId);
        } else {
            $deleteQuery->where('guest_id', $guestId);
        }
        $deleteQuery->delete();

        return $this->response->setJSON([
            'status'        => true,
            'message'       => 'Ujian Telah Selesai.',
            'jumlah_soal'   => count($daftarSoal),
            'poin_benar'    => $totalPoinBenar,
            'poin_salah'    => $totalPoinSalah,
            'poin_maksimal' => $totalBobotMaks,
            'soal_benar'    => $jumlahSoalBenar,
            'soal_salah'    => $jumlahSoalSalah,
            'nilai'         => $nilaiAkhir,
            'koreksi_detail' => $koreksiPerSoal,
            'tampil_nilai'  => $ujian['tampil_nilai']
        ]);
    }

    protected function cocokIsian(string $jawabanUser, array $kunciJawaban): bool
    {
        $normalizedUser = $this->normalizeTextAdvanced($jawabanUser);

        foreach ($kunciJawaban as $kunci) {
            if ($normalizedUser === $this->normalizeTextAdvanced($kunci)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeTextAdvanced(?string $text): string
    {
        $text = strtolower(trim($text ?? ''));

        // Hilangkan semua tanda baca (kecuali huruf dan angka)
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Ganti spasi ganda atau tab dengan satu spasi
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }
}
