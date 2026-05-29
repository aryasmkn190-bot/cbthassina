<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UjianModel;
use App\Models\HasilUjianModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\JawabanModel;
use App\Models\PesertaModel;
use Ramsey\Uuid\Uuid;

class UjianPesertaApiController extends ResourceController
{
    protected $ujianModel;
    protected $hasilUjianModel;
    protected $pesertaModel;
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
        $this->pesertaModel = new PesertaModel();
    }



    protected function failResponse($message)
    {
        return $this->respond(['success' => false, 'message' => $message, 'data' => null]);
    }

    public function cekToken()
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized($peserta['message']);
        }

        $ujianId = $this->request->getVar('ujian_id');
        $inputToken = $this->request->getVar('token');

        if (!$ujianId || !$inputToken) {
            return $this->fail('Ujian ID dan token wajib diisi.');
        }

        $ujian = $this->ujianModel
            ->select('id, pakai_token, token, nama_ujian')
            ->find($ujianId);
        if (!$ujian) {
            return $this->failNotFound('Ujian tidak ditemukan.');
        }

        if (empty($ujian['pakai_token']) || !$ujian['pakai_token']) {
            return $this->respond(['success' => true, 'message' => 'Ujian tidak memerlukan token.', 'data' => null]);
        }

        $tokenBenar = strtoupper(trim($ujian['token']));
        $tokenInput = strtoupper(trim($inputToken));

        if ($tokenBenar !== $tokenInput) {
            return $this->failValidationErrors('Token tidak valid.');
        }

        $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->set(['token_valid' => 1])
            ->update();

        return $this->respond(['success' => true, 'message' => 'Token valid.', 'data' => null]);
    }


    public function mulaiUjian()
    {
        $ujianId = $this->request->getVar('ujian_id');
        $platform = $this->request->getVar('platform');     // "android"
        $deviceId = $this->request->getVar('device_id');    // device unik

        if (!$ujianId) {
            return $this->failValidationErrors('Ujian ID wajib diisi.');
        }

        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid !');
        }

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'mulai');
        if (isset($validasi['error'])) {
            return $this->fail(['message' => $validasi['error']]);
        }
        if (!empty($deviceId)) {

            $this->hasilUjianModel->update($validasi['hasil']['id'], [
                'platform' => $platform,
                'device_id' => $deviceId
            ]);
        }
        return $this->respond([
            'success' => true,
            'message' => 'Ujian dimulai.',
            'data' => [
                'status_ujian' => 'mulai',
                'hasil' => $validasi['hasil'],
                'ujian' => $validasi['ujian'],
            ]
        ]);
    }



    public function lanjutUjian()
    {
        $ujianId = $this->request->getVar('ujian_id');
        $deviceIdRequest = $this->request->getVar('device_id'); // device_id dari client
        if (!$ujianId) {
            return $this->failValidationErrors('Ujian ID wajib diisi.');
        }

        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid !');
        }

        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'lanjut');
        if (isset($validasi['error'])) {
            return $this->fail(['message' => $validasi['error']]);
        }

        $hasil = $validasi['hasil'];
        $ujian = $validasi['ujian'];

        if (!empty($deviceIdRequest) && isset($ujian['single_login']) && $ujian['single_login'] == 1) {
            if (!empty($validasi['hasil']['device_id']) && $validasi['hasil']['device_id'] !== $deviceIdRequest) {
                return $this->fail(['message' => 'Perangkat berbeda, tidak bisa melanjutkan ujian, Silahkan minta Reset Perangkat ke Panitia Ujian.']);
            }

            if (empty($validasi['hasil']['device_id'])) {

                $this->hasilUjianModel->update($validasi['hasil']['id'], [
                    'device_id' => $deviceIdRequest
                ]);
                $validasi['hasil']['device_id'] = $deviceIdRequest;
            }
        }


        return $this->respond([
            'success' => true,
            'message' => 'Lanjut ujian.',
            'data' => [
                'status_ujian' => 'lanjut',
                'ujian' => $ujian,
                'hasil' => $hasil,
                'peserta' => $peserta,
            ]
        ]);
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
            ->select('id, waktu_mulai, status, token_valid, urutan_soal, urutan_opsi')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $pesertaId)
            ->first();

        if (!$hasil) return ['error' => 'Data hasil ujian belum tersedia untuk peserta ini.'];
        if ($hasil['status'] === 'selesai') return ['error' => 'Kamu sudah menyelesaikan ujian ini.'];

        if ($ujian['pakai_token'] == '1' && !$hasil['token_valid']) {
            return ['error' => 'Token belum diverifikasi.'];
        }

        if ($mode === 'mulai' && $hasil['status'] === 'sedang_ujian') {
            return ['error' => 'Ujian sudah dimulai sebelumnya.'];
        }

        if ($mode === 'lanjut' && $hasil['status'] !== 'sedang_ujian') {
            return ['error' => 'Ujian belum dimulai.'];
        }

        $perluUpdate = [];
        $statusSebelum = $hasil['status'];

        $soalList = json_decode($hasil['urutan_soal'] ?? '[]', true);
        if (!is_array($soalList) || count($soalList) === 0) {
            $soalList = $this->soalModel->getSoalIdsByBank($ujian['bank_soal_id']);
            if ($ujian['acak_soal'] == '1') shuffle($soalList);
            $perluUpdate['urutan_soal'] = json_encode($soalList);
            $hasil['urutan_soal'] = $perluUpdate['urutan_soal'];
        }

        $opsiMap = json_decode($hasil['urutan_opsi'] ?? '{}', true);
        if (!is_array($opsiMap) || count($opsiMap) === 0) {
            $opsiMap = $this->soalModel->getOpsiOrderMap($ujian['bank_soal_id'], $ujian['acak_opsi'] == '1');
            $perluUpdate['urutan_opsi'] = json_encode($opsiMap);
            $hasil['urutan_opsi'] = $perluUpdate['urutan_opsi'];
        }

        if ($mode === 'mulai' && $statusSebelum !== 'sedang_ujian') {
            $perluUpdate['status'] = 'sedang_ujian';
            $perluUpdate['waktu_mulai'] = $now;
            $hasil['status'] = 'sedang_ujian';
            $hasil['waktu_mulai'] = $now;
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
    public function soal($ujianId)
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid!');
        }

        // Validasi status ujian peserta
        $validasi = $this->validasiUjianPeserta($ujianId, $peserta['id'], 'getsoal');
        if (isset($validasi['error'])) {
            return $this->respond([
                'success' => false,
                'message' => $validasi['error'],
                'data' => null
            ]);
        }

        $hasil = $validasi['hasil'];
        $soalIds = json_decode($hasil['urutan_soal'], true);
        $opsiUrutan = json_decode($hasil['urutan_opsi'], true);

        if (!is_array($soalIds) || !is_array($opsiUrutan)) {
            return $this->respond([
                'success' => false,
                'message' => 'Data urutan soal atau opsi tidak valid.',
                'data' => null
            ]);
        }

        // 🔹 Ambil soal & opsi sesuai urutan database (tanpa tertukar)
        $soal = $this->soalModel->getSoalByUrutanCached($validasi['ujian']['bank_soal_id'], $soalIds, $opsiUrutan);

        // 🔹 Hilangkan kunci jawaban dan hitung max_select untuk MPG
        foreach ($soal as &$s) {
            $maxSelect = 0;

            if (isset($s['jenis_soal']) && strtolower($s['jenis_soal']) === 'mpg' && !empty($s['opsi'])) {
                foreach ($s['opsi'] as &$op) {
                    if (!empty($op['is_true'])) {
                        $maxSelect++;
                    }
                    unset($op['is_true']); // hapus kunci benar agar tidak bocor
                }
            } else {
                foreach ($s['opsi'] as &$op) {
                    unset($op['is_true']);
                }
            }

            if ($maxSelect > 0) {
                $s['max_select'] = $maxSelect;
            }
        }

        return $this->respond([
            'success' => true,
            'message' => 'Soal berhasil diambil.',
            'data' => [
                'jumlah_soal' => count($soal),
                'soal' => $soal
            ]
        ]);
    }


    protected function getPesertaFromToken()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return [
                'success' => false,
                'message' => 'Header Authorization tidak ditemukan atau format salah.',
                'id'      => null,
                'data'    => null
            ];
        }

        $token = substr($authHeader, 7);
        $peserta = db_connect()->table('peserta')
            ->where('api_token', $token)
            ->get()
            ->getRowArray();

        if (!$peserta) {
            return [
                'success' => false,
                'message' => 'Token peserta tidak valid.',
                'id'      => null,
                'data'    => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Token valid.',
            'id'      => $peserta['id'],
            'data'    => $peserta
        ];
    }

    public function getUjianDetail($ujianId = null)
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized($peserta['message']);
        }

        if (!$ujianId) {
            return $this->fail('ID ujian tidak ditemukan.');
        }

        // Ambil data ujian: hanya kolom penting
        $ujian = $this->ujianModel
            ->select('id, nama_ujian, bank_soal_id, tampil_nilai, durasi_ujian, is_active, minimal_durasi')
            ->asArray()
            ->where('id', $ujianId)
            ->first();

        if (!$ujian) {
            return $this->failNotFound('Ujian tidak ditemukan.');
        }

        // Cek akses peserta dan ambil hasil ujian sekaligus
        $hasil = $this->hasilUjianModel
            ->select('status, waktu_mulai, waktu_selesai')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasil) {
            return $this->failForbidden('Tidak memiliki akses ke ujian ini.');
        }

        // Set default kalau belum mulai
        $ujian['status_peserta'] = $hasil['status'] ?? 'belum_mulai';
        $ujian['waktu_mulai']    = $hasil['waktu_mulai'] ?? null;
        $ujian['waktu_selesai']  = $hasil['waktu_selesai'] ?? null;

        return $this->respond([
            'success' => true,
            'message' => 'Berhasil mengambil detail ujian.',
            'data'    => $ujian
        ]);
    }



    public function listUjian()
    {
        try {
            $peserta = $this->getPesertaFromToken();
            if (!$peserta['success']) {
                return $this->failUnauthorized($peserta['message']);
            }

            // 🔹 Ambil semua ujian peserta (ringan)
            $ujianList = $this->ujianModel->getAllPesertaRingan($peserta['id']);
            if (!$ujianList || empty($ujianList)) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Belum ada ujian untuk peserta ini.',
                    'data'    => []
                ]);
            }

            $now = date('Y-m-d H:i:s');
            $ujianIds = array_column($ujianList, 'id');

            // 🔹 Ambil semua hasil ujian peserta (hanya jika ada daftar ujian)
            $hasilList = [];
            if (!empty($ujianIds)) {
                $hasilList = $this->hasilUjianModel
                    ->select('ujian_id, status, waktu_mulai')
                    ->whereIn('ujian_id', $ujianIds)
                    ->where('peserta_id', $peserta['id'])
                    ->findAll();
            }

            // 🔹 Buat map hasil untuk lookup cepat
            $hasilMap = [];
            foreach ($hasilList as $h) {
                $hasilMap[$h['ujian_id']] = $h;
            }

            foreach ($ujianList as &$ujian) {
                $waktuMulai   = $ujian['waktu_mulai'] ?? $now;
                $waktuSelesai = $ujian['waktu_selesai'] ?? $now;

                if (!strtotime($waktuMulai)) $waktuMulai = $now;
                if (!strtotime($waktuSelesai)) $waktuSelesai = $now;

                // status waktu ujian
                if ($now < $waktuMulai) {
                    $ujian['status_waktu'] = 'belum_mulai';
                } elseif ($now >= $waktuMulai && $now <= $waktuSelesai) {
                    $ujian['status_waktu'] = 'dibuka';
                } else {
                    $ujian['status_waktu'] = 'terlambat';
                }

                // status peserta
                if (isset($hasilMap[$ujian['id']])) {
                    $hasil = $hasilMap[$ujian['id']];
                    $status = $hasil['status'] ?? 'belum_mulai';

                    $ujian['status_peserta'] = match ($status) {
                        'selesai'        => 'selesai',
                        'sedang_ujian'   => 'sedang_mengerjakan',
                        default           => 'belum_mulai',
                    };

                    if ($status === 'selesai') {
                        $ujian['status_waktu'] = 'selesai';
                    }

                    $ujian['waktu_mulai_ujian'] = $hasil['waktu_mulai'] ?? null;
                } else {
                    $ujian['status_peserta']    = 'belum_mulai';
                    $ujian['waktu_mulai_ujian'] = null;
                }
            }

            return $this->respond([
                'success' => true,
                'message' => 'Berhasil mengambil daftar ujian.',
                'data'    => $ujianList
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'listUjian error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }





    public function getJawabanPeserta($ujianId)
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized($peserta['message']);
        }

        $rows = $this->jawabanModel->getJawabanCached($ujianId, $peserta['id']);

        $data = [];
        foreach ($rows as $row) {
            $decoded = json_decode($row['jawaban'], true);
            $data[$row['soal_id']] = json_last_error() === JSON_ERROR_NONE ? $decoded : $row['jawaban'];
        }

        return $this->respond([
            'success' => true,
            'message' => 'Jawaban berhasil diambil',
            'data' => $data
        ]);
    }



    public function simpanJawaban()
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid!');
        }

        $post = $this->request->getJSON(true);
        $ujianId = $post['ujian_id'] ?? null;
        $jawabanInput = $post['jawaban'] ?? null;

        if (!$ujianId || !is_array($jawabanInput)) {
            return $this->fail('Data tidak lengkap atau format salah.');
        }

        // Ambil data ujian
        $ujian = $this->ujianModel
            ->select('id, bank_soal_id, tampil_nilai')
            ->find($ujianId);
        if (!$ujian) {
            return $this->fail('Data ujian tidak ditemukan.');
        }

        // Ambil hasil ujian peserta
        $hasilUjian = $this->hasilUjianModel
            ->select('id, status')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasilUjian || $hasilUjian['status'] !== 'sedang_ujian') {
            return $this->fail('Hasil ujian tidak ditemukan atau status tidak valid.');
        }

        // 🔹 Simpan jawaban ke database (minimal, tanpa query berat)
        // foreach ($jawabanInput as $soalId => $jawaban) {
        //     $jawabanJson = is_array($jawaban)
        //         ? json_encode($jawaban, JSON_UNESCAPED_UNICODE)
        //         : json_encode(['value' => $jawaban], JSON_UNESCAPED_UNICODE);

        //     $this->jawabanModel->updateOrInsert(
        //         [
        //             'ujian_id' => $ujianId,
        //             'peserta_id' => $peserta['id'],
        //             'soal_id' => $soalId
        //         ],
        //         [
        //             'id' => Uuid::uuid4()->toString(),
        //             'ujian_id' => $ujianId,
        //             'peserta_id' => $peserta['id'],
        //             'soal_id' => $soalId,
        //             'jawaban' => $jawabanJson,
        //             'skor' => 0,
        //         ]
        //     );
        // }

        // 🔹 Bentuk $jawabanPesertaList langsung dari POST
        $jawabanPesertaList = [];
        foreach ($jawabanInput as $soalId => $jawaban) {
            if (is_array($jawaban)) {
                $isAssoc = array_keys($jawaban) !== range(0, count($jawaban) - 1);
                if ($isAssoc) {
                    $jawabanJson = json_encode($jawaban, JSON_UNESCAPED_UNICODE);
                } else {
                    $jawabanJson = json_encode(['values' => $jawaban], JSON_UNESCAPED_UNICODE);
                }
            } else {
                $jawabanJson = json_encode(['value' => $jawaban], JSON_UNESCAPED_UNICODE);
            }
            $jawabanPesertaList[] = [
                'soal_id' => $soalId,
                'jawaban' => $jawabanJson,
                'skor' => 0
            ];
        }

        // 🔹 Jalankan koreksi otomatis
        $koreksi = (new \App\Libraries\KoreksiService())
            ->koreksiPeserta($ujian, $hasilUjian, $jawabanPesertaList);

        // 🔹 Update hasil ujian
        $this->hasilUjianModel->update($hasilUjian['id'], [
            'status' => 'selesai',
            'nilai_pg'        => $koreksi['nilai'] ?? 0,
            'nilai_total'     => $koreksi['nilai'] ?? 0,
            'poin_benar'      => $koreksi['poin_benar'] ?? 0,
            'poin_salah'      => $koreksi['poin_salah'] ?? 0,
            'poin_maksimal'   => $koreksi['total_bobot'] ?? 0,
            'soal_benar'      => $koreksi['soal_benar'] ?? 0,
            'soal_salah'      => $koreksi['soal_salah'] ?? 0,
            'jawaban_json'    => json_encode($koreksi['arsip_jawaban'] ?? [])
        ]);

        return $this->respond([
            'success' => true,
            'message' => 'Jawaban tersimpan dan dikoreksi tanpa query berat.',
        ]);
    }

    public function selesaiUjian()
    {
        // 🔹 Ambil peserta dari token
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid!');
        }

        // 🔹 Ambil ujian_id dari request
        $post = $this->request->getJSON(true);
        $ujianId = $post['ujian_id'] ?? null;

        if (!$ujianId) {
            return $this->fail('ID ujian tidak diberikan.');
        }

        // 🔹 Ambil hasil ujian peserta (hanya kolom penting)
        $hasilUjian = $this->hasilUjianModel
            ->select('id, nilai_pg, nilai_total, poin_benar, poin_salah, poin_maksimal, soal_benar, soal_salah')
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if (!$hasilUjian) {
            return $this->fail('Hasil ujian peserta tidak ditemukan.');
        }

        // 🔹 Ambil info tambahan ujian (tampil_nilai)
        $ujian = $this->ujianModel
            ->select('id, tampil_nilai')
            ->find($ujianId);

        // 🔹 Kirim data ringan ke peserta
        return $this->respond([
            'success' => true,
            'message' => 'Ujian telah diselesaikan.',
            'data' => [
                'nilai'         => (float) ($hasilUjian['nilai_total'] ?? 0),
                'poin_benar'    => (int) ($hasilUjian['poin_benar'] ?? 0),
                'poin_salah'    => (int) ($hasilUjian['poin_salah'] ?? 0),
                'poin_maksimal' => (int) ($hasilUjian['poin_maksimal'] ?? 0),
                'soal_benar'    => (int) ($hasilUjian['soal_benar'] ?? 0),
                'soal_salah'    => (int) ($hasilUjian['soal_salah'] ?? 0),
                'tampil_nilai'  => (string) (($ujian['tampil_nilai'] ?? '0')), // ✅ aman dan jelas
            ]
        ]);
    }
}
