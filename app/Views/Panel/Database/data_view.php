<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>
<style>
    .db-table-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-radius: 12px;
        background: #ffffff;
        margin-bottom: 10px;
        border: 1px solid #f1f5f9;
        transition: all .18s ease-in-out;
    }

    .db-table-item:hover {
        border-color: #dbeafe;
        background: #f8fafc;
    }

    .db-left-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .db-table-icon {
        width: 18px;
        height: 18px;
        opacity: .55;
    }

    .db-table-label {
        font-weight: 500;
        color: #334155;
        font-size: 14px;
    }

    .db-badge {
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }

    .db-badge.empty {
        background: #fee2e2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .db-checkbox {
        transform: scale(1.05);
        cursor: pointer;
    }
</style>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading mb-3">
                    <h5 class="">Manajemen Database</h5>
                </div>
                <div class="widget-content">
                    <div class="card style-4 col-md-8">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Backup, Hapus, dan Restore Data</h6>
                        </div>

                        <form id="form-db-action">
                            <div class="card-body">
                                <p class="text-muted mb-2">Centang tabel yang ingin dibackup atau dihapus:</p>

                                <!-- Select All -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label fw-bold text-primary" for="selectAll">Pilih Semua</label>
                                </div>

                                <div class="row">

                                    <!-- LEFT -->
                                    <div class="col-md-6">

                                        <?php
                                        $tablesLeft = [
                                            'peserta',
                                            'kelas',
                                            'jurusan',
                                            'tingkat',
                                            'agama',
                                            'ruang',
                                            'sesi',
                                            'jenis_ujian'
                                        ];

                                        foreach ($tablesLeft as $table):
                                            $count = $counts[$table] ?? 0;
                                            $badgeClass = $count == 0 ? 'empty' : '';
                                        ?>
                                            <div class="db-table-item">
                                                <div class="db-left-group">
                                                    <input class="form-check-input db-checkbox table-check"
                                                        type="checkbox"
                                                        value="<?= $table ?>"
                                                        id="<?= $table ?>">

                                                    <i data-feather="database" style="width:17px;height:17px;opacity:.6;"></i>

                                                    <label class="db-table-label" for="<?= $table ?>">
                                                        <?= ucwords(str_replace('_', ' ', $table)) ?>
                                                    </label>
                                                </div>

                                                <span class="db-badge <?= $badgeClass ?>"><?= $count ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                    <div class="col-md-6">

                                        <?php
                                        $tablesRight = [
                                            'bank_soal',
                                            'topik_soal',
                                            'soal',
                                            'soal_opsi',
                                            'ujian',
                                            'hasil_ujian',
                                            'jawaban',
                                        ];

                                        foreach ($tablesRight as $table):
                                            $count = $counts[$table] ?? 0;
                                            $badgeClass = $count == 0 ? 'empty' : '';
                                        ?>
                                            <div class="db-table-item">
                                                <div class="db-left-group">
                                                    <input class="form-check-input db-checkbox table-check"
                                                        type="checkbox"
                                                        value="<?= $table ?>"
                                                        id="<?= $table ?>">

                                                    <i data-feather="folder" style="width:17px;height:17px;opacity:.6;"></i>

                                                    <label class="db-table-label" for="<?= $table ?>">
                                                        <?= ucwords(str_replace('_', ' ', $table)) ?>
                                                    </label>
                                                </div>

                                                <span class="db-badge <?= $badgeClass ?>"><?= $count ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>


                                </div>

                                <div class="card-footer border-top d-flex flex-wrap gap-2 pt-3">
                                    <button type="button" id="btnBackup" class="btn btn-success">Backup JSON</button>
                                    <button type="button" id="btnBackupSql" class="btn btn-warning">Backup SQL</button>
                                    <button type="button" id="btnHapus" class="btn btn-danger">Hapus Data</button>
                                </div>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Restore Section -->
                    <div class="d-flex flex-column gap-3 col-md-8">
                        <div class="card style-4">
                            <div class="card-body">
                                <h6 class="text-danger mb-2">🔄 Restore dari Backup JSON</h6>
                                <p class="text-muted mb-3">Unggah file JSON hasil backup untuk mengembalikan data.</p>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="jsonRestoreInput" accept=".json">
                                </div>
                                <button type="button" class="btn btn-primary" id="btnRestore">Restore Data</button>
                            </div>
                        </div>

                        <div class="card style-4">
                            <div class="card-body">
                                <h6 class="text-warning mb-2">⚡ Restore dari Backup SQL</h6>
                                <p class="text-muted mb-3">Unggah file SQL hasil backup untuk mengembalikan data (sangat direkomendasikan untuk database besar).</p>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="sqlRestoreInput" accept=".sql">
                                </div>
                                <button type="button" class="btn btn-warning" id="btnRestoreSql">Restore Data</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Select All Checkbox
        $('#selectAll').on('change', function() {
            $('.table-check').prop('checked', this.checked);
        });

        // Uncheck "Select All" if one is unchecked manually
        $('.table-check').on('change', function() {
            if (!this.checked) {
                $('#selectAll').prop('checked', false);
            }
        });

        // Backup JSON
        $('#btnBackup').on('click', function() {
            const tables = getSelectedTables();
            if (tables.length === 0) {
                return Swal.fire('Peringatan', 'Pilih minimal satu tabel untuk dibackup.', 'warning');
            }

            const url = `<?= base_url('panel/pengaturan/database/export-json') ?>?tables=${tables.join(',')}`;
            window.open(url, '_blank');
        });

        // Backup SQL
        $('#btnBackupSql').on('click', function() {
            const tables = getSelectedTables();
            if (tables.length === 0) {
                return Swal.fire('Peringatan', 'Pilih minimal satu tabel untuk dibackup.', 'warning');
            }

            const url = `<?= base_url('panel/pengaturan/database/export-sql') ?>?tables=${tables.join(',')}`;
            window.open(url, '_blank');
        });

        // Restore JSON
        $('#btnRestore').on('click', function() {
            const fileInput = document.getElementById('jsonRestoreInput');
            const file = fileInput.files[0];

            if (!file) {
                return Swal.fire('Peringatan', 'Pilih file JSON terlebih dahulu.', 'warning');
            }

            const formData = new FormData();
            formData.append('backup_json', file);

            Swal.fire({
                title: 'Konfirmasi Restore',
                html: 'Data dari file akan <strong>mengganti data yang ada</strong>.<br>Yakin ingin melanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Restore',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang merestore database dari JSON.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.ajax({
                        url: '<?= base_url('panel/pengaturan/database/restore-json') ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        });

        // Restore SQL
        $('#btnRestoreSql').on('click', function() {
            const fileInput = document.getElementById('sqlRestoreInput');
            const file = fileInput.files[0];

            if (!file) {
                return Swal.fire('Peringatan', 'Pilih file SQL terlebih dahulu.', 'warning');
            }

            const formData = new FormData();
            formData.append('backup_sql', file);

            Swal.fire({
                title: 'Konfirmasi Restore SQL',
                html: 'Data dari file SQL akan <strong>mengganti data yang ada</strong>.<br>Yakin ingin melanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Restore SQL',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang merestore database dari SQL.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.ajax({
                        url: '<?= base_url('panel/pengaturan/database/restore-sql') ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        });

        // Delete
        $('#btnHapus').on('click', function() {
            const tables = getSelectedTables();
            if (tables.length === 0) {
                return Swal.fire('Peringatan', 'Pilih minimal satu tabel untuk dihapus.', 'warning');
            }

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: 'Data pada tabel yang dipilih akan <strong>hilang permanen</strong>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('panel/pengaturan/database/delete-tables') ?>',
                        type: 'POST',
                        data: JSON.stringify({
                            tables
                        }),
                        contentType: 'application/json',
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                }
            });
        });

        function getSelectedTables() {
            return $('.table-check:checked').map(function() {
                return $(this).val();
            }).get();
        }
    });
</script>
<script>
    feather.replace();
</script>
<?= $this->endSection(); ?>