<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table            = 'soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields    = [
        'id',
        'bank_soal_id',
        'soal_no',
        'jenis_soal',
        'pertanyaan',
        'jawaban',
        'topik_soal_id',
        'bobot',

        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;

    protected $afterInsert   = ['clearCacheOnMutation'];
    protected $afterUpdate   = ['clearCacheOnMutation'];
    protected $beforeDelete  = ['clearCacheOnDelete'];

    protected function clearCacheOnMutation(array $data)
    {
        $bankSoalIds = [];
        if (isset($data['data']['bank_soal_id'])) {
            $bankSoalIds[] = $data['data']['bank_soal_id'];
        }
        if (isset($data['id'])) {
            $ids = is_array($data['id']) ? $data['id'] : [$data['id']];
            if (!empty($ids)) {
                $rows = $this->db->table($this->table)->select('bank_soal_id')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $row) {
                    $bankSoalIds[] = $row['bank_soal_id'];
                }
            }
        }
        $bankSoalIds = array_unique(array_filter($bankSoalIds));
        
        try {
            $cache = service('cache');
            foreach ($bankSoalIds as $bsId) {
                $cache->delete("banksoal:questions:{$bsId}");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Redis clearCacheOnMutation failed: ' . $e->getMessage());
        }
        
        return $data;
    }

    protected function clearCacheOnDelete(array $data)
    {
        $bankSoalIds = [];
        if (isset($data['id'])) {
            $ids = is_array($data['id']) ? $data['id'] : [$data['id']];
            if (!empty($ids)) {
                $rows = $this->db->table($this->table)->select('bank_soal_id')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $row) {
                    $bankSoalIds[] = $row['bank_soal_id'];
                }
            }
        }
        $bankSoalIds = array_unique(array_filter($bankSoalIds));
        
        try {
            $cache = service('cache');
            foreach ($bankSoalIds as $bsId) {
                $cache->delete("banksoal:questions:{$bsId}");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Redis clearCacheOnDelete failed: ' . $e->getMessage());
        }
        
        return $data;
    }

    public function getSoalByUrutanCached(string $bankSoalId, array $soalIds, array $urutanOpsi)
    {
        if (empty($soalIds)) return [];

        $cachedPool = null;
        try {
            $cache = service('cache');
            $cacheClass = get_class($cache);
            $isDummy = (strpos($cacheClass, 'DummyHandler') !== false);
            if (!$isDummy) {
                $cacheKey = "banksoal:questions:{$bankSoalId}";
                $cachedPool = $cache->get($cacheKey);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Redis get banksoal questions failed, falling back to MySQL: ' . $e->getMessage());
            $cachedPool = null;
        }

        if ($cachedPool === null) {
            $cachedPool = $this->getSoalWithOpsi($bankSoalId);
            
            try {
                $cache = service('cache');
                $cacheClass = get_class($cache);
                $isDummy = (strpos($cacheClass, 'DummyHandler') !== false);
                if (!$isDummy && !empty($cachedPool)) {
                    $cacheKey = "banksoal:questions:{$bankSoalId}";
                    $cache->save($cacheKey, $cachedPool, 7200); // 2 hours
                }
            } catch (\Throwable $e) {
                // Ignore cache save error
            }
        }

        // Map the cached pool by soal ID for easy lookup
        $poolMap = [];
        foreach ($cachedPool as $soal) {
            $poolMap[$soal['id']] = $soal;
        }

        // Reconstruct the ordered questions and options
        $result = [];
        foreach ($soalIds as $soalId) {
            if (isset($poolMap[$soalId])) {
                $soal = $poolMap[$soalId];
                
                // Sort/filter options based on $urutanOpsi
                $orderedOpsi = [];
                if (isset($urutanOpsi[$soalId])) {
                    $opsiMap = [];
                    foreach ($soal['opsi'] as $opsi) {
                        $opsiMap[$opsi['id']] = $opsi;
                    }
                    foreach ($urutanOpsi[$soalId] as $opsiId) {
                        if (isset($opsiMap[$opsiId])) {
                            $orderedOpsi[] = $opsiMap[$opsiId];
                        }
                    }
                }
                
                $soal['opsi'] = $orderedOpsi;
                $result[] = $soal;
            }
        }

        return $result;
    }

    // Optional: jika ingin menambahkan relasi ke soal_opsi
    public function withOpsi($soalId)
    {
        return $this->db->table('soal')
            ->select('soal.*, soal_opsi.id as opsi_id, soal_opsi.teks, soal_opsi.label, soal_opsi.is_true, soal_opsi.bobot as opsi_bobot')
            ->join('soal_opsi', 'soal_opsi.soal_id = soal.id', 'left')
            ->where('soal.id', $soalId)
            ->get()
            ->getResultArray();
    }
    public function getSoalByBank($bankSoalId, $acakSoal = false, $acakOpsi = false)
    {
        // Ambil data soal dengan topik
        $soalList = $this->db->table('soal s')
            ->select('s.*, ts.nama as nama_topik')
            ->join('topik_soal ts', 'ts.id = s.topik_soal_id', 'left')
            ->where('s.bank_soal_id', $bankSoalId)
            ->orderBy('s.soal_no', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($soalList)) {
            return [];
        }

        // Acak soal jika perlu
        if ($acakSoal) {
            shuffle($soalList);
        }

        $soalIds = array_column($soalList, 'id');

        // Ambil opsi
        $opsiList = $this->db->table('soal_opsi')
            ->whereIn('soal_id', $soalIds)
            ->get()
            ->getResultArray();

        // Kelompokkan opsi per soal
        $groupedOpsi = [];
        foreach ($opsiList as $opsi) {
            $groupedOpsi[$opsi['soal_id']][] = $opsi;
        }

        // Gabungkan ke soal
        foreach ($soalList as &$soal) {
            $opsi = $groupedOpsi[$soal['id']] ?? [];
            if ($acakOpsi) {
                shuffle($opsi);
            }
            $soal['opsi'] = $opsi;
        }

        return $soalList;
    }


    public function getSoalIdsByBank($bankSoalId)
    {
        $soal = $this->select('id')
            ->where('bank_soal_id', $bankSoalId)
            ->orderBy('soal_no', 'ASC') // urutkan berdasarkan soal_no
            ->findAll();

        return array_column($soal, 'id'); // hanya array of ID
    }


    // Mapping acakan opsi, urutkan berdasarkan label jika tidak diacak
    public function getOpsiOrderMap($bankSoalId, $acak = false)
    {
        // Ambil semua soal di bank soal
        $soalList = $this->select('id')
            ->where('bank_soal_id', $bankSoalId)
            ->findAll();

        $result = [];

        foreach ($soalList as $soal) {
            // Ambil semua opsi soal dengan label
            $opsi = $this->db->table('soal_opsi')
                ->select('id, label') // ambil label untuk sorting
                ->where('soal_id', $soal['id'])
                ->get()
                ->getResultArray();

            if ($acak) {
                // Jika acak, cukup shuffle ID
                $ids = array_column($opsi, 'id');
                shuffle($ids);
            } else {
                // Jika tidak acak, urutkan berdasarkan label (A, B, C, dst.)
                usort($opsi, function ($a, $b) {
                    return strcmp($a['label'], $b['label']);
                });
                $ids = array_column($opsi, 'id');
            }

            // Simpan mapping soal => urutan opsi
            $result[$soal['id']] = $ids;
        }

        return $result;
    }

    // Ambil soal dan opsi sesuai urutan
    public function getSoalByUrutan(array $soalIds, array $urutanOpsi)
    {
        if (empty($soalIds)) return [];

        // 1️⃣ Ambil soal sekaligus
        $escapedSoalIds = implode(',', array_map(fn($id) => $this->db->escape($id), $soalIds));

        $soals = $this->db->table('soal')
            ->select('id, bank_soal_id, soal_no, jenis_soal, pertanyaan')
            ->whereIn('id', $soalIds)
            ->orderBy("FIELD(id, {$escapedSoalIds})", '', false)
            ->get()
            ->getResultArray();

        $soalMap = [];
        foreach ($soals as $soal) {
            $soal['opsi'] = [];
            $soalMap[$soal['id']] = $soal;
        }

        // 2️⃣ Ambil semua opsi sekaligus untuk semua soal
        $allOpsiIds = [];
        foreach ($urutanOpsi as $soalId => $opsiIds) {
            $allOpsiIds = array_merge($allOpsiIds, $opsiIds);
        }
        $allOpsiIds = array_unique($allOpsiIds);
        if (!empty($allOpsiIds)) {
            $escapedOpsiIds = implode(',', array_map(fn($id) => $this->db->escape($id), $allOpsiIds));

            $opsiRows = $this->db->table('soal_opsi')
                ->select('id, soal_id, label, teks, pasangan')
                ->whereIn('id', $allOpsiIds)
                ->get()
                ->getResultArray();

            // 3️⃣ Map opsi ke soal masing-masing
            $opsiMap = [];
            foreach ($opsiRows as $op) {
                $opsiMap[$op['soal_id']][] = $op;
            }

            // 4️⃣ Urutkan opsi sesuai urutanOpsi
            foreach ($urutanOpsi as $soalId => $opsiIds) {
                if (isset($soalMap[$soalId])) {
                    $soalMap[$soalId]['opsi'] = array_values(array_map(
                        fn($id) => $opsiMap[$soalId][array_search($id, array_column($opsiMap[$soalId], 'id'))] ?? null,
                        $opsiIds
                    ));
                }
            }
        }

        return array_values($soalMap);
    }

    public function getSoalByUrutanLitexx(array $soalIds, array $urutanOpsi)
    {
        if (empty($soalIds)) return [];

        // Escape soal dan opsi
        $escapedSoalIds = implode(',', array_map(fn($id) => $this->db->escape($id), $soalIds));
        $allOpsiIds = [];
        foreach ($urutanOpsi as $ids) $allOpsiIds = array_merge($allOpsiIds, $ids);
        $allOpsiIds = array_unique($allOpsiIds);
        $escapedOpsiIds = implode(',', array_map(fn($id) => $this->db->escape($id), $allOpsiIds));

        $rows = $this->db->table('soal s')
            ->select('s.id as soal_id, s.bank_soal_id, s.soal_no, s.jenis_soal, s.pertanyaan,
                  o.id as opsi_id, o.label, o.teks, o.pasangan, o.is_true')
            ->join('soal_opsi o', 'o.soal_id = s.id AND o.id IN (' . $escapedOpsiIds . ')', 'left')
            ->whereIn('s.id', $soalIds)
            ->orderBy("FIELD(s.id, {$escapedSoalIds})", '', false)
            ->orderBy("FIELD(o.id, {$escapedOpsiIds})", '', false)
            ->get()
            ->getResultArray();

        $soalMap = [];
        foreach ($rows as $row) {
            $soalId = $row['soal_id'];
            if (!isset($soalMap[$soalId])) {
                $soalMap[$soalId] = [
                    'id' => $row['soal_id'],
                    'bank_soal_id' => $row['bank_soal_id'],
                    'soal_no' => $row['soal_no'],
                    'jenis_soal' => $row['jenis_soal'],
                    'pertanyaan' => $row['pertanyaan'],
                    'opsi' => []
                ];
            }
            if ($row['opsi_id']) {
                $soalMap[$soalId]['opsi'][] = [
                    'id' => $row['opsi_id'],
                    'label' => $row['label'],
                    'teks' => $row['teks'],
                    'pasangan' => $row['pasangan'] ?? null,
                    'is_true' => $row['is_true'] ?? null  // 🔹 tambahkan ini
                ];
            }
        }

        return array_values($soalMap);
    }

    public function getSoalByUrutanFinal(array $soalIds, array $urutanOpsi)
    {
        if (empty($soalIds)) return [];

        // Escape soal IDs
        $escapedSoalIds = implode(',', array_map(fn($id) => $this->db->escape($id), $soalIds));

        // Ambil semua soal sekaligus
        $soalRows = $this->db->table('soal')
            ->whereIn('id', $soalIds)
            ->orderBy("FIELD(id, {$escapedSoalIds})", '', false)
            ->get()
            ->getResultArray();

        // Siapkan mapping soal
        $soalMap = [];
        foreach ($soalRows as $s) {
            $soalMap[$s['id']] = [
                'id' => $s['id'],
                'bank_soal_id' => $s['bank_soal_id'],
                'soal_no' => $s['soal_no'],
                'jenis_soal' => $s['jenis_soal'],
                'pertanyaan' => $s['pertanyaan'],
                'opsi' => []
            ];
        }

        // Ambil semua opsi untuk soal-soal yang ada
        $allOpsiIds = [];
        foreach ($urutanOpsi as $ids) {
            $allOpsiIds = array_merge($allOpsiIds, $ids);
        }
        $allOpsiIds = array_unique($allOpsiIds);

        if (!empty($allOpsiIds)) {
            $escapedOpsiIds = implode(',', array_map(fn($id) => $this->db->escape($id), $allOpsiIds));

            $opsiRows = $this->db->query("
            SELECT id, soal_id, label, teks, pasangan, is_true
            FROM soal_opsi
            WHERE id IN ($escapedOpsiIds)
        ")->getResultArray();

            // Map opsi per soal
            $opsiMap = [];
            foreach ($opsiRows as $op) {
                $opsiMap[$op['soal_id']][$op['id']] = $op;
            }

            // Susun opsi sesuai urutan database
            foreach ($soalMap as $soalId => &$s) {
                if (isset($urutanOpsi[$soalId])) {
                    foreach ($urutanOpsi[$soalId] as $opId) {
                        if (isset($opsiMap[$soalId][$opId])) {
                            $s['opsi'][] = $opsiMap[$soalId][$opId];
                        }
                    }
                }
            }
        }

        return array_values($soalMap);
    }





    public function getSoalWithOpsi($bankSoalId)
    {
        $soalList = $this->where('bank_soal_id', $bankSoalId)
            ->orderBy('soal_no', 'ASC')
            ->findAll();

        $soalIds = array_column($soalList, 'id');

        if (empty($soalIds)) return [];

        // Ambil semua opsi sekaligus
        $opsiList = $this->db->table('soal_opsi')
            ->whereIn('soal_id', $soalIds)
            ->get()
            ->getResultArray();

        // Kelompokkan opsi berdasarkan soal_id
        $opsiGrouped = [];
        foreach ($opsiList as $opsi) {
            $opsiGrouped[$opsi['soal_id']][] = $opsi;
        }

        // Gabungkan ke soal
        foreach ($soalList as &$soal) {
            $soal['opsi'] = $opsiGrouped[$soal['id']] ?? [];
        }

        return $soalList;
    }
}
