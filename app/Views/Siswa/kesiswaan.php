<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .kesiswaan-card {
        border-radius: 20px;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .score-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        font-weight: 800;
        margin: 0 auto 12px auto;
        background: #fdf2f8;
        color: #db2777;
        border: 4px solid #fce7f3;
    }
    .score-circle.points-safe {
        background: #ecfdf5;
        color: #059669;
        border-color: #d1fae5;
    }
    body.dark .kesiswaan-card {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
    body.dark .score-circle {
        background: #0f172a;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Data Kesiswaan & Prestasi</h4>
                <p class="mb-0 text-muted small">Pantau catatan prestasi Anda dan daftar poin kedisiplinan/pelanggaran tata tertib.</p>
            </div>
            <div class="text-rose">
                <i data-feather="heart" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="row g-4 mb-4">
            
            <div class="col-md-6 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white kesiswaan-card text-center">
                    <div class="score-circle points-safe">
                        <?= count($prestasi) ?>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Penghargaan / Prestasi</h6>
                    <p class="text-muted small mb-0">Total prestasi akademik maupun non-akademik yang diraih.</p>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <?php
                $totalPoints = 0;
                foreach ($pelanggaran as $p) {
                    $totalPoints += (int)$p['point'];
                }
                $pointsClass = $totalPoints > 0 ? '' : 'points-safe';
                ?>
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white kesiswaan-card text-center">
                    <div class="score-circle <?= $pointsClass ?>">
                        <?= $totalPoints ?>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Akumulasi Poin Pelanggaran</h6>
                    <p class="text-muted small mb-0">Batas maksimal poin pelanggaran sebelum tindakan SP adalah 100.</p>
                </div>
            </div>

        </div>

        <!-- Detail Listing (Prestasi & Pelanggaran tabs) -->
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white kesiswaan-card mb-4">
            
            <!-- Tab Controls -->
            <ul class="nav nav-pills mb-4 justify-content-center bg-light p-1 rounded-pill" id="pills-tab" role="tablist" style="gap: 4px; max-width: 320px; margin: 0 auto;">
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link active py-2.5 rounded-pill w-100 fw-bold small" id="pills-prestasi-tab" data-bs-toggle="pill" data-bs-target="#pills-prestasi" type="button" role="tab" aria-controls="pills-prestasi" aria-selected="true"><i data-feather="award" class="me-1" style="width: 14px; height: 14px; margin-top:-2px;"></i>Prestasi</button>
                </li>
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link py-2.5 rounded-pill w-100 fw-bold small" id="pills-pelanggaran-tab" data-bs-toggle="pill" data-bs-target="#pills-pelanggaran" type="button" role="tab" aria-controls="pills-pelanggaran" aria-selected="false"><i data-feather="alert-triangle" class="me-1" style="width: 14px; height: 14px; margin-top:-2px;"></i>Pelanggaran</button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">
                
                <!-- 1. Prestasi Tab -->
                <div class="tab-pane fade show active" id="pills-prestasi" role="tabpanel" aria-labelledby="pills-prestasi-tab">
                    <h6 class="fw-bold mb-3 text-dark">Daftar Prestasi & Penghargaan</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Prestasi</th>
                                    <th>Tingkat</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($prestasi)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada catatan prestasi terdaftar. Teruslah bersemangat belajar!</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($prestasi as $pr): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($pr['tanggal'])) ?></td>
                                            <td><strong class="text-dark"><?= esc($pr['nama_prestasi']) ?></strong></td>
                                            <td><span class="badge bg-primary"><?= esc($pr['tingkat']) ?></span></td>
                                            <td><span class="badge bg-info"><?= esc($pr['kategori']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Pelanggaran Tab -->
                <div class="tab-pane fade" id="pills-pelanggaran" role="tabpanel" aria-labelledby="pills-pelanggaran-tab">
                    <h6 class="fw-bold mb-3 text-dark">Daftar Poin & Pelanggaran Tata Tertib</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Bentuk Pelanggaran</th>
                                    <th>Golongan / Poin</th>
                                    <th>Tindakan / Pembinaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pelanggaran)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-success py-4">Luar biasa! Tidak ada catatan pelanggaran tata tertib tercatat. Pertahankan sikap disiplin Anda!</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pelanggaran as $pe): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($pe['tanggal'])) ?></td>
                                            <td><strong class="text-dark"><?= esc($pe['nama_pelanggaran']) ?></strong></td>
                                            <td>
                                                <span class="badge bg-danger">-<?= $pe['point'] ?> Poin</span>
                                                <div class="small text-muted mt-1">Kat: <?= esc($pe['kategori']) ?></div>
                                            </td>
                                            <td><p class="small mb-0 text-muted"><?= esc($pe['tindakan'] ?: 'Pemberian teguran lisan.') ?></p></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    // Tab initial logic
</script>
<?= $this->endSection(); ?>
