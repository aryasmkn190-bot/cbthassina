<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SinkronisasiController extends BaseController
{
    public function index()
    {
        return view('Panel/Sinkronisasi/sinkronisasi_view', [
            'setting' => $this->appSetting(),
            'title'   => 'Sinkronisasi Data',
        ]);
    }



    public function proses()
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $request = service('request');
        $token   = $request->getPost('token');
        $url     = $request->getPost('url');
        $type    = $request->getPost('type');
        $offset  = (int)$request->getPost('offset') ?: 0;
        $limit   = 500;

        $hasilGlobal = [];

        do {
            $response = $this->prosesBatch($token, $url, $type, $offset, $limit);

            if (!$response['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sinkronisasi gagal di batch offset ' . $offset,
                    'error' => $response['message'] ?? null
                ]);
            }

            // Gabungkan hasil tiap batch
            foreach ($response['gagal'] as $key => $sub) {
                if (!isset($hasilGlobal[$type][$key])) {
                    $hasilGlobal[$type][$key] = [
                        'berhasil' => 0,
                        'gagal' => 0,
                        'detail' => []
                    ];
                }

                $hasilGlobal[$type][$key]['berhasil'] += $sub['berhasil'] ?? 0;

                if (isset($sub['gagal'])) {
                    // Pastikan $sub['gagal'] array
                    $gagalCount = is_array($sub['gagal']) ? count($sub['gagal']) : 0;
                    $hasilGlobal[$type][$key]['gagal'] += $gagalCount;
                }

                if (isset($sub['detail'])) {
                    $hasilGlobal[$type][$key]['detail'] = array_merge(
                        $hasilGlobal[$type][$key]['detail'],
                        $sub['detail']
                    );
                }
            }

            $offset += $limit;
            $hasMore = $response['has_more'] ?? false;
        } while ($hasMore);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sinkronisasi otomatis selesai',
            'data'    => $hasilGlobal
        ]);
    }

    private function prosesBatch($token, $url, $type, $offset, $limit)
    {
        try {
            $client = \Config\Services::curlrequest();

            $res = $client->get(rtrim($url, '/') . '/api/sinkronisasi', [
                'query' => [
                    'token'  => $token,
                    'type'   => $type,
                    'offset' => $offset,
                    'limit'  => $limit
                ],
                'http_errors' => false
            ]);

            $status = $res->getStatusCode();
            $body   = json_decode($res->getBody(), true);

            if ($status !== 200 || !$body['success']) {
                return [
                    'success' => false,
                    'message' => $body['message'] ?? 'Gagal mengambil data'
                ];
            }

            $data = $body['data'] ?? [];
            $dataType = isset($data[$type]) && is_array($data[$type]) ? $data[$type] : [];
            $hasMore = count($dataType) === $limit;

            $gagal = [];

            switch ($type) {
                case 'peserta':
                    $gagal['kelas']   = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\KelasModel(), $data['kelas'] ?? []));
                    $gagal['tingkat'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\TingkatModel(), $data['tingkat'] ?? []));
                    $gagal['jurusan'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\JurusanModel(), $data['jurusan'] ?? []));
                    $gagal['agama']   = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\AgamaModel(), $data['agama'] ?? []));
                    $gagal['ruang']   = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\RuangModel(), $data['ruang'] ?? []));
                    $gagal['sesi']    = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\SesiModel(), $data['sesi'] ?? []));
                    $gagal['peserta'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\PesertaModel(), $data['peserta'] ?? [], 'nisn'));
                    break;

                case 'banksoal':
                    $gagal['users']      = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\UserModel(), $data['users'] ?? [], 'username'));
                    $gagal['banksoal']   = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\BankSoalModel(), $data['banksoal'] ?? []));
                    $gagal['topik_soal'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\TopikSoalModel(), $data['topik_soal'] ?? []));
                    $gagal['soal']       = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\SoalModel(), $data['soal'] ?? []));
                    $gagal['soal_opsi']  = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\SoalOpsiModel(), $this->flattenSoalOpsi($data['soal'] ?? [])));

                    if (!empty($data['media_files'])) {
                        $mediaDebug = [];

                        foreach ($data['media_files'] as $file) {
                            $remoteUrl  = rtrim($url, '/') . '/' . ltrim($file['path'], '/');
                            $targetPath = FCPATH . $file['path'];
                            $status = null;
                            $reason = null;

                            try {
                                if (!is_dir(dirname($targetPath))) {
                                    mkdir(dirname($targetPath), 0755, true);
                                }

                                if (file_exists($targetPath) && filesize($targetPath) > 0) {
                                    $reason = 'Sudah ada';
                                } else {
                                    $res = $client->get($remoteUrl, [
                                        'sink' => $targetPath,
                                        'http_errors' => false,
                                        'verify' => false,
                                        'headers' => ['User-Agent' => 'PHP cURL']
                                    ]);

                                    $status = $res->getStatusCode();

                                    // fallback manual jika file masih kosong
                                    if ($status === 200) {
                                        if (!file_exists($targetPath) || filesize($targetPath) === 0) {
                                            $body = $res->getBody(); // string
                                            if (strlen($body) > 0) {
                                                file_put_contents($targetPath, $body);
                                            }
                                        }
                                    }

                                    if (!file_exists($targetPath) || filesize($targetPath) === 0) {
                                        $reason = 'File tidak ada atau kosong setelah download';
                                    }
                                }
                            } catch (\Throwable $e) {
                                $reason = $e->getMessage();
                            }

                            $mediaDebug[] = [
                                'file' => $file['path'],
                                'remoteUrl' => $remoteUrl,
                                'targetPath' => $targetPath,
                                'status' => $status,
                                'reason' => $reason
                            ];
                        }

                        $gagal['media_files'] = [
                            'berhasil' => count(array_filter($mediaDebug, fn($d) => !$d['reason'])),
                            'gagal'    => count(array_filter($mediaDebug, fn($d) => $d['reason'])),
                            'detail'   => $mediaDebug
                        ];
                    }


                    break;

                case 'jadwal':
                    $gagal['jenis_ujian'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\JenisUjianModel(), $data['jenis_ujian'] ?? []));
                    $gagal['ujian']       = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\UjianModel(), $data['ujian'] ?? []));
                    $gagal['hasil_ujian'] = $this->formatSimpanBatch($this->simpanBatch(new \App\Models\HasilUjianModel(), $data['hasil_ujian'] ?? []));
                    break;
            }

            return [
                'success' => true,
                'gagal' => $gagal,
                'has_more' => $hasMore
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function downloadMediaFilesBatch($files, $serverUrl)
    {
        $client = \Config\Services::curlrequest();
        $gagal = [];

        foreach ($files as $file) {
            try {
                $remoteUrl  = rtrim($serverUrl, '/') . '/' . ltrim($file['path'], '/');
                $targetPath = FCPATH . $file['path'];

                // Buat folder jika belum ada
                if (!is_dir(dirname($targetPath))) mkdir(dirname($targetPath), 0755, true);

                // Lewati jika file sudah ada
                if (file_exists($targetPath)) {
                    $gagal[] = [
                        'file' => $file['path'],
                        'reason' => 'Sudah ada',
                        'targetPath' => $targetPath,
                        'remoteUrl' => $remoteUrl
                    ];
                    continue;
                }

                // Ambil file dengan cURL
                $res = $client->get($remoteUrl, [
                    'http_errors' => false,
                    'sink'        => $targetPath,
                    'verify'      => false, // bypass SSL
                    'headers'     => [
                        'User-Agent' => 'Mozilla/5.0 (PHP cURL)'
                    ]
                ]);

                $status = $res->getStatusCode();

                if ($status !== 200) {
                    $gagal[] = [
                        'file' => $file['path'],
                        'reason' => 'Gagal download',
                        'status' => $status,
                        'remoteUrl' => $remoteUrl,
                        'targetPath' => $targetPath
                    ];
                }
            } catch (\Throwable $e) {
                $gagal[] = [
                    'file' => $file['path'] ?? 'unknown',
                    'error' => $e->getMessage(),
                    'remoteUrl' => $remoteUrl ?? null,
                    'targetPath' => $targetPath ?? null
                ];
            }
        }

        return $gagal;
    }

    /**
     * Format hasil simpan batch
     * Input: array dari simpanBatch misal ['gagal' => [...], 'tersimpan' => n]
     * Output: ['berhasil' => n, 'gagal' => [...]]
     */
    private function formatSimpanBatch($result)
    {
        return [
            'berhasil' => $result['tersimpan'] ?? 0,
            'gagal' => $result['gagal'] ?? []
        ];
    }


    private function simpanBatch($model, array $data, ?string $uniqueKey = null): array
    {
        $gagal = [];
        $sukses = 0;

        foreach ($data as $item) {
            try {
                $id = $item['id'] ?? null;

                if ($uniqueKey && isset($item[$uniqueKey])) {
                    $model->where($uniqueKey, $item[$uniqueKey])->orWhere('id', $id)->delete(null, true);
                } elseif ($id) {
                    $model->where('id', $id)->delete(null, true);
                }

                $model->insert($item);
                $sukses++;
            } catch (\Throwable $e) {
                $gagal[] = [
                    'data'  => $item,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'tersimpan' => $sukses, // <-- ubah dari 'sukses' ke 'tersimpan'
            'gagal'     => $gagal
        ];
    }



    public function simpan_koneksi()
    {
        $token = $this->request->getPost('token');
        $url   = $this->request->getPost('url');

        if (!$token || !$url) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token atau URL kosong.'
            ]);
        }

        $model = new \App\Models\SettingsModel();
        $model->settings_update(['id' => 1], [
            'api_token' => $token,
            'api_url'   => $url,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data koneksi disimpan.'
        ]);
    }
    private function flattenSoalOpsi(array $soal): array
    {
        $opsi = [];
        foreach ($soal as $item) {
            if (!empty($item['opsi'])) {
                foreach ($item['opsi'] as $o) {
                    $opsi[] = $o;
                }
            }
        }
        return $opsi;
    }
}
