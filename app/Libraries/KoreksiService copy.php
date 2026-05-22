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
        $daftarSoal = $this->soalModel
            ->where('bank_soal_id', $ujian['bank_soal_id'])
            ->findAll();

        $jawabanMap = [];
        foreach ($jawabanPesertaList as $jawaban) {
            $jawabanMap[$jawaban['soal_id']] = json_decode($jawaban['jawaban'], true);
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
            $daftarOpsi = $this->soalOpsiModel->where('soal_id', $soalId)->findAll();
            $jawaban = $jawabanMap[$soalId] ?? null;

            $skorDiperoleh = 0;
            $isBenar = false;

            // ======== koreksi per jenis soal =========
            switch ($jenis) {
                case 'pg':
                    $labelJawaban = $jawaban['value'] ?? null;
                    $opsiDipilih = array_filter($daftarOpsi, fn($o) => $o['label'] === $labelJawaban);
                    $bobot = $opsiDipilih ? (int) array_values($opsiDipilih)[0]['bobot'] : 0;
                    $skorDiperoleh = $bobot;

                    $opsiBenar = array_filter($daftarOpsi, fn($o) => !empty($o['is_true']));
                    $labelBenar = $opsiBenar ? array_values($opsiBenar)[0]['label'] ?? null : null;
                    $isBenar = $labelJawaban === $labelBenar;
                    break;

                case 'mpg':
                    $jawabanList = $jawaban['values'] ?? [];
                    foreach ($jawabanList as $label) {
                        $opsi = array_values(array_filter($daftarOpsi, fn($o) => $o['label'] === $label));
                        if ($opsi) $skorDiperoleh += (int) $opsi[0]['bobot'];
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
                        $jawabanPeserta = strtolower($jawaban[$label] ?? ''); // ubah jawaban peserta jadi lowercase
                        $jawabanBenar = strtolower($opsi['is_true'] ? 'Benar' : 'Salah'); // ubah kunci jadi lowercase
                        $bobot = (int) $opsi['bobot'];

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

                case 'isian':
                case 'esai':
                    $jawabanUser = is_array($jawaban) ? ($jawaban['value'] ?? '') : (string) $jawaban;
                    $kunciJawaban = json_decode($soal['jawaban'], true) ?? [];
                    $isBenar = $this->cocokIsian($jawabanUser, $kunciJawaban);
                    $skorDiperoleh = $isBenar ? (int) $soal['bobot'] : 0;
                    $totalBobotMaks += (int) $soal['bobot'];
                    break;
            }

            // bobot maksimal
            if ($jenis === 'jodohkan' || $jenis === 'benar_salah') {
                foreach ($daftarOpsi as $opsi) {
                    if ((int) $opsi['bobot'] > 0) $totalBobotMaks += (int) $opsi['bobot'];
                }
            } else {
                // benar
                foreach ($daftarOpsi as $opsi) {
                    if (!empty($opsi['is_true']) && (int) $opsi['bobot'] > 0) {
                        $totalBobotMaks += (int) $opsi['bobot'];
                    }
                }
            }

            $totalPoinBenar += $skorDiperoleh;
            if ($isBenar) $jumlahSoalBenar++;
            else $jumlahSoalSalah++;

            // arsip jawaban
            if ($jawaban !== null) {
                $arsipJawaban[$soalId] = is_array($jawaban)
                    ? array_merge($jawaban, ['is_benar' => $isBenar, 'poin' => $skorDiperoleh])
                    : ['value' => $jawaban, 'is_benar' => $isBenar, 'poin' => $skorDiperoleh];
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
            'poin_salah' => $totalBobotMaks - $totalPoinBenar,
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
