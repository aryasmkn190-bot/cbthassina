<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Soal - <?= esc($bankSoal['nama']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 40px;
        }

        h2 {
            text-align: center;
        }

        .soal {
            margin-bottom: 20px;
        }

        .opsi {
            margin-left: 20px;
        }

        .opsi div {
            margin: 3px 0;
        }

        .topik {
            font-style: italic;
            font-size: 13px;
            color: #555;
        }

        hr {
            margin: 30px 0;
        }

        .opsi-teks {
            display: inline-block;
            color: #333;
            font-size: 15px;
        }

        .opsi-teks p {
            margin: 0;
        }
    </style>
    <link href="<?= base_url() ?>src/plugins/src/editors/quill/katex.min.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <h2>Bank Soal: <?= esc($bankSoal['nama']) ?></h2>

    <?php 
    $kunci = $kunci ?? false;
    foreach ($soalList as $i => $soal): 
    ?>
        <div class="soal">
            <div class="topik"><?= ($i + 1) ?>. Topik: <?= esc($soal['topik']['nama'] ?? '-') ?> | Jenis: <?= esc(strtoupper($soal['jenis_soal'])) ?></div>
            <div><strong> <?= $soal['pertanyaan'] ?></strong></div>

            <!-- PG / MPG -->
            <?php if (in_array($soal['jenis_soal'], ['pg', 'mpg'])): ?>
                <div class="opsi">
                    <?php foreach ($soal['opsi'] as $opsi): ?>
                        <div class="d-flex" style="margin: 3px 0;">
                            <strong class="me-2"><?= esc($opsi['label']) ?>.</strong>
                            <span class="opsi-teks" style="<?= ($kunci && $opsi['is_true']) ? 'color: green; font-weight: bold;' : '' ?>">
                                <?= esc($opsi['teks'], 'raw') ?>
                                <?php if ($kunci && $opsi['is_true']): ?>
                                    <strong style="color: green; margin-left: 5px;">[✓ Kunci]</strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Benar / Salah -->
            <?php if ($soal['jenis_soal'] === 'benar_salah'): ?>
                <div class="opsi">
                    <?php foreach ($soal['opsi'] as $opsi): ?>
                        <div class="d-flex" style="margin: 5px 0;">
                            <strong class="me-2"><?= esc($opsi['label']) ?>.</strong>
                            <span class="opsi-teks">
                                <?= esc($opsi['teks'], 'raw') ?>
                                <?php if ($kunci): ?>
                                    <strong style="color: <?= $opsi['is_true'] ? 'green' : 'red' ?>; margin-left: 10px;">
                                        [<?= $opsi['is_true'] ? 'Benar' : 'Salah' ?>]
                                    </strong>
                                <?php else: ?>
                                    <span style="color: #888; margin-left: 10px;">( Benar / Salah )</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Jodohkan -->
            <?php if ($soal['jenis_soal'] === 'jodohkan'): ?>
                <?php if ($kunci): ?>
                    <div class="opsi">
                        <?php foreach ($soal['opsi'] as $opsi): ?>
                            <div class="d-flex" style="margin: 3px 0;">
                                <strong class="me-2"><?= esc($opsi['label']) ?>.</strong>
                                <span class="opsi-teks">
                                    <?= esc($opsi['teks'], 'raw') ?> — <strong style="color: green;">[Pasangan: <?= esc($opsi['pasangan']) ?>]</strong>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php
                    $kiri = [];
                    $kanan = [];
                    foreach ($soal['opsi'] as $opsi) {
                        $kiri[] = $opsi['teks'];
                        if ($opsi['pasangan']) {
                            $kanan[] = $opsi['pasangan'];
                        }
                    }
                    shuffle($kanan);
                    ?>
                    <table style="width: 100%; margin-left: 20px; margin-top: 10px; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <strong>Pernyataan:</strong>
                                <?php foreach ($kiri as $idx => $tek): ?>
                                    <div style="margin: 5px 0;">
                                        <?= ($idx+1) ?>. <?= esc($tek, 'raw') ?> (.....)
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td style="width: 50%; vertical-align: top; padding-left: 20px; border-left: 1px dashed #ccc;">
                                <strong>Pilihan Pasangan:</strong>
                                <?php foreach ($kanan as $idx => $pas): ?>
                                    <div style="margin: 5px 0;">
                                        <?= chr(65 + $idx) ?>. <?= esc($pas) ?>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Isian & Esai -->
            <?php if (in_array($soal['jenis_soal'], ['isian', 'esai'])): ?>
                <div class="opsi" style="margin-top: 10px;">
                    <em>Jawaban:</em>
                    <?php if ($kunci): ?>
                        <div style="border: 1px solid #ccc; padding: 10px; min-height: 30px; background-color: #f9f9f9;">
                            <?= esc($soal['jawaban'], 'raw') ?>
                        </div>
                    <?php else: ?>
                        <div style="border: 1px dashed #ccc; padding: 10px; min-height: 50px;"></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
    <script>
        window.onload = function() {
            window.print();

            // Tunggu proses print selesai atau dibatalkan
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>

</body>

</html>