<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0 fw-bold">Jadwal Pelajaran</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary btn-sm">
                        <i data-feather="plus-circle"></i> Tambah Jadwal
                    </button>
                    <button type="button" id="refreshList" onclick="loadJadwal()" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <select id="filterKelas" class="form-control form-control-sm" onchange="renderJadwal()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['nama'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <select id="filterHari" class="form-control form-control-sm" onchange="renderJadwal()">
                        <option value="">-- Semua Hari --</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i data-feather="search"></i>
                        </span>
                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari Mapel atau Guru..." oninput="renderJadwal()">
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0" id="jadwalTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Kelas</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengampu</th>
                                    <th>Ruangan</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="jadwalList">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Memuat data...</td>
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

<!-- Modal Form -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapels as $mapel): ?>
                            <option value="<?= $mapel['id'] ?>"><?= esc($mapel['nama']) ?> (<?= esc($mapel['kode']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Hari --</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Guru Pengampu</label>
                    <select name="guru_id" id="guru_id_select" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Mata Pelajaran Dulu --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" class="form-control form-control-sm" required placeholder="Contoh: Kelas X-A / Lab Komputer">
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
    let jadwalData = [];
    let currentPage = 1;
    const perPage = 10;
    let save_method;

    function loadJadwal() {
        $.get('<?= base_url('panel/akademik/jadwal/list') ?>', function(res) {
            if (res.status) {
                jadwalData = res.data;
                renderJadwal();
            } else {
                $('#jadwalList').html(`<tr><td colspan="7" class="text-center py-4 text-danger">${res.message}</td></tr>`);
            }
        });
    }

    function renderJadwal() {
        const filterKelas = $('#filterKelas').val();
        const filterHari = $('#filterHari').val();
        const query = $('#searchBox').val().toLowerCase();

        let filtered = jadwalData;

        if (filterKelas) {
            filtered = filtered.filter(j => j.nama_kelas === filterKelas);
        }
        if (filterHari) {
            filtered = filtered.filter(j => j.hari === filterHari);
        }
        if (query) {
            filtered = filtered.filter(j => 
                (j.mata_pelajaran_nama || '').toLowerCase().includes(query) || 
                (j.guru_nama_joined || j.guru_nama || '').toLowerCase().includes(query) ||
                j.ruangan.toLowerCase().includes(query)
            );
        }

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        if (pageData.length === 0) {
            $('#jadwalList').html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada jadwal pelajaran ditemukan.</td></tr>');
            $('#pagination').html('');
            return;
        }

        let html = '';
        pageData.forEach(j => {
            const hariCapitalized = j.hari.charAt(0).toUpperCase() + j.hari.slice(1);
            html += `
                <tr>
                    <td class="ps-3"><span class="badge bg-light-primary text-primary fw-bold">${j.nama_kelas || '-'}</span></td>
                    <td class="text-capitalize">${j.hari}</td>
                    <td><i data-feather="clock" class="text-muted p-1"></i> ${j.waktu_mulai.substring(0,5)} - ${j.waktu_selesai.substring(0,5)}</td>
                    <td class="fw-bold">${j.mata_pelajaran_nama || '-'}</td>
                    <td>${j.guru_nama_joined ? j.guru_nama_joined : j.guru_nama}</td>
                    <td><span class="badge bg-light-secondary text-secondary">${j.ruangan}</span></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-info edit-btn p-1 me-1" data-id="${j.id}">
                            <i data-feather="edit-2"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn p-1" data-id="${j.id}">
                            <i data-feather="trash-2"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#jadwalList').html(html);
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
        renderJadwal();
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('#errorMessages').html('');
        $('#guru_id_select').html('<option value="">-- Pilih Mata Pelajaran Dulu --</option>');
        $('.modal-title').text('Tambah Jadwal Pelajaran');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/akademik/jadwal/create') ?>' :
            '<?= base_url('panel/akademik/jadwal/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadJadwal();
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
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const item = jadwalData.find(j => j.id == id);
        if (item) {
            $('#form')[0].reset();
            $('#errorMessages').html('');
            $('[name="id"]').val(item.id);
            $('[name="kelas_id"]').val(item.kelas_id);
            $('[name="mata_pelajaran_id"]').val(item.mata_pelajaran_id);
            $('[name="hari"]').val(item.hari);
            $('[name="waktu_mulai"]').val(item.waktu_mulai.substring(0,5));
            $('[name="waktu_selesai"]').val(item.waktu_selesai.substring(0,5));
            $('[name="ruangan"]').val(item.ruangan);
            
            // Load teachers dynamically and select the current teacher
            loadTeachersForSubject(item.mata_pelajaran_id, item.guru_id);
            
            $('.modal-title').text('Edit Jadwal Pelajaran');
            $('#modal_form').modal('show');
            save_method = 'edit';
        }
    });

    function loadTeachersForSubject(subjectId, selectedTeacherId = null) {
        const select = $('#guru_id_select');
        if (!subjectId) {
            select.html('<option value="">-- Pilih Mata Pelajaran Dulu --</option>');
            return;
        }
        
        select.html('<option value="">Memuat...</option>');
        $.get(`<?= base_url('panel/akademik/jadwal/teachers-by-subject') ?>/${subjectId}`, function(res) {
            if (res.status) {
                if (res.data.length === 0) {
                    select.html('<option value="">-- Tidak ada guru mengampu mapel ini --</option>');
                } else {
                    let html = '<option value="">-- Pilih Guru Pengampu --</option>';
                    res.data.forEach(t => {
                        html += `<option value="${t.id}" ${selectedTeacherId == t.id ? 'selected' : ''}>${t.full_name}</option>`;
                    });
                    select.html(html);
                }
            } else {
                select.html('<option value="">Gagal memuat data guru</option>');
            }
        });
    }

    $(document).on('change', '[name="mata_pelajaran_id"]', function() {
        const subjectId = $(this).val();
        loadTeachersForSubject(subjectId);
    });

    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus jadwal ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/akademik/jadwal/delete/') ?>${id}`, function(res) {
                    if (res.status) {
                        loadJadwal();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }
                }, 'json');
            }
        });
    });

    $(document).ready(function() {
        loadJadwal();
    });
</script>
<?= $this->endSection(); ?>
