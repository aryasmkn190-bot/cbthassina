<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .task-card {
        border-radius: 18px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }
    .task-card .badge {
        font-weight: 700;
    }
    body.dark .task-card {
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
                <h4 class="mb-1 fw-bold text-dark">Tugas & Pekerjaan Rumah (LMS)</h4>
                <p class="mb-0 text-muted small">Kelola tugas mandiri dan kumpulkan jawaban secara digital.</p>
            </div>
            <div class="text-warning">
                <i data-feather="edit-3" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="row">
            <!-- Left Side: Tasks Feed -->
            <div class="col-12">
                <h5 class="fw-bold mb-3 text-dark d-md-none">Tugas & PR</h5>
                
                <div id="tugas-feed-container">
                    <?php if (empty($tugas)): ?>
                        <div class="card p-5 rounded-4 shadow-sm border-0 text-center bg-white">
                            <i data-feather="smile" class="text-muted mx-auto mb-3" style="width: 48px; height: 48px;"></i>
                            <h6 class="fw-bold text-dark">Tidak Ada Tugas</h6>
                            <p class="text-muted mb-0 small">Hebat! Semua tugas atau PR kelas Anda sudah selesai atau tidak ada yang aktif.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($tugas as $t): 
                                $deadline = new DateTime($t['tenggat_waktu']);
                                $now = new DateTime();
                                $isPassed = $deadline < $now;
                                $formatDeadline = $deadline->format('d M Y, H:i') . ' WIB';

                                $statusHtml = '';
                                $formHtml = '';
                                $borderClass = 'border-danger';

                                if ($t['submission']) {
                                    $borderClass = 'border-success';
                                    $score = (int)$t['submission']['nilai'];
                                    if ($score > 0) {
                                        $statusHtml = '<span class="badge bg-success py-1 px-2.5 rounded-pill mb-1">Nilai: ' . $score . '</span>';
                                        if ($t['submission']['catatan_guru']) {
                                            $statusHtml .= '<div class="small text-muted mt-1">Umpan Balik: <em>"' . esc($t['submission']['catatan_guru']) . '"</em></div>';
                                        }
                                    } else {
                                        $statusHtml = '<span class="badge bg-info py-1 px-2.5 rounded-pill mb-1">Dikumpulkan (Menunggu Penilaian)</span>';
                                    }
                                    $statusHtml .= '<div class="small mt-1"><a href="' . base_url() . esc($t['submission']['file_path']) . '" target="_blank" class="text-primary fw-bold"><i data-feather="file" style="width:12px;height:12px;margin-top:-2px;"></i> Lihat File Anda</a></div>';
                                } else {
                                    if ($isPassed) {
                                        $borderClass = 'border-danger';
                                        $statusHtml = '<span class="badge bg-danger py-1 px-2.5 rounded-pill mb-1">Terlambat</span>';
                                    } else {
                                        $borderClass = 'border-warning';
                                        $statusHtml = '<span class="badge bg-warning text-dark py-1 px-2.5 rounded-pill mb-1">Belum Dikumpulkan</span>';
                                    }
                                }

                                if (!$t['submission'] || (int)$t['submission']['nilai'] === 0) {
                                    $formHtml = '
                                        <form class="mt-3 p-3 bg-light rounded-3 border form-submit-tugas" enctype="multipart/form-data">
                                            <input type="hidden" name="tugas_id" value="' . $t['id'] . '">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-sm-8 col-12">
                                                    <input type="file" name="file" class="form-control form-control-sm" required accept=".pdf,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg">
                                                </div>
                                                <div class="col-sm-4 col-12 d-grid">
                                                    <button type="button" class="btn btn-primary btn-sm btn-submit-file py-2 rounded-pill"><i data-feather="upload-cloud" class="me-1" style="width: 14px; height: 14px;"></i>Kirim</button>
                                                </div>
                                            </div>
                                            <div class="text-muted mt-1.5" style="font-size:0.68rem;"><i data-feather="info" class="me-0.5" style="width:10px;height:10px;"></i> Max 10MB (PDF, Word, Zip, Gambar)</div>
                                        </form>
                                    ';
                                }
                            ?>
                                <div class="col-md-6 col-12">
                                    <div class="card task-card p-4 h-100 border-start border-4 <?= $borderClass ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                            <span class="badge bg-primary-subtle text-primary py-1 px-2 rounded-pill" style="font-size: 0.72rem; background: rgba(79, 70, 229, 0.08); color: #4f46e5 !important;"><?= esc($t['mata_pelajaran']) ?></span>
                                            <div><?= $statusHtml ?></div>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;"><?= esc($t['judul']) ?></h5>
                                        <p class="text-muted small mb-3 flex-grow-1" style="white-space: pre-wrap; font-size: 0.85rem;"><?= esc($t['deskripsi'] ?: 'Tidak ada deskripsi.') ?></p>
                                        
                                        <div class="text-muted small border-top pt-2.5 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                            <i data-feather="clock" style="width:13px;height:13px;"></i> 
                                            <span>Tenggat: <strong class="text-dark"><?= $formatDeadline ?></strong></span>
                                        </div>
                                        <?= $formHtml ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
        $(document).on('click', '.btn-submit-file', function(e) {
            e.preventDefault();
            const btn = $(this);
            const form = btn.closest('form');
            const fileInput = form.find('input[type="file"]')[0];

            if (fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih File',
                    text: 'Silakan pilih file jawaban terlebih dahulu!',
                    customClass: { popup: 'rounded-4' }
                });
                return;
            }

            const formData = new FormData(form[0]);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Mengirim...');

            $.ajax({
                url: '<?= base_url('peserta/akademik/submit-tugas') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            customClass: { popup: 'rounded-4' }
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        let errors = '';
                        if (typeof res.message === 'object') {
                            $.each(res.message, (k, v) => errors += v + '\n');
                        } else {
                            errors = res.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errors,
                            customClass: { popup: 'rounded-4' }
                        });
                        btn.prop('disabled', false).html('<i data-feather="upload-cloud" class="me-1" style="width: 14px; height: 14px;"></i>Kirim');
                        feather.replace();
                    }
                },
                error: () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server.',
                        customClass: { popup: 'rounded-4' }
                    });
                    btn.prop('disabled', false).html('<i data-feather="upload-cloud" class="me-1" style="width: 14px; height: 14px;"></i>Kirim');
                    feather.replace();
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
