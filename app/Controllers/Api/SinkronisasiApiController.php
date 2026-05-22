<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PesertaModel;
use App\Models\UjianModel;
use App\Models\BankSoalModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\HasilUjianModel;
use App\Models\KelasModel;
use App\Models\TingkatModel;
use App\Models\RuangModel;
use App\Models\SesiModel;
use App\Models\JurusanModel;
use App\Models\AgamaModel;
use App\Models\MediaFileModel;
use App\Models\UserModel;
use App\Models\TopikSoalModel;
use App\Models\JenisUjianModel;

use CodeIgniter\RESTful\ResourceController;

class SinkronisasiApiController extends ResourceController
{
    protected $format = 'json';

    protected $limit  = 500;

    public function proses()
    {
        $token  = $this->request->getGet('token');
        $offset = (int)$this->request->getGet('offset') ?: 0;

        if (!$this->isValidToken($token)) {
            return $this->respond([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        try {
            // Load model
            $pesertaModel    = new PesertaModel();
            $kelasModel      = new KelasModel();
            $tingkatModel    = new TingkatModel();
            $jurusanModel    = new JurusanModel();
            $agamaModel      = new AgamaModel();
            $ujianModel      = new UjianModel();
            $banksoalModel   = new BankSoalModel();
            $soalModel       = new SoalModel();
            $soalOpsiModel   = new SoalOpsiModel();
            $hasilUjianModel = new HasilUjianModel();
            $mediaFileModel  = new MediaFileModel();
            $userModel       = new UserModel();
            $topikSoalModel  = new TopikSoalModel();
            $ruangModel  = new RuangModel();
            $sesiModel  = new SesiModel();
            $jenisUjianModel = new JenisUjianModel();

            // --- Peserta batch ---
            $peserta = $pesertaModel
                ->limit($this->limit, $offset)
                ->find();

            $kelasIds   = array_unique(array_column($peserta, 'kelas_id'));
            $tingkatIds = array_unique(array_column($peserta, 'tingkat_id'));
            $jurusanIds = array_unique(array_column($peserta, 'jurusan_id'));
            $agamaIds   = array_unique(array_column($peserta, 'agama_id'));
            $ruangIds   = array_unique(array_column($peserta, 'ruang_id'));
            $sesiIds   = array_unique(array_column($peserta, 'sesi_id'));

            $kelas   = $kelasIds   ? $kelasModel->whereIn('id', $kelasIds)->findAll()   : [];
            $tingkat = $tingkatIds ? $tingkatModel->whereIn('id', $tingkatIds)->findAll() : [];
            $jurusan = $jurusanIds ? $jurusanModel->whereIn('id', $jurusanIds)->findAll() : [];
            $agama   = $agamaIds   ? $agamaModel->whereIn('id', $agamaIds)->findAll()   : [];
            $ruang   = $ruangIds   ? $ruangModel->whereIn('id', $ruangIds)->findAll()   : [];
            $sesi   = $sesiIds   ? $sesiModel->whereIn('id', $sesiIds)->findAll()   : [];

            // --- Ujian batch ---
            $ujian = $ujianModel
                ->limit($this->limit, $offset)
                ->find();

            $jenisUjianIds = array_unique(array_column($ujian, 'jenis_ujian_id'));
            $jenisUjian = $jenisUjianIds
                ? $jenisUjianModel->whereIn('id', $jenisUjianIds)->findAll()
                : [];

            $banksoalIds = array_unique(array_column($ujian, 'bank_soal_id'));
            $banksoal = $banksoalIds ? $banksoalModel->whereIn('id', $banksoalIds)->findAll() : [];

            // --- Soal batch (support >500) ---
            $soal = [];
            if ($banksoal) {
                $soalOffset = 0;
                $soalBatch  = [];
                $soalIdsAll = array_column($banksoal, 'id');

                do {
                    $soalBatch = $soalModel
                        ->whereIn('bank_soal_id', $soalIdsAll)
                        ->limit($this->limit, $soalOffset)
                        ->findAll();

                    $soal = array_merge($soal, $soalBatch);
                    $soalOffset += $this->limit;
                } while (count($soalBatch) === $this->limit);
            }

            // --- Opsi soal ---
            $soalOpsi = [];
            if ($soal) {
                $soalIdsOnly = array_column($soal, 'id');
                $soalOpsi = $soalOpsiModel->whereIn('soal_id', $soalIdsOnly)->findAll();
            }

            // Gabungkan opsi ke soal
            $opsiBySoalId = [];
            foreach ($soalOpsi as $opsi) {
                $opsiBySoalId[$opsi['soal_id']][] = $opsi;
            }
            foreach ($soal as &$s) {
                $s['opsi'] = $opsiBySoalId[$s['id']] ?? [];
            }

            // --- Hasil ujian batch ringan ---
            $hasilUjian = [];
            $hasilLimit  = 100; // limit per batch supaya aman
            $hasilOffset = $offset;

            do {
                $batch = $hasilUjianModel
                    ->select('id, ujian_id, peserta_id, nilai_total, waktu_mulai,waktu_selesai,soal_benar,soal_salah,poin_benar,poin_salah,poin_maksimal, status, platform')
                    ->limit($hasilLimit, $hasilOffset)
                    ->findAll();

                $hasilUjian = array_merge($hasilUjian, $batch);
                $hasilOffset += $hasilLimit;
            } while (count($batch) === $hasilLimit); // ulangi sampai batch terakhir



            // --- Media files batch ---
            $mediaFiles = $mediaFileModel
                ->limit($this->limit, $offset)
                ->findAll();

            // --- Users dan topik soal ---
            $userIds = array_unique(array_column($banksoal, 'created_by'));
            $users   = $userIds ? $userModel->whereIn('id', $userIds)->findAll() : [];

            $topikSoalIds = array_unique(array_column($soal, 'topik_soal_id'));
            $topikSoal    = $topikSoalIds ? $topikSoalModel->whereIn('id', $topikSoalIds)->findAll() : [];

            return $this->respond([
                'success' => true,
                'message' => 'Data batch berhasil diambil',
                'data' => [
                    'peserta'     => $peserta,
                    'kelas'       => $kelas,
                    'tingkat'     => $tingkat,
                    'jurusan'     => $jurusan,
                    'agama'       => $agama,
                    'ruang'       => $ruang,
                    'sesi'        => $sesi,
                    'ujian'       => $ujian,
                    'banksoal'    => $banksoal,
                    'soal'        => $soal,
                    'hasil_ujian' => $hasilUjian,
                    'media_files' => $mediaFiles,
                    'users'       => $users,
                    'topik_soal'  => $topikSoal,
                    'jenis_ujian' => $jenisUjian,
                ],
                'limit'  => $this->limit,
                'offset' => $offset,
                'count'  => count($peserta)
            ]);
        } catch (\Throwable $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function cekKoneksi()
    {
        $token = $this->request->getGet('token');

        if (!$token) {
            return $this->respond([
                'success' => false,
                'message' => 'Token tidak dikirim'
            ], 400);
        }

        if ($this->isValidToken($token)) {
            return $this->respond([
                'success' => true,
                'message' => 'Koneksi berhasil, token valid'
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }
    }


    private function isValidToken($token)
    {
        $settingModel = new \App\Models\SettingsModel();
        $apiToken = $settingModel->where('id', '1')->get()->getFirstRow();

        if (!$apiToken) {
            return false;
        }

        return $token === $apiToken->api_token;
    }

    public function kirimujian()
    {
        $token = $this->request->getPost('token');
        $ujian_id = $this->request->getPost('ujian_id');
        $jsonData = $this->request->getPost('data');

        if (!$this->isValidToken($token)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Token tidak valid']);
        }

        $hasilList = json_decode($jsonData, true);

        if (!$hasilList) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data kosong atau rusak']);
        }

        $model = new \App\Models\HasilUjianModel();
        foreach ($hasilList as $hasil) {
            $existing = $model->where('id', $hasil['id'])->first();
            if ($existing) {
                $model->update($hasil['id'], $hasil);
            }
            // else {
            //     $model->insert($hasil);
            // }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Sinkronisasi berhasil']);
    }
}
