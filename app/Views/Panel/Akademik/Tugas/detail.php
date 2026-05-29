<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Back Link & Header -->
            <div class="d-flex align-items-center mb-3 gap-2">
                <a href="<?= base_url('panel/akademik/tugas') ?>" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
                <h5 class="mb-0 fw-bold">Detail Tugas & Jawaban Siswa</h5>
            </div>

            <!-- Task Detail Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <span class="badge bg-light-primary text-primary fw-bold mb-2"><?= esc($tugas['nama_kelas']) ?></span>
                            <h4 class="fw-bold text-dark mb-1"><?= esc($tugas['judul']) ?></h4>
                            <p class="text-primary small mb-3"><i data-feather="book-open" style="width:16px;height:16px;"></i> <?= esc($tugas['mata_pelajaran']) ?></p>
                            
                            <h6 class="fw-bold text-dark mb-1">Instruksi Tugas:</h6>
                            <p class="text-muted bg-light p-3 rounded" style="white-space: pre-wrap;"><?= esc($tugas['deskripsi'] ?: 'Tidak ada instruksi khusus.') ?></p>
                        </div>
                        <div class="col-12 col-md-4 border-start-md">
                            <div class="p-2">
                                <div class="text-muted small mb-1">Tenggat Waktu:</div>
                                <div class="fw-bold text-danger mb-3">
                                    <i data-feather="calendar" style="width:16px;height:16px;"></i> 
                                    <?= date('d M Y, H:i', strtotime($tugas['tenggat_waktu'])) ?> WIB
                                </div>

                                <div class="text-muted small mb-1">Dibuat Pada:</div>
                                <div class="text-dark fw-bold mb-3">
                                    <?= date('d M Y, H:i', strtotime($tugas['created_at'])) ?> WIB
                                </div>

                                <div class="text-muted small mb-1">Jumlah Pengumpulan:</div>
                                <div class="text-dark fw-bold fs-5">
                                    <?= count($submissions) ?> Siswa
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submissions Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">Daftar Pengumpulan Siswa</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Username / NISN</th>
                                    <th>Tanggal Kirim</th>
                                    <th>Berkas</th>
                                    <th>Nilai</th>
                                    <th>Catatan Guru</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($submissions)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Belum ada siswa yang mengumpulkan tugas ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($submissions as $idx => $s): ?>
                                        <tr>
                                            <td class="ps-3"><?= $idx + 1 ?></td>
                                            <td class="fw-bold text-dark"><?= esc($s['nama_peserta']) ?></td>
                                            <td><?= esc($s['username_peserta']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($s['tanggal_kirim'])) ?> WIB</td>
                                            <td>
                                                <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary p-1 px-2">
                                                    <i data-feather="download" style="width:14px;height:14px;"></i> Unduh
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $s['nilai'] >= 75 ? 'success' : 'warning' ?> fs-6 rounded-pill">
                                                    <?= $s['nilai'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small text-truncate d-inline-block" style="max-width:150px;" title="<?= esc($s['catatan_guru']) ?>">
                                                    <?= esc($s['catatan_guru'] ?: '-') ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <button type="button" onclick="grade('<?= $s['id'] ?>', '<?= $s['nilai'] ?>', '<?= esc($s['catatan_guru'], 'js') ?>')" class="btn btn-sm btn-primary p-1 px-2">
                                                    <i data-feather="check-square" style="width:14px;height:14px;"></i> Beri Nilai
                                                </button>
                                            </td>
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

<!-- Modal Grading Form -->
<div class="modal fade" id="modal_grade" tabindex="-1">
    <div class="modal-dialog">
        <form id="gradeForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Penilaian Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="gradeErrorMessages"></div>
                <input type="hidden" id="submissionId">

                <div class="mb-3">
                    <label class="form-label">Nilai (0 - 100)</label>
                    <input type="number" id="gradeValue" name="nilai" class="form-control form-control-sm" required min="0" max="100" placeholder="Contoh: 85">
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan / Umpan Balik Guru</label>
                    <textarea id="gradeFeedback" name="catatan_guru" rows="4" class="form-control form-control-sm" placeholder="Tulis masukan atau catatan untuk siswa di sini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveGrade()" class="btn btn-primary btn-sm">Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    function grade(id, currentNilai, currentCatatan) {
        $('#gradeForm')[0].reset();
        $('#gradeErrorMessages').html('');
        $('#submissionId').val(id);
        $('#gradeValue').val(currentNilai);
        $('#gradeFeedback').val(currentCatatan);
        $('#modal_grade').modal('show');
    }

    function saveGrade() {
        const id = $('#submissionId').val();
        const url = '<?= base_url('panel/akademik/tugas/grade') ?>/' + id;

        if ($('#gradeForm')[0].checkValidity() === false) {
            $('#gradeForm').addClass('was-validated');
            return;
        }

        $.post(url, $('#gradeForm').serialize(), function(res) {
            if (res.status) {
                $('#modal_grade').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                // Reload page to update UI
                setTimeout(() => location.reload(), 1000);
            } else {
                let errors = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul></div>';
                $('#gradeErrorMessages').html(errors);
            }
        }, 'json');
    }

    $(document).ready(function() {
        feather.replace();
    });
</script>

<style>
    @media (min-width: 768px) {
        .border-start-md {
            border-left: 1px solid #dee2e6 !important;
        }
    }
</style>
<?= $this->endSection(); ?>
