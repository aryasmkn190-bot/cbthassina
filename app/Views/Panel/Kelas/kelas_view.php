<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0">Manajemen Kelas</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary btn-sm">
                        <i data-feather="plus-circle"></i> Tambah
                    </button>
                    <button type="button" id="refreshList" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-white">
                    <i data-feather="search"></i>
                </span>
                <input type="text" id="searchBox" class="form-control" placeholder="Cari kelas...">
            </div>

            <!-- List -->
            <div id="kelasList" class="row g-3"></div>

            <!-- Pagination -->
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
            </nav>

        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="is_active" class="form-control form-control-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Wali Kelas</label>
                    <select name="wali_kelas_id" class="form-control form-control-sm">
                        <option value="">-- Tanpa Wali Kelas --</option>
                        <?php foreach ($waliKelas as $wali): ?>
                            <option value="<?= $wali['id'] ?>"><?= esc($wali['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="save()" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let kelasData = [];
    let currentPage = 1;
    const perPage = 8; // lebih banyak card per halaman
    let save_method;

    function renderKelasCard(kelas) {
        const waliKelasText = kelas.wali_kelas_nama ? kelas.wali_kelas_nama : '<span class="text-muted italic small">-</span>';
        return `
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card h-100 shadow-sm position-relative p-2">

            <div class="card-body d-flex flex-column">
                <h6 class="card-title mb-1 text-truncate">${kelas.nama}</h6>
                <p class="text-muted small mb-2">Wali Kelas: <b class="text-dark">${waliKelasText}</b></p>
                <div class="d-flex align-items-center justify-content-between mt-auto">
                    <span class="badge ${kelas.is_active == 1 ? 'bg-success' : 'bg-danger'} rounded-pill px-2 py-1 small">
                        ${kelas.is_active == 1 ? 'Aktif' : 'Nonaktif'}
                    </span>
                    <button onclick="manageStudents('${kelas.id}', '${kelas.nama}')" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.72rem;">
                        Kelola Siswa
                    </button>
                </div>
            </div>

           <!-- Tombol edit/hapus di pojok kanan atas -->
            <div class="position-absolute top-0 end-0 d-flex gap-1 m-2">
                <button class="btn  btn-sm edit-btn p-1" data-id="${kelas.id}" title="Edit">
                    Edit
                </button>
                <button class="btn  btn-sm delete-btn p-1" data-id="${kelas.id}" title="Hapus">
                    Hapus
                </button>
            </div>

        </div>
    </div>`;
    }





    // Load data
    function loadKelas() {
        $.get('<?= base_url('panel/kelas/list') ?>', function(res) {
            if (res.status) {
                kelasData = res.data;
                renderKelasList();
            }
        });
    }

    // Render list + pagination
    function renderKelasList() {
        const query = $('#searchBox').val().toLowerCase();
        const filtered = kelasData.filter(k => k.nama.toLowerCase().includes(query));

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        $('#kelasList').html(filtered.slice(start, end).map(renderKelasCard).join(''));

        renderPagination(totalPages);
    }

    // Pagination
    function renderPagination(totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i===currentPage?'active':''}">
                    <a href="#" class="page-link" onclick="goPage(${i})">${i}</a>
                 </li>`;
        }
        $('#pagination').html(html);
    }

    function goPage(page) {
        currentPage = page;
        renderKelasList();
    }

    // Search
    $('#searchBox').on('input', function() {
        currentPage = 1;
        renderKelasList();
    });

    // Add
    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.modal-title').text('Tambah Kelas');
        $('#modal_form').modal('show');
    }

    // Save add/edit
    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/kelas/create') ?>' :
            '<?= base_url('panel/kelas/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadKelas();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                errors += '</ul></div>';
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }

    // Submit form
    $('#form').on('submit', function(e) {
        e.preventDefault();
        save();
    });

    // Edit
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const item = kelasData.find(k => k.id == id);
        if (item) {
            $('[name="id"]').val(item.id);
            $('[name="nama"]').val(item.nama);
            $('[name="is_active"]').val(item.is_active);
            $('[name="wali_kelas_id"]').val(item.wali_kelas_id || '');
            $('.modal-title').text('Edit Kelas');
            $('#modal_form').modal('show');
            save_method = 'edit';
        }
    });

    // Delete
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus kelas ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/kelas/delete/') ?>${id}`, function(res) {
                    if (res.status) {
                        kelasData = kelasData.filter(k => k.id != id);
                        renderKelasList();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }
                }, 'json');
            }
        });
    });

    // Initial load
    $(document).ready(loadKelas);

    // --- STUDENT MANAGEMENT LOGIC ---
    let currentClassStudents = [];
    let eligibleStudents = [];

    window.manageStudents = function(kelasId, kelasNama) {
        $('#active_kelas_id').val(kelasId);
        $('#studentModalTitle').text(`Kelola Siswa - ${kelasNama}`);
        $('#searchActiveStudents').val('');
        $('#searchEligibleStudents').val('');
        $('#selectAllEligible').prop('checked', false);
        
        // Switch to active students tab first
        const tabEl = document.querySelector('#active-students-tab');
        if (tabEl) {
            const tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
        
        loadClassStudents();
        loadEligibleStudents();
        $('#modal_students').modal('show');
    }

    function loadClassStudents() {
        const kelasId = $('#active_kelas_id').val();
        $('#activeStudentsList').html('<tr><td colspan="3" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
        $.get(`<?= base_url('panel/kelas/students') ?>/${kelasId}`, function(res) {
            if (res.status) {
                currentClassStudents = res.data;
                renderClassStudents();
            }
        });
    }

    function renderClassStudents() {
        const q = $('#searchActiveStudents').val().toLowerCase();
        const filtered = currentClassStudents.filter(s => s.nama.toLowerCase().includes(q) || s.nisn.includes(q));
        
        if (filtered.length === 0) {
            $('#activeStudentsList').html('<tr><td colspan="3" class="text-center text-muted small py-3">Tidak ada siswa di kelas ini.</td></tr>');
            return;
        }
        
        let html = '';
        filtered.forEach(s => {
            html += `
            <tr>
                <td class="small fw-bold text-dark">${s.nama}</td>
                <td class="small text-muted">${s.nisn}</td>
                <td>
                    <button type="button" onclick="removeStudentFromClass('${s.id}')" class="btn btn-xs btn-danger px-2 py-0" style="font-size:0.65rem;">
                        Keluarkan
                    </button>
                </td>
            </tr>`;
        });
        $('#activeStudentsList').html(html);
    }

    function loadEligibleStudents() {
        $('#eligibleStudentsList').html('<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
        $.get(`<?= base_url('panel/kelas/eligible-students') ?>`, function(res) {
            if (res.status) {
                const currentClassId = $('#active_kelas_id').val();
                eligibleStudents = res.data.filter(s => s.kelas_id !== currentClassId);
                renderEligibleStudents();
            }
        });
    }

    function renderEligibleStudents() {
        const q = $('#searchEligibleStudents').val().toLowerCase();
        const filtered = eligibleStudents.filter(s => s.nama.toLowerCase().includes(q) || s.nisn.includes(q));
        
        if (filtered.length === 0) {
            $('#eligibleStudentsList').html('<tr><td colspan="4" class="text-center text-muted small py-3">Tidak ada siswa yang cocok.</td></tr>');
            return;
        }
        
        let html = '';
        filtered.forEach(s => {
            const classText = s.kelas_nama ? `<span class="badge bg-light text-dark border small">${s.kelas_nama}</span>` : '<span class="text-muted small italic">Belum ada kelas</span>';
            html += `
            <tr>
                <td><input type="checkbox" class="student-select-cb" value="${s.id}"></td>
                <td class="small fw-bold text-dark">${s.nama}</td>
                <td class="small text-muted">${s.nisn}</td>
                <td>${classText}</td>
            </tr>`;
        });
        $('#eligibleStudentsList').html(html);
    }

    $('#searchActiveStudents').on('input', renderClassStudents);
    $('#searchEligibleStudents').on('input', renderEligibleStudents);

    $('#selectAllEligible').on('change', function() {
        $('.student-select-cb').prop('checked', this.checked);
    });

    window.removeStudentFromClass = function(pesertaId) {
        Swal.fire({
            title: 'Keluarkan siswa dari kelas?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('<?= base_url('panel/kelas/remove-student') ?>', { peserta_id: pesertaId }, function(res) {
                    if (res.status) {
                        loadClassStudents();
                        loadEligibleStudents();
                        Snackbar.show({ text: res.message, pos: 'top-center' });
                    }
                }, 'json');
            }
        });
    }

    window.submitAddStudents = function() {
        const selectedIds = [];
        $('.student-select-cb:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu siswa untuk ditambahkan.', 'info');
            return;
        }
        
        const kelasId = $('#active_kelas_id').val();
        $.post('<?= base_url('panel/kelas/add-students') ?>', { kelas_id: kelasId, peserta_ids: selectedIds }, function(res) {
            if (res.status) {
                loadClassStudents();
                loadEligibleStudents();
                $('#selectAllEligible').prop('checked', false);
                Snackbar.show({ text: res.message, pos: 'top-center' });
            }
        }, 'json');
    }
</script>

<!-- Modal Kelola Siswa -->
<div class="modal fade" id="modal_students" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalTitle">Kelola Siswa Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="active_kelas_id">
                <ul class="nav nav-tabs mb-3" id="studentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="active-students-tab" data-bs-toggle="tab" data-bs-target="#active-students" type="button" role="tab">Siswa Aktif Kelas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-students-tab" data-bs-toggle="tab" data-bs-target="#add-students" type="button" role="tab">Tambahkan Siswa</button>
                    </li>
                </ul>
                <div class="tab-content" id="studentTabContent">
                    <!-- Tab Siswa Aktif -->
                    <div class="tab-pane fade show active" id="active-students" role="tabpanel">
                        <div class="mb-2">
                            <input type="text" id="searchActiveStudents" class="form-control form-control-sm" placeholder="Cari siswa di kelas ini...">
                        </div>
                        <div class="table-responsive" style="max-height: 350px;">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="activeStudentsList"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Tab Tambahkan Siswa -->
                    <div class="tab-pane fade" id="add-students" role="tabpanel">
                        <div class="d-flex gap-2 mb-2">
                            <input type="text" id="searchEligibleStudents" class="form-control form-control-sm" placeholder="Cari siswa untuk ditambahkan...">
                            <button type="button" onclick="submitAddStudents()" class="btn btn-primary btn-sm px-3 flex-shrink-0">
                                Tambahkan Terpilih
                            </button>
                        </div>
                        <div class="table-responsive" style="max-height: 350px;">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="selectAllEligible"></th>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Kelas Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody id="eligibleStudentsList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>