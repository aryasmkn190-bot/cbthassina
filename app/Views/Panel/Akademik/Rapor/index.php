<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0 fw-bold">Kelola Rapor Nilai Siswa</h5>
                <button type="button" id="refreshList" onclick="loadStudents()" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="refresh-cw"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label text-muted small fw-bold">Pilih Kelas</label>
                    <select id="filterKelas" class="form-control form-control-sm" onchange="loadStudents()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label text-muted small fw-bold">Cari Siswa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i data-feather="search"></i>
                        </span>
                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari nama atau username siswa..." oninput="renderStudents()">
                    </div>
                </div>
            </div>

            <!-- Student Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Username / NISN</th>
                                    <th>Kelas</th>
                                    <th class="text-end pe-3" style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="studentList">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa.</td>
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

<!-- Modal Manage Grades -->
<div class="modal fade" id="modal_grades" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="modalStudentName">Nilai Rapor Siswa</h5>
                    <span class="text-muted small" id="modalStudentInfo">NISN: - | Kelas: -</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Filters for Semester & School Year -->
                <div class="row g-2 mb-3 bg-light p-3 rounded">
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold">Semester</label>
                        <select id="modalSemester" class="form-control form-control-sm" onchange="loadGrades()">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold">Tahun Ajaran</label>
                        <select id="modalTahunAjaran" class="form-control form-control-sm" onchange="loadGrades()">
                            <option value="2025/2026">2025/2026</option>
                            <option value="2026/2027">2026/2027</option>
                            <option value="2027/2028">2027/2028</option>
                        </select>
                    </div>
                </div>

                <!-- Add Grade Form (Inline Collapse) -->
                <div class="mb-3">
                    <button class="btn btn-outline-primary btn-sm mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#addGradeCollapse">
                        <i data-feather="plus"></i> Input Nilai Baru
                    </button>
                    
                    <div class="collapse" id="addGradeCollapse">
                        <form id="gradeForm" class="card card-body border p-3">
                            <input type="hidden" name="id" id="gradeRecordId">
                            <input type="hidden" name="peserta_id" id="gradePesertaId">
                            <input type="hidden" name="semester" id="gradeSemester">
                            <input type="hidden" name="tahun_ajaran" id="gradeTahunAjaran">
                            
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small">Mata Pelajaran</label>
                                    <select name="mata_pelajaran_id" class="form-control form-control-sm" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        <?php foreach ($mapels as $mapel): ?>
                                            <option value="<?= $mapel['id'] ?>"><?= esc($mapel['nama']) ?> (<?= esc($mapel['kode']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label small">Nilai Angka (0-100)</label>
                                    <input type="number" name="nilai" class="form-control form-control-sm" required min="0" max="100" placeholder="Contoh: 80">
                                </div>
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <button type="button" onclick="saveGrade()" class="btn btn-primary btn-sm w-100">Simpan</button>
                                </div>
                            </div>
                            <div id="gradeFormErrors" class="mt-2 text-danger small"></div>
                        </form>
                    </div>
                </div>

                <!-- Grades Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th style="width: 120px;">Nilai Angka</th>
                                <th style="width: 120px;">Grade Huruf</th>
                                <th style="width: 100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Memuat nilai...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let studentData = [];
    let currentPage = 1;
    const perPage = 15;
    let selectedStudent = null;

    function loadStudents() {
        const kelasId = $('#filterKelas').val();
        if (!kelasId) {
            $('#studentList').html('<tr><td colspan="5" class="text-center py-4 text-muted">Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa.</td></tr>');
            $('#pagination').html('');
            return;
        }

        $.get('<?= base_url('panel/akademik/absensi/students') ?>/' + kelasId, function(res) {
            if (res.status) {
                studentData = res.data;
                renderStudents();
            } else {
                $('#studentList').html(`<tr><td colspan="5" class="text-center py-4 text-danger">${res.message}</td></tr>`);
            }
        });
    }

    function renderStudents() {
        const query = $('#searchBox').val().toLowerCase();
        let filtered = studentData;

        if (query) {
            filtered = filtered.filter(s => s.nama.toLowerCase().includes(query) || s.username.toLowerCase().includes(query));
        }

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        if (pageData.length === 0) {
            $('#studentList').html('<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada siswa ditemukan.</td></tr>');
            $('#pagination').html('');
            return;
        }

        const kelasNama = $('#filterKelas option:selected').text();

        let html = '';
        pageData.forEach((s, idx) => {
            html += `
                <tr>
                    <td class="ps-3">${start + idx + 1}</td>
                    <td class="fw-bold text-dark">${s.nama}</td>
                    <td>${s.username}</td>
                    <td><span class="badge bg-light-primary text-primary">${kelasNama}</span></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-primary p-1 px-3" onclick="openGradesModal('${s.id}', '${s.nama}', '${s.username}', '${kelasNama}')">
                            <i data-feather="award"></i> Kelola Nilai
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#studentList').html(html);
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
        renderStudents();
    }

    function openGradesModal(id, name, username, classNama) {
        selectedStudent = { id, name, username, classNama };
        $('#modalStudentName').text('Nilai Rapor: ' + name);
        $('#modalStudentInfo').text(`Username/NISN: ${username} | Kelas: ${classNama}`);
        
        // Setup hidden form inputs
        $('#gradePesertaId').val(id);
        
        // Hide add grade collapse and reset form
        const addCollapse = bootstrap.Collapse.getInstance(document.getElementById('addGradeCollapse'));
        if (addCollapse) addCollapse.hide();
        resetGradeForm();

        // Show modal
        $('#modal_grades').modal('show');

        // Load grades
        loadGrades();
    }

    function loadGrades() {
        if (!selectedStudent) return;
        
        const semester = $('#modalSemester').val();
        const tahunAjaran = $('#modalTahunAjaran').val();

        // Update hidden inputs for saving
        $('#gradeSemester').val(semester);
        $('#gradeTahunAjaran').val(tahunAjaran);

        $('#gradesTableBody').html('<tr><td colspan="4" class="text-center py-3 text-muted">Memuat nilai...</td></tr>');

        $.get('<?= base_url('panel/akademik/rapor/list') ?>', {
            peserta_id: selectedStudent.id,
            semester: semester,
            tahun_ajaran: tahunAjaran
        }, function(res) {
            if (res.status) {
                renderGradesTable(res.data);
            } else {
                $('#gradesTableBody').html(`<tr><td colspan="4" class="text-center py-3 text-danger">${res.message}</td></tr>`);
            }
        });
    }

    function renderGradesTable(data) {
        if (data.length === 0) {
            $('#gradesTableBody').html('<tr><td colspan="4" class="text-center py-3 text-muted">Belum ada nilai yang diinput untuk semester & tahun ajaran ini.</td></tr>');
            return;
        }

        let html = '';
        data.forEach(g => {
            let badgeClass = 'bg-light-success text-success';
            if (g.grade === 'C') badgeClass = 'bg-light-warning text-warning';
            if (g.grade === 'D' || g.grade === 'E') badgeClass = 'bg-light-danger text-danger';

            html += `
                <tr>
                    <td class="fw-bold text-dark">${g.mata_pelajaran_nama || '-'}</td>
                    <td>${g.nilai}</td>
                    <td><span class="badge ${badgeClass} fw-bold px-3 py-1 rounded-pill">${g.grade}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info p-1 me-1" onclick="editGrade('${g.id}', '${g.mata_pelajaran_id}', '${g.nilai}')" title="Edit">
                            <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger p-1" onclick="deleteGrade('${g.id}')" title="Hapus">
                            <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#gradesTableBody').html(html);
        feather.replace();
    }

    function resetGradeForm() {
        $('#gradeRecordId').val('');
        $('#gradeFormErrors').html('');
        $('#gradeForm')[0].reset();
        $('#gradeForm button').text('Simpan');
    }

    function saveGrade() {
        const form = $('#gradeForm');
        
        // Ensure values are set
        $('#gradeSemester').val($('#modalSemester').val());
        $('#gradeTahunAjaran').val($('#modalTahunAjaran').val());

        if (form[0].checkValidity() === false) {
            form.addClass('was-validated');
            return;
        }

        $.post('<?= base_url('panel/akademik/rapor/save') ?>', form.serialize(), function(res) {
            if (res.status) {
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                resetGradeForm();
                
                // Hide collapse
                const collapseElement = document.getElementById('addGradeCollapse');
                const bsCollapse = bootstrap.Collapse.getInstance(collapseElement);
                if (bsCollapse) bsCollapse.hide();

                loadGrades();
            } else {
                let errors = '<ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul>';
                $('#gradeFormErrors').html(errors);
            }
        }, 'json');
    }

    function editGrade(id, subjectId, score) {
        $('#gradeRecordId').val(id);
        $('[name="mata_pelajaran_id"]').val(subjectId);
        $('[name="nilai"]').val(score);
        $('#gradeForm button').text('Perbarui');
        
        // Show form collapse
        const collapseElement = document.getElementById('addGradeCollapse');
        const bsCollapse = new bootstrap.Collapse(collapseElement, { toggle: false });
        bsCollapse.show();
    }

    function deleteGrade(id) {
        Swal.fire({
            title: 'Hapus nilai ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('<?= base_url('panel/akademik/rapor/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        loadGrades();
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
        feather.replace();
    });
</script>
<?= $this->endSection(); ?>
