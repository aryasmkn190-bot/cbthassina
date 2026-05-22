<?= $this->extend('Layout/main_peserta'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">

        <div class="mt-3 mb-3">
            <h4 class="fw-bold">Hasil Ujian: <?= esc($ujian['nama_bank_soal']) ?></h4>
            <p class="text-muted mb-0">Kode Ujian: <?= esc($ujian['kode_ujian']) ?></p>

            <a href="<?= base_url('peserta/home') ?>" class="btn btn-secondary mt-2">
                <i data-feather="arrow-left"></i> Kembali
            </a>
        </div>


        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Ringkasan Nilai</h5>
                <div class="row">
                    <div class="col-md-3"><strong>Nilai Akhir:</strong> <?= esc($hasil['nilai_total']) ?></div>
                    <div class="col-md-3"><strong>Soal Benar:</strong> <?= esc($hasil['soal_benar']) ?></div>
                    <div class="col-md-3"><strong>Soal Salah:</strong> <?= esc($hasil['soal_salah']) ?></div>
                    <div class="col-md-3"><strong>Poin Benar:</strong> <?= esc($hasil['poin_benar']) ?> / <?= esc($hasil['poin_maksimal']) ?></div>
                </div>
            </div>
        </div>

        <?php if ($ujian['tampil_pembahasan'] == '1') { ?>
            <?php foreach ($soalList as $index => $soal): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6>Soal <?= $index + 1 ?>:</h6>
                        <div class="mb-2"><?= $soal['pertanyaan'] ?></div>

                        <?php
                        $jawaban = $jawabanMap[$soal['id']] ?? null;
                        $poin = $jawaban['poin'] ?? 0;
                        $isBenar = $jawaban['is_benar'] ?? false;
                        $belumDijawab = $jawaban === null;
                        ?>

                        <?php if (!empty($soal['opsi'])): ?>
                            <ul class="list-group mb-2">
                                <?php foreach ($soal['opsi'] as $opsi):
                                    $label = $opsi['label'];
                                    $jawabanPeserta = $jawabanMap[$soal['id']][$label] ?? null;
                                    $benar = !empty($opsi['is_true']);
                                    $isDipilih = $jawabanPeserta === 'Benar' || $jawabanPeserta === true ||
                                        ($jawabanMap[$soal['id']]['value'] ?? null) === $label ||
                                        in_array($label, $jawabanMap[$soal['id']]['values'] ?? []);

                                    $class = $benar ? 'list-group-item-success' : '';
                                    if ($isDipilih && !$benar) $class = 'list-group-item-danger';
                                ?>
                                    <li class="list-group-item <?= $class ?>">
                                        <strong><?= $label ?>.</strong> <?= $opsi['teks'] ?>
                                        <?php if ($isDipilih): ?>
                                            <span class="badge bg-info float-end">Jawaban Kamu</span>
                                        <?php endif; ?>
                                        <?php if ($benar): ?>
                                            <span class="badge bg-success float-end me-2">Kunci</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div>
                            <strong>Status:</strong>
                            <?php if ($belumDijawab): ?>
                                <span class="badge bg-warning">Belum Dijawab</span>
                            <?php else: ?>
                                <?= $isBenar ? '<span class="badge bg-success">Benar</span>' : '<span class="badge bg-danger">Salah</span>' ?>
                                | <strong>Poin:</strong> <?= $poin ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php } ?>

        <a href="<?= base_url('peserta/home') ?>" class="btn btn-secondary">Kembali ke Beranda</a>
    </div>
</div>

<?= $this->endSection(); ?>