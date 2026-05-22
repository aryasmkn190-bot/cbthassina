<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UjianModel;
use App\Models\HasilUjianModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\JawabanModel;
use Ramsey\Uuid\Uuid;

class UjianPesertaApiController extends ResourceController
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

        $ujian = $this->ujianModel->find($ujianId);
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
            $hasilUjianModel = new \App\Models\HasilUjianModel();
            $hasilUjianModel->update($validasi['hasil']['id'], [
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
                $hasilUjianModel = new \App\Models\HasilUjianModel();
                $hasilUjianModel->update($validasi['hasil']['id'], [
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





    public function soal($ujianId)
    {
        $peserta = $this->getPesertaFromToken();
        if (!$peserta['success']) {
            return $this->failUnauthorized('Token tidak valid !');
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

        $soal = $this->soalModel->getSoalByUrutan($soalIds, $opsiUrutan);

        // Hilangkan kunci jawaban
        foreach ($soal as &$s) {
            foreach ($s['opsi'] as &$op) {
                unset($op['is_true']);
            }
        }

        return $this->respond([
            'success' => true,
            'message' => 'Soal berhasil diambil.',
            'data' => [
                'jumlah_soal' => count($soal),  // <- Tambahkan ini
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

        $ujian = $this->ujianModel
            ->asArray()
            ->where('id', $ujianId)
            ->first();

        if (!$ujian) {
            return $this->failNotFound('Ujian tidak ditemukan.');
        }

        // cek akses peserta
        $pesertaUjian = $this->ujianModel->getAllPeserta($peserta['id']);
        $allowed = false;
        foreach ($pesertaUjian as $u) {
            if ($u['id'] == $ujianId) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return $this->failForbidden('Tidak memiliki akses ke ujian ini.');
        }

        // ambil hasil ujian kalau ada
        $hasil = $this->hasilUjianModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->first();

        if ($hasil) {
            $ujian['waktu_mulai']    = $hasil['waktu_mulai'];
            $ujian['waktu_selesai']  = $hasil['waktu_selesai'];
            $ujian['status_peserta'] = $hasil['status'];
        } else {
            $ujian['status_peserta'] = 'belum_mulai';
        }

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

            $data = $this->ujianModel->getAllPeserta($peserta['id']);
            if (!$data) $data = [];

            $now = date('Y-m-d H:i:s');

            foreach ($data as &$ujian) {
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

                // cek hasil ujian
                $hasil = $this->hasilUjianModel
                    ->where('ujian_id', $ujian['id'])
                    ->where('peserta_id', $peserta['id'])
                    ->first();

                if ($hasil) {
                    $status = $hasil['status'] ?? 'belum_mulai';
                    $ujian['status_peserta'] = $status === 'selesai'
                        ? 'selesai'
                        : ($status === 'sedang_ujian' ? 'sedang_mengerjakan' : 'belum_mulai');

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
                'data'    => $data
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

        $rows = $this->jawabanModel
            ->where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta['id'])
            ->findAll();

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
            return $this->failUnauthorized('Token tidak valid !');
        }

        $post = $this->request->getJSON(true);
        $ujianId = $post['ujian_id'] ?? null;
        $jawabanInput = $post['jawaban'] ?? null;

        if (!$ujianId || !is_array($jawabanInput)) {
            return $this->fail('Data tidak lengkap atau format salah.');
        }

        foreach ($jawabanInput as $soalId => $jawaban) {
            // Deteksi format jawaban
            if (is_array($jawaban)) {
                $isAssoc = array_keys($jawaban) !== range(0, count($jawaban) - 1);

                if ($isAssoc) {
                    // Menjodohkan atau Benar/Salah
                    $jawabanJson = json_encode($jawaban, JSON_UNESCAPED_UNICODE);
                } else {
                    // MPG (Multiple pilihan ganda)
                    $jawabanJson = json_encode(['values' => $jawaban], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // PG, Esai, Isian
                $jawabanJson = json_encode(['value' => $jawaban], JSON_UNESCAPED_UNICODE);
            }

            $data = [
                'id' => Uuid::uuid4()->toString(),
                'ujian_id' => $ujianId,
                'peserta_id' => $peserta['id'],
                'soal_id' => $soalId,
                'jawaban' => $jawabanJson,
                'skor' => 0,
            ];

            $this->jawabanModel->updateOrInsert([
                'ujian_id' => $ujianId,
                'peserta_id' => $peserta['id'],
                'soal_id' => $soalId,
            ], $data);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }




    public function selesaiUjian()
    {
        try {
            // Ambil peserta dari token
            $peserta = $this->getPesertaFromToken();
            if (!$peserta['success']) {
                return $this->failUnauthorized('Token tidak valid !');
            }

            $pesertaId = $peserta['id'];
            $input = $this->request->getJSON(true);
            $ujianId = $input['ujian_id'] ?? null;

            if (!$ujianId) {
                return $this->fail('ujian_id wajib dikirim.');
            }

            // Ambil hasil ujian peserta
            $hasilUjian = $this->hasilUjianModel
                ->where('ujian_id', $ujianId)
                ->where('peserta_id', $pesertaId)
                ->first();

            if (!$hasilUjian || $hasilUjian['status'] !== 'sedang_ujian') {
                return $this->fail('Data hasil tidak ditemukan atau status tidak valid.');
            }

            // Ambil data ujian
            $ujian = $this->ujianModel->find($ujianId);
            if (!$ujian) {
                return $this->fail('Data ujian tidak ditemukan.');
            }

            // Ambil soal dan jawaban peserta
            $daftarSoal = $this->soalModel
                ->where('bank_soal_id', $ujian['bank_soal_id'])
                ->findAll();
            if (!$daftarSoal) $daftarSoal = [];

            $jawabanPesertaList = $this->jawabanModel
                ->where('ujian_id', $ujianId)
                ->where('peserta_id', $pesertaId)
                ->findAll();
            if (!$jawabanPesertaList) $jawabanPesertaList = [];

            // Koreksi otomatis
            $koreksi = (new \App\Libraries\KoreksiService())
                ->koreksiPeserta($ujian, $hasilUjian, $jawabanPesertaList);

            // Update hasil ujian
            $this->hasilUjianModel->update($hasilUjian['id'], [
                'status' => 'selesai',
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'nilai_pg' => $koreksi['nilai'] ?? 0,
                'nilai_total' => $koreksi['nilai'] ?? 0,
                'poin_benar' => $koreksi['poin_benar'] ?? 0,
                'poin_salah' => $koreksi['poin_salah'] ?? 0,
                'poin_maksimal' => $koreksi['total_bobot'] ?? 0,
                'soal_benar' => $koreksi['soal_benar'] ?? 0,
                'soal_salah' => $koreksi['soal_salah'] ?? 0,
                'jawaban_json' => json_encode($koreksi['arsip_jawaban'] ?? [])
            ]);

            return $this->respond([
                'success' => true,
                'message' => 'Ujian telah diselesaikan dan dikoreksi.',
                'data' => [
                    'jumlah_soal' => count($daftarSoal),
                    'poin_benar' =>  $koreksi['poin_benar'] ?? 0,
                    'poin_salah' => $koreksi['poin_salah'] ?? 0,
                    'poin_maksimal' => $koreksi['total_bobot'] ?? 0,
                    'soal_benar' => $koreksi['soal_benar'] ?? 0,
                    'soal_salah' => $koreksi['soal_salah'] ?? 0,
                    'nilai' => $koreksi['nilai'] ?? 0,
                    'koreksi_detail' => $koreksi['koreksi_detail'] ?? [],
                    'tampil_nilai' => $ujian['tampil_nilai'] ?? 0
                ]
            ]);
        } catch (\Throwable $e) {
            // Tangani error server agar tidak 500
            return $this->respond([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
