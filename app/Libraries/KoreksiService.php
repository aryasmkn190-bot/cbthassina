<?php

namespace App\Libraries;

class KoreksiService
{
    protected $soalModel;
    protected $soalOpsiModel;

    public function __construct()
    {
        $this->soalModel = model('SoalModel');
        $this->soalOpsiModel = model('SoalOpsiModel');
    }

    /**
     * Koreksi ujian untuk satu peserta
     */
    public function koreksiPeserta(array $ujian, array $peserta, array $jawabanPesertaList): array
    {
        // 🔹 Cache per bank soal
        static $cacheSoal = [];
        static $cacheOpsi = [];

        $bankSoalId = $ujian['bank_soal_id'];

        if (!isset($cacheSoal[$bankSoalId])) {
            $cacheSoal[$bankSoalId] = $this->soalModel
                ->where('bank_soal_id', $bankSoalId)
                ->findAll();

            $soalIds = array_column($cacheSoal[$bankSoalId], 'id');
            $opsiList = $this->soalOpsiModel
                ->whereIn('soal_id', $soalIds)
                ->findAll();

            foreach ($opsiList as $opsi) {
                $cacheOpsi[$bankSoalId][$opsi['soal_id']][] = $opsi;
            }
        }

        $daftarSoal = $cacheSoal[$bankSoalId];
        $opsiBank = $cacheOpsi[$bankSoalId];

        // 🔹 Map jawaban peserta
        $jawabanMap = [];
        foreach ($jawabanPesertaList as $j) {
            $jawabanMap[$j['soal_id']] = json_decode($j['jawaban'], true);
        }

        $totalPoinBenar = 0;
        $totalBobotMaks = 0;
        $jumlahSoalBenar = 0;
        $jumlahSoalSalah = 0;
        $arsipJawaban = [];
        $koreksiPerSoal = [];

        foreach ($daftarSoal as $soal) {
            $soalId = $soal['id'];
            $jenis = $soal['jenis_soal'];
            $daftarOpsi = $opsiBank[$soalId] ?? [];
            $jawaban = $jawabanMap[$soalId] ?? null;

            $skorDiperoleh = 0;
            $isBenar = false;
            $bobotSoal = (int) ($soal['bobot'] ?? 0);

            // ======== Koreksi Per Jenis Soal ========
            switch ($jenis) {
                case 'pg':
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiDipilih = array_filter($daftarOpsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot = $opsiDipilih ? (int) array_values($opsiDipilih)[0]['bobot'] : 0;
                    $skorDiperoleh = $labelJawaban ? $bobot : 0;

                    $opsiBenar = array_filter($daftarOpsi, fn($o) => !empty($o['is_true']));
                    $labelBenar = $opsiBenar ? array_values($opsiBenar)[0]['label'] ?? null : null;
                    $isBenar = $labelJawaban && $labelJawaban === $labelBenar;
                    break;

                case 'mpg':
                    $jawabanList = $jawaban['values'] ?? [];
                    $opsiBenar = [];
                    foreach ($daftarOpsi as $opsi) {
                        if (!empty($opsi['is_true'])) {
                            $opsiBenar[] = $opsi['label'];
                            if ((int) $opsi['bobot'] > 0) {
                                $totalBobotMaks += (int) $opsi['bobot'];
                            }
                        }
                        if (in_array($opsi['label'], $jawabanList)) {
                            $skorDiperoleh += (int) $opsi['bobot'];
                        }
                    }
                    sort($jawabanList);
                    sort($opsiBenar);
                    $isBenar = $jawabanList === $opsiBenar;
                    break;

                case 'benar_salah':
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $label = $opsi['label'];
                        $jawabanPeserta = strtolower($jawaban[$label] ?? '');
                        $jawabanBenar = strtolower($opsi['is_true'] ? 'benar' : 'salah');
                        $bobot = (int) $opsi['bobot'];

                        if ($jawabanPeserta === '') continue;

                        if ($jawabanPeserta === $jawabanBenar) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'jodohkan':
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $jawab = $jawaban[$opsi['label']] ?? null;
                        $bobot = (int) $opsi['bobot'];
                        if ($jawab === null || $jawab === '') continue;

                        if ($jawab === $opsi['pasangan']) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'isian':
                case 'esai':
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;
                    $kunciJawaban = json_decode($soal['jawaban'], true) ?? [];
                    $isBenar = $jawabanUser !== '' && $this->cocokIsian($jawabanUser, $kunciJawaban);
                    $skorDiperoleh = $isBenar ? $bobotSoal : 0;
                    break;
            }

            // ✅ Tambah total bobot maksimum tiap soal (kecuali MPG, karena sudah ditambah per opsi)
            if ($jenis !== 'mpg') {
                $totalBobotMaks += $bobotSoal;
            }

            $totalPoinBenar += $skorDiperoleh;
            if ($isBenar) $jumlahSoalBenar++;
            else $jumlahSoalSalah++;

            if ($jawaban !== null) {
                // 🔹 Arsip jawaban
                if ($jenis === 'pg') {
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? null,
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif ($jenis === 'mpg') {
                    $arsipJawaban[$soalId] = [
                        'values' => $jawaban['values'] ?? [],
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif (in_array($jenis, ['benar_salah', 'jodohkan'])) {
                    $arsipJawaban[$soalId] = array_merge(
                        $jawaban,
                        ['is_benar' => $isBenar, 'poin' => $skorDiperoleh]
                    );
                } else { // isian, esai
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? $jawaban ?? '',
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                }
            }

            $koreksiPerSoal[] = [
                'soal_id' => $soalId,
                'jenis' => $jenis,
                'jawaban' => $jawaban,
                'is_benar' => $isBenar,
                'bobot_diperoleh' => $skorDiperoleh
            ];
        }

        $nilaiAkhir = $totalBobotMaks > 0 ? round(($totalPoinBenar / $totalBobotMaks) * 100, 2) : 0;

        return [
            'nilai' => $nilaiAkhir,
            'total_bobot' => $totalBobotMaks,
            'poin_benar' => $totalPoinBenar,
            'poin_salah' => max(0, $totalBobotMaks - $totalPoinBenar),
            'soal_benar' => $jumlahSoalBenar,
            'soal_salah' => $jumlahSoalSalah,
            'arsip_jawaban' => $arsipJawaban,
            'koreksi_detail' => $koreksiPerSoal
        ];
    }
    public function koreksiPesertaUlang(array $ujian, array $hasilUjian, array $jawabanJson): array
    {
        static $cacheSoal = [];
        static $cacheOpsi = [];

        $bankSoalId = $ujian['bank_soal_id'];

        if (!isset($cacheSoal[$bankSoalId])) {
            $cacheSoal[$bankSoalId] = $this->soalModel
                ->where('bank_soal_id', $bankSoalId)
                ->findAll();

            $soalIds = array_column($cacheSoal[$bankSoalId], 'id');
            $opsiList = $this->soalOpsiModel
                ->whereIn('soal_id', $soalIds)
                ->findAll();

            foreach ($opsiList as $opsi) {
                $cacheOpsi[$bankSoalId][$opsi['soal_id']][] = $opsi;
            }
        }

        $daftarSoal = $cacheSoal[$bankSoalId];
        $opsiBank = $cacheOpsi[$bankSoalId];
        $jawabanMap = $jawabanJson;

        // 🔹 Variabel total
        $totalPoinBenar = 0;    // total semua soal
        $totalBobotMaks = 0;

        $totalPoinPG = 0;       // total soal non-esai
        $totalBobotPG = 0;

        $totalPoinEsai = 0;
        $totalBobotEsai = 0;

        $jumlahSoalBenar = 0;
        $jumlahSoalSalah = 0;
        $arsipJawaban = [];
        $koreksiPerSoal = [];

        foreach ($daftarSoal as $soal) {
            $soalId = $soal['id'];
            $jenis = $soal['jenis_soal'];
            $daftarOpsi = $opsiBank[$soalId] ?? [];
            $jawaban = $jawabanMap[$soalId] ?? null;

            $skorDiperoleh = 0;
            $isBenar = false;

            switch ($jenis) {
                case 'pg':
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiDipilih = array_filter($daftarOpsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot = $opsiDipilih ? (int) array_values($opsiDipilih)[0]['bobot'] : 0;
                    $skorDiperoleh = $labelJawaban ? $bobot : 0;

                    $opsiBenar = array_filter($daftarOpsi, fn($o) => !empty($o['is_true']));
                    $labelBenar = $opsiBenar ? array_values($opsiBenar)[0]['label'] ?? null : null;
                    $isBenar = $labelJawaban && $labelJawaban === $labelBenar;
                    break;

                case 'mpg':
                    $jawabanList = $jawaban['values'] ?? [];
                    $skorDiperoleh = 0;
                    foreach ($jawabanList as $label) {
                        $opsi = array_values(array_filter($daftarOpsi, fn($o) => $o['label'] === $label));
                        if ($opsi) $skorDiperoleh += (int) $opsi[0]['bobot'];
                    }
                    $labelBenar = array_column(array_filter($daftarOpsi, fn($o) => !empty($o['is_true'])), 'label');
                    sort($labelBenar);
                    $labelJawaban = $jawabanList;
                    sort($labelJawaban);
                    $isBenar = !empty($jawabanList) && $labelBenar === $labelJawaban;
                    break;

                case 'benar_salah':
                    $skorDiperoleh = 0;
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $label = $opsi['label'];
                        $jawabanPeserta = strtolower($jawaban[$label] ?? '');
                        $jawabanBenar = strtolower($opsi['is_true'] ? 'benar' : 'salah');
                        $bobot = (int) $opsi['bobot'];

                        if ($jawabanPeserta === '') continue;

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
                        if ($jawab === null || $jawab === '') continue;

                        if ($jawab === $opsi['pasangan']) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'isian':
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;
                    $kunciJawaban = json_decode($soal['jawaban'], true) ?? [];
                    $isBenar = $jawabanUser !== '' && $this->cocokIsian($jawabanUser, $kunciJawaban);
                    $skorDiperoleh = $isBenar ? (int) $soal['bobot'] : 0;
                    break;

                case 'esai':
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;

                    $poinPeserta = null;
                    if (is_array($jawaban) && array_key_exists('poin', $jawaban)) {
                        $poinPeserta = floatval($jawaban['poin']);
                    }

                    $maxBobot = is_numeric($soal['bobot']) ? floatval($soal['bobot']) : 0.0;

                    if ($poinPeserta === null) {
                        $skorDiperoleh = 0;
                        $isBenar = false;
                    } else {
                        if ($poinPeserta < 0) $poinPeserta = 0;
                        if ($poinPeserta > $maxBobot) $poinPeserta = $maxBobot;

                        $skorDiperoleh = $poinPeserta;
                        $isBenar = $poinPeserta > 0;
                    }

                    $totalPoinEsai += $skorDiperoleh;
                    $totalBobotEsai += $maxBobot;
                    break;
            }

            $totalBobotMaks += (int) $soal['bobot'];
            $totalPoinBenar += $skorDiperoleh;

            // 🔹 Hitung PG/non-esai
            if ($jenis !== 'esai') {
                $totalPoinPG += $skorDiperoleh;
                $totalBobotPG += (int) $soal['bobot'];
            }

            if ($isBenar) $jumlahSoalBenar++;
            else $jumlahSoalSalah++;

            if ($jawaban !== null) {
                if ($jenis === 'pg') {
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? null,
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif ($jenis === 'mpg') {
                    $arsipJawaban[$soalId] = [
                        'values' => $jawaban['values'] ?? [],
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif (in_array($jenis, ['benar_salah', 'jodohkan'])) {
                    $arsipJawaban[$soalId] = array_merge(
                        $jawaban,
                        ['is_benar' => $isBenar, 'poin' => $skorDiperoleh]
                    );
                } else {
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? $jawaban ?? '',
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                }
            }

            $koreksiPerSoal[] = [
                'soal_id' => $soalId,
                'jenis' => $jenis,
                'jawaban' => $jawaban,
                'is_benar' => $isBenar,
                'bobot_diperoleh' => $skorDiperoleh
            ];
        }

        // 🔹 Hitung nilai akhir
        $nilaiTotal = $totalBobotMaks > 0 ? round(($totalPoinBenar / $totalBobotMaks) * 100, 2) : 0;
        // nilai PG dihitung proporsional terhadap total bobot supaya <= nilai total
        $nilaiPG = $totalBobotPG > 0 ? round(($totalPoinPG / $totalBobotMaks) * 100, 2) : 0;
        $nilaiEsai = $totalBobotEsai > 0 ? round(($totalPoinEsai / $totalBobotMaks) * 100, 2) : 0;

        return [
            'nilai_total' => $nilaiTotal,
            'nilai_pg' => $nilaiPG,
            'nilai_esai' => $nilaiEsai,
            'total_bobot' => $totalBobotMaks,
            'poin_benar' => $totalPoinBenar,
            'poin_pg' => $totalPoinPG,
            'poin_essai' => $totalPoinEsai,
            'poin_salah' => max(0, $totalBobotMaks - $totalPoinBenar),
            'soal_benar' => $jumlahSoalBenar,
            'soal_salah' => $jumlahSoalSalah,
            'arsip_jawaban' => $arsipJawaban,
            'koreksi_detail' => $koreksiPerSoal
        ];
    }




    public function koreksiPesertaUlangxx(array $ujian, array $hasilUjian, array $jawabanJson): array
    {
        // 🔹 Cache soal dan opsi
        static $cacheSoal = [];
        static $cacheOpsi = [];

        $bankSoalId = $ujian['bank_soal_id'];

        if (!isset($cacheSoal[$bankSoalId])) {
            $cacheSoal[$bankSoalId] = $this->soalModel
                ->where('bank_soal_id', $bankSoalId)
                ->findAll();

            $soalIds = array_column($cacheSoal[$bankSoalId], 'id');
            $opsiList = $this->soalOpsiModel
                ->whereIn('soal_id', $soalIds)
                ->findAll();

            foreach ($opsiList as $opsi) {
                $cacheOpsi[$bankSoalId][$opsi['soal_id']][] = $opsi;
            }
        }

        $daftarSoal = $cacheSoal[$bankSoalId];
        $opsiBank = $cacheOpsi[$bankSoalId];

        // 🔹 Peta jawaban peserta
        $jawabanMap = $jawabanJson; // key = soal_id

        $totalPoinBenar = 0;
        $totalBobotMaks = 0;
        $jumlahSoalBenar = 0;
        $jumlahSoalSalah = 0;
        $arsipJawaban = [];
        $koreksiPerSoal = [];

        foreach ($daftarSoal as $soal) {
            $soalId = $soal['id'];
            $jenis = $soal['jenis_soal'];
            $daftarOpsi = $opsiBank[$soalId] ?? [];
            $jawaban = $jawabanMap[$soalId] ?? null;

            $skorDiperoleh = 0;
            $isBenar = false;

            switch ($jenis) {
                case 'pg':
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiDipilih = array_filter($daftarOpsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot = $opsiDipilih ? (int) array_values($opsiDipilih)[0]['bobot'] : 0;
                    $skorDiperoleh = $labelJawaban ? $bobot : 0;

                    $opsiBenar = array_filter($daftarOpsi, fn($o) => !empty($o['is_true']));
                    $labelBenar = $opsiBenar ? array_values($opsiBenar)[0]['label'] ?? null : null;
                    $isBenar = $labelJawaban && $labelJawaban === $labelBenar;
                    break;

                case 'mpg':
                    $jawabanList = $jawaban['values'] ?? [];
                    $skorDiperoleh = 0;
                    foreach ($jawabanList as $label) {
                        $opsi = array_values(array_filter($daftarOpsi, fn($o) => $o['label'] === $label));
                        if ($opsi) $skorDiperoleh += (int) $opsi[0]['bobot'];
                    }
                    $labelBenar = array_column(array_filter($daftarOpsi, fn($o) => !empty($o['is_true'])), 'label');
                    sort($labelBenar);
                    $labelJawaban = $jawabanList;
                    sort($labelJawaban);
                    $isBenar = !empty($jawabanList) && $labelBenar === $labelJawaban;
                    break;

                case 'benar_salah':
                    $skorDiperoleh = 0;
                    $isBenar = true;
                    foreach ($daftarOpsi as $opsi) {
                        $label = $opsi['label'];
                        $jawabanPeserta = strtolower($jawaban[$label] ?? '');
                        $jawabanBenar = strtolower($opsi['is_true'] ? 'benar' : 'salah');
                        $bobot = (int) $opsi['bobot'];

                        if ($jawabanPeserta === '') continue;

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
                        if ($jawab === null || $jawab === '') continue;

                        if ($jawab === $opsi['pasangan']) {
                            $skorDiperoleh += $bobot;
                        } else {
                            if ($bobot < 0) $skorDiperoleh += $bobot;
                            $isBenar = false;
                        }
                    }
                    break;

                case 'isian':
                    // Jawaban isian: dicocokkan otomatis dengan kunci (existing behavior)
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;
                    $kunciJawaban = json_decode($soal['jawaban'], true) ?? [];
                    $isBenar = $jawabanUser !== '' && $this->cocokIsian($jawabanUser, $kunciJawaban);
                    $skorDiperoleh = $isBenar ? (int) $soal['bobot'] : 0;
                    break;

                case 'esai':
                    // Jawaban esai: nilai diambil dari jawaban['poin'] (manual). Jika belum ada, anggap 0.
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;

                    // Ambil poin jika sudah diberikan oleh penilai (bisa decimal)
                    $poinPeserta = null;
                    if (is_array($jawaban) && array_key_exists('poin', $jawaban)) {
                        $poinPeserta = floatval($jawaban['poin']);
                    }

                    // Bobot maksimal untuk soal
                    $maxBobot = is_numeric($soal['bobot']) ? floatval($soal['bobot']) : 0.0;

                    if ($poinPeserta === null) {
                        // Belum dikoreksi -> no score yet
                        $skorDiperoleh = 0;
                        $isBenar = false;
                    } else {
                        // Batasi antara 0 .. maxBobot
                        if ($poinPeserta < 0) $poinPeserta = 0;
                        if ($poinPeserta > $maxBobot) $poinPeserta = $maxBobot;

                        // Untuk perhitungan poin gunakan nilai yang diberikan penilai
                        $skorDiperoleh = $poinPeserta;
                        // Anggap soal "benar" bila diberi poin lebih dari 0 (atau kamu bisa pakai flag dikoreksi)
                        $isBenar = $poinPeserta > 0;
                    }
                    break;
            }

            // ⚡ Tambahkan total bobot maksimal untuk semua jenis soal
            $totalBobotMaks += (int) $soal['bobot'];

            $totalPoinBenar += $skorDiperoleh;
            if ($isBenar) $jumlahSoalBenar++;
            else $jumlahSoalSalah++;

            // 🔹 Arsip jawaban
            if ($jawaban !== null) {
                if ($jenis === 'pg') {
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? null,
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif ($jenis === 'mpg') {
                    $arsipJawaban[$soalId] = [
                        'values' => $jawaban['values'] ?? [],
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                } elseif (in_array($jenis, ['benar_salah', 'jodohkan'])) {
                    $arsipJawaban[$soalId] = array_merge(
                        $jawaban,
                        ['is_benar' => $isBenar, 'poin' => $skorDiperoleh]
                    );
                } else { // isian, esai
                    $arsipJawaban[$soalId] = [
                        'value' => $jawaban['value'] ?? $jawaban ?? '',
                        'is_benar' => $isBenar,
                        'poin' => $skorDiperoleh
                    ];
                }
            }

            // 🔹 Koreksi per soal
            $koreksiPerSoal[] = [
                'soal_id' => $soalId,
                'jenis' => $jenis,
                'jawaban' => $jawaban,
                'is_benar' => $isBenar,
                'bobot_diperoleh' => $skorDiperoleh
            ];
        }

        $nilaiAkhir = $totalBobotMaks > 0 ? round(($totalPoinBenar / $totalBobotMaks) * 100, 2) : 0;

        return [
            'nilai_pg' => $nilaiAkhir,
            'total_bobot' => $totalBobotMaks,
            'poin_benar' => $totalPoinBenar,
            'poin_salah' => max(0, $totalBobotMaks - $totalPoinBenar),
            'soal_benar' => $jumlahSoalBenar,
            'soal_salah' => $jumlahSoalSalah,
            'arsip_jawaban' => $arsipJawaban,
            'koreksi_detail' => $koreksiPerSoal
        ];
    }



    private function cocokIsian(string $jawaban, $kunciJawaban): bool
    {
        $normalizedUser = $this->normalizeTextAdvanced($jawaban);

        // Pastikan array
        $kunciJawaban = is_array($kunciJawaban) ? $kunciJawaban : [$kunciJawaban];

        foreach ($kunciJawaban as $kunci) {
            if ($normalizedUser === $this->normalizeTextAdvanced((string)$kunci)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeTextAdvanced(?string $text): string
    {
        $text = strtolower(trim($text ?? ''));

        // Hilangkan semua tanda baca (kecuali huruf dan angka)
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Ganti spasi ganda atau tab dengan satu spasi
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }
}
