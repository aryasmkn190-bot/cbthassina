<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0 fw-bold">Absensi Kehadiran Siswa</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="openManualModal()" class="btn btn-outline-primary btn-sm">
                        <i data-feather="user-plus"></i> Catat Absen Manual
                    </button>
                    <button type="button" id="refreshList" onclick="loadAttendance()" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Start QR Session Section -->
            <div class="card shadow-sm border-0 mb-4 bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8">
                            <h5 class="fw-bold text-white mb-2"><i data-feather="cpu"></i> Mulai Absensi QR Otomatis</h5>
                            <p class="text-white-50 mb-md-0 small">Pilih kelas di bawah untuk memunculkan QR Code di proyektor/layar kelas. Siswa dapat langsung memindai QR Code menggunakan handphone mereka untuk mencatat kehadiran secara real-time.</p>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <select id="qrKelasSelect" class="form-control form-control-sm text-dark bg-white">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas as $k): ?>
                                        <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-dark btn-sm fw-bold" onclick="startQrSession()">Mulai QR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label text-muted small fw-bold">Filter Kelas</label>
                    <select id="filterKelas" class="form-control form-control-sm" onchange="loadAttendance()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-muted small fw-bold">Filter Tanggal</label>
                    <input type="date" id="filterTanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="loadAttendance()">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i data-feather="search"></i>
                        </span>
                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari nama siswa..." oninput="renderAttendance()">
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Waktu Pindai</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceList">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>

        </div>
    </div>
</div>

<!-- Modal Manual Attendance Form -->
<div class="modal fade" id="modal_manual" tabindex="-1">
    <div class="modal-dialog">
        <form id="manualForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catat Absensi Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="manualErrorMessages"></div>
                
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select id="manualKelasSelect" class="form-control form-control-sm" required onchange="fetchStudentsForManual(this.value)">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <select id="manualSiswaSelect" name="peserta_id" class="form-control form-control-sm" required disabled>
                        <option value="">-- Pilih Siswa (Pilih Kelas Terlebih Dahulu) --</option>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Waktu Scan</label>
                        <input type="time" name="waktu_scan" class="form-control form-control-sm" required value="<?= date('H:i') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Kehadiran</label>
                    <select name="status" class="form-control form-control-sm" required>
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="alfa">Alfa</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveManual()" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Status Form -->
<div class="modal fade" id="modal_edit_status" tabindex="-1">
    <div class="modal-dialog">
        <form id="editStatusForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ubah Status Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editErrorMessages"></div>
                <input type="hidden" id="editAbsensiId">
                
                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <input type="text" id="editSiswaName" class="form-control form-control-sm" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Kehadiran</label>
                    <select id="editStatusSelect" name="status" class="form-control form-control-sm" required>
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="alfa">Alfa</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveEditStatus()" class="btn btn-primary btn-sm">Perbarui Status</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let attendanceData = [];
    let currentPage = 1;
    const perPage = 15;

    function startQrSession() {
        const kelasId = $('#qrKelasSelect').val();
        if (!kelasId) {
            Swal.fire('Perhatian', 'Silakan pilih kelas terlebih dahulu!', 'info');
            return;
        }
        window.location.href = '<?= base_url('panel/akademik/absensi/qr') ?>/' + kelasId;
    }

    function loadAttendance() {
        const kelasId = $('#filterKelas').val();
        const tanggal = $('#filterTanggal').val();

        $.get('<?= base_url('panel/akademik/absensi/list') ?>', { kelas_id: kelasId, tanggal: tanggal }, function(res) {
            if (res.status) {
                attendanceData = res.data;
                renderAttendance();
            } else {
                $('#attendanceList').html(`<tr><td colspan="6" class="text-center py-4 text-danger">${res.message}</td></tr>`);
            }
        });
    }

    function renderAttendance() {
        const query = $('#searchBox').val().toLowerCase();
        let filtered = attendanceData;

        if (query) {
            filtered = filtered.filter(a => a.nama_peserta.toLowerCase().includes(query));
        }

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        if (pageData.length === 0) {
            $('#attendanceList').html('<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data absensi ditemukan untuk kelas dan tanggal ini.</td></tr>');
            $('#pagination').html('');
            return;
        }

        let html = '';
        pageData.forEach((a, idx) => {
            let badgeClass = 'bg-light-success text-success';
            if (a.status === 'sakit') badgeClass = 'bg-light-info text-info';
            if (a.status === 'izin') badgeClass = 'bg-light-warning text-warning';
            if (a.status === 'alfa') badgeClass = 'bg-light-danger text-danger';

            html += `
                <tr>
                    <td class="ps-3">${start + idx + 1}</td>
                    <td class="fw-bold text-dark">${a.nama_peserta} <br><small class="text-muted">${a.username_peserta}</small></td>
                    <td><span class="badge bg-light-primary text-primary">${a.nama_kelas || '-'}</span></td>
                    <td><i data-feather="clock" class="text-muted p-1"></i> ${a.waktu_scan}</td>
                    <td><span class="badge ${badgeClass} text-uppercase font-weight-bold px-3 py-1 rounded-pill">${a.status}</span></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-info p-1 me-1 px-2" onclick="editStatus('${a.id}', '${a.nama_peserta}', '${a.status}')">
                            <i data-feather="edit-2"></i> Ubah
                        </button>
                        <button class="btn btn-sm btn-outline-danger p-1 px-2" onclick="deleteAttendance('${a.id}')">
                            <i data-feather="trash-2"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#attendanceList').html(html);
        feather.replace();
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = '';
        if (totalPages <= 1) {
            $('#pagination').html('');
            return;
        }
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a href="javascript:void(0)" class="page-link" onclick="goPage(${i})">${i}</a>
            </li>`;
        }
        $('#pagination').html(html);
    }

    function goPage(page) {
        currentPage = page;
        renderAttendance();
    }

    function openManualModal() {
        $('#manualForm')[0].reset();
        $('#manualErrorMessages').html('');
        $('#manualSiswaSelect').html('<option value="">-- Pilih Siswa (Pilih Kelas Terlebih Dahulu) --</option>').prop('disabled', true);
        $('#modal_manual').modal('show');
    }

    function fetchStudentsForManual(kelasId) {
        if (!kelasId) {
            $('#manualSiswaSelect').html('<option value="">-- Pilih Siswa (Pilih Kelas Terlebih Dahulu) --</option>').prop('disabled', true);
            return;
        }

        $.get('<?= base_url('panel/akademik/absensi/students') ?>/' + kelasId, function(res) {
            if (res.status && res.data.length > 0) {
                let html = '<option value="">-- Pilih Siswa --</option>';
                res.data.forEach(s => {
                    html += `<option value="${s.id}">${s.nama} (${s.username})</option>`;
                });
                $('#manualSiswaSelect').html(html).prop('disabled', false);
            } else {
                $('#manualSiswaSelect').html('<option value="">-- Tidak ada siswa di kelas ini --</option>').prop('disabled', true);
            }
        });
    }

    function saveManual() {
        if ($('#manualForm')[0].checkValidity() === false) {
            $('#manualForm').addClass('was-validated');
            return;
        }

        $.post('<?= base_url('panel/akademik/absensi/create') ?>', $('#manualForm').serialize(), function(res) {
            if (res.status) {
                $('#modal_manual').modal('hide');
                loadAttendance();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul></div>';
                $('#manualErrorMessages').html(errors);
            }
        }, 'json');
    }

    function editStatus(id, name, status) {
        $('#editStatusForm')[0].reset();
        $('#editErrorMessages').html('');
        $('#editAbsensiId').val(id);
        $('#editSiswaName').val(name);
        $('#editStatusSelect').val(status);
        $('#modal_edit_status').modal('show');
    }

    function saveEditStatus() {
        const id = $('#editAbsensiId').val();
        if ($('#editStatusForm')[0].checkValidity() === false) {
            $('#editStatusForm').addClass('was-validated');
            return;
        }

        $.post('<?= base_url('panel/akademik/absensi/update') ?>/' + id, $('#editStatusForm').serialize(), function(res) {
            if (res.status) {
                $('#modal_edit_status').modal('hide');
                loadAttendance();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul></div>';
                $('#editErrorMessages').html(errors);
            }
        }, 'json');
    }

    function deleteAttendance(id) {
        Swal.fire({
            title: 'Yakin hapus absensi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('<?= base_url('panel/akademik/absensi/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        loadAttendance();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadAttendance();
    });
</script>
<?= $this->endSection(); ?>
