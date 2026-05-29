<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .rapor-card {
        border-radius: 20px;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .gpa-badge {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #4f46e5;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    body.dark .rapor-card {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Rapor Digital & Hasil Belajar</h4>
                <p class="mb-0 text-muted small">Lihat nilai akhir per semester, rata-rata kelas, dan nilai akademik mata pelajaran.</p>
            </div>
            <div class="text-rose">
                <i data-feather="award" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Filters Card -->
            <div class="col-lg-4 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white Rapor-filters-card">
                    <h5 class="fw-bold mb-3 text-dark"><i data-feather="sliders" class="text-primary me-2" style="width: 18px; height: 18px;"></i>Filter Rapor</h5>
                    
                    <form method="GET" action="<?= base_url('siswa/rapor') ?>">
                        <div class="mb-3">
                            <label class="form-label small text-muted font-weight-bold">Semester</label>
                            <select name="semester" class="form-control rounded-3" onchange="this.form.submit()">
                                <option value="Ganjil" <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Semester Ganjil</option>
                                <option value="Genap" <?= $semester === 'Genap' ? 'selected' : '' ?>>Semester Genap</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted font-weight-bold">Tahun Ajaran</label>
                            <select name="tahun_ajaran" class="form-control rounded-3" onchange="this.form.submit()">
                                <option value="2025/2026" <?= $tahunAjaran === '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
                                <option value="2026/2027" <?= $tahunAjaran === '2026/2027' ? 'selected' : '' ?>>2026/2027</option>
                                <option value="2027/2028" <?= $tahunAjaran === '2027/2028' ? 'selected' : '' ?>>2027/2028</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grades display card -->
            <div class="col-lg-8 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white rapor-card">
                    
                    <?php if (empty($grades)): ?>
                        <div class="text-center py-5 text-muted">
                            <i data-feather="info" class="mb-3 text-muted" style="width: 48px; height: 48px;"></i>
                            <h6 class="fw-bold text-dark">Data Nilai Belum Rilis</h6>
                            <p class="small mb-0">Rapor untuk tahun pelajaran <?= esc($tahunAjaran) ?> (<?= esc($semester) ?>) belum diinput atau dirilis oleh wali kelas.</p>
                        </div>
                    <?php else: 
                        // Compute dynamic GPA/Average
                        $totalScore = 0;
                        foreach ($grades as $g) {
                            $totalScore += (int)$g['nilai'];
                        }
                        $gpa = number_format($totalScore / count($grades), 1);
                    ?>
                        <!-- Summary Header -->
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-4">
                            <div class="gpa-badge"><?= $gpa ?></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5" style="font-size: 0.95rem;">Rata-rata Nilai Rapor</h6>
                                <p class="text-muted small mb-0">Total Pelajaran: <span class="text-primary fw-bold"><?= count($grades) ?> Mata Pelajaran</span></p>
                            </div>
                        </div>

                        <!-- Grades Listing -->
                        <h6 class="fw-bold mb-3 text-dark">Rincian Nilai Mata Pelajaran</h6>
                        <div class="rapor-subjects">
                            <?php foreach ($grades as $g): 
                                $score = (int)$g['nilai'];
                                $barColor = 'bg-primary';
                                if ($g['grade'] === 'C') $barColor = 'bg-warning';
                                if ($g['grade'] === 'D' || $g['grade'] === 'E') $barColor = 'bg-danger';
                            ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <span class="small fw-bold text-dark"><?= esc($g['mata_pelajaran']) ?></span>
                                        <span class="small fw-bold text-primary"><?= $score ?> (<?= esc($g['grade']) ?>)</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 3px;">
                                        <div class="progress-bar <?= $barColor ?> rounded-pill" role="progressbar" style="width: <?= $score ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Print Action -->
                        <div class="text-end border-top pt-3 mt-2">
                            <button class="btn btn-outline-primary rounded-pill py-2 px-4 btn-print-rapor"><i data-feather="printer" class="me-1.5" style="width: 15px; height: 15px; margin-top:-2px;"></i>Cetak Rapor PDF</button>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        $(".btn-print-rapor").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Cetak Rapor',
                text: 'Fitur cetak rapor sedang disiapkan oleh Admin Sekolah.',
                customClass: { popup: 'rounded-4' }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
