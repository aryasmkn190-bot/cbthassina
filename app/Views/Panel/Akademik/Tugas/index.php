<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0 fw-bold">Daftar Tugas & PR</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary btn-sm">
                        <i data-feather="plus-circle"></i> Buat Tugas Baru
                    </button>
                    <button type="button" id="refreshList" onclick="loadTugas()" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <select id="filterKelas" class="form-control form-control-sm" onchange="renderTugas()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['nama'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i data-feather="search"></i>
                        </span>
                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari Judul Tugas atau Mata Pelajaran..." oninput="renderTugas()">
                    </div>
                </div>
            </div>

            <!-- Tugas Card List -->
            <div id="tugasList" class="row g-3">
                <div class="col-12 text-center py-4 text-muted">Memuat data...</div>
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
    <div class="modal-dialog modal-lg">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Tugas & PR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <?php foreach ($mapels as $mapel): ?>
                                <option value="<?= $mapel['id'] ?>"><?= esc($mapel['nama']) ?> (<?= esc($mapel['kode']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="judul" class="form-control form-control-sm" required placeholder="Contoh: PR Persamaan Kuadrat">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tenggat Waktu (Deadline)</label>
                    <input type="datetime-local" name="tenggat_waktu" class="form-control form-control-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi & Instruksi Tugas</label>
                    <textarea name="deskripsi" rows="6" class="form-control form-control-sm" placeholder="Tuliskan petunjuk pengerjaan tugas di sini..."></textarea>
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
    let tugasData = [];
    let currentPage = 1;
    const perPage = 6;
    let save_method;

    function loadTugas() {
        $.get('<?= base_url('panel/akademik/tugas/list') ?>', function(res) {
            if (res.status) {
                tugasData = res.data;
                renderTugas();
            } else {
                $('#tugasList').html(`<div class="col-12 text-center py-4 text-danger">${res.message}</div>`);
            }
        });
    }

    function renderTugas() {
        const filterKelas = $('#filterKelas').val();
        const query = $('#searchBox').val().toLowerCase();

        let filtered = tugasData;

        if (filterKelas) {
            filtered = filtered.filter(t => t.nama_kelas === filterKelas);
        }
        if (query) {
            filtered = filtered.filter(t => 
                t.judul.toLowerCase().includes(query) || 
                (t.mata_pelajaran_nama || '').toLowerCase().includes(query)
            );
        }

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        if (pageData.length === 0) {
            $('#tugasList').html('<div class="col-12 text-center py-4 text-muted">Tidak ada tugas ditemukan.</div>');
            $('#pagination').html('');
            return;
        }

        let html = '';
        pageData.forEach(t => {
            const deadline = new Date(t.tenggat_waktu);
            const now = new Date();
            const isPassed = deadline < now;
            const formatDeadline = deadline.toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

            html += `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light-primary text-primary fw-bold">${t.nama_kelas || 'Umum'}</span>
                                <span class="badge ${isPassed ? 'bg-light-danger text-danger' : 'bg-light-success text-success'}">
                                    ${isPassed ? 'Selesai' : 'Aktif'}
                                </span>
                            </div>
                            <h6 class="card-title fw-bold text-dark text-truncate mb-1" title="${t.judul}">${t.judul}</h6>
                            <p class="text-primary small mb-3"><i data-feather="book-open" style="width:14px;height:14px;"></i> ${t.mata_pelajaran_nama || '-'}</p>
                            
                            <p class="card-text text-muted text-truncate-3 small flex-fill">
                                ${t.deskripsi || 'Tidak ada deskripsi.'}
                            </p>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">
                                    <i data-feather="calendar" style="width:14px;height:14px;"></i> ${formatDeadline}
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= base_url('panel/akademik/tugas/detail') ?>/${t.id}" class="btn btn-primary btn-sm flex-fill">
                                    <i data-feather="eye"></i> Detail & Jawaban
                                </a>
                                <button class="btn btn-outline-info btn-sm edit-btn" data-id="${t.id}">
                                    <i data-feather="edit-2"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm delete-btn" data-id="${t.id}">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#tugasList').html(html);
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
        renderTugas();
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('#errorMessages').html('');
        $('.modal-title').text('Buat Tugas Baru');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/akademik/tugas/create') ?>' :
            '<?= base_url('panel/akademik/tugas/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadTugas();
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
        const item = tugasData.find(t => t.id == id);
        if (item) {
            $('#form')[0].reset();
            $('#errorMessages').html('');
            $('[name="id"]').val(item.id);
            $('[name="kelas_id"]').val(item.kelas_id);
            $('[name="judul"]').val(item.judul);
            $('[name="mata_pelajaran_id"]').val(item.mata_pelajaran_id);
            $('[name="tenggat_waktu"]').val(item.tenggat_waktu.replace(' ', 'T'));
            $('[name="deskripsi"]').val(item.deskripsi);
            $('.modal-title').text('Edit Tugas');
            $('#modal_form').modal('show');
            save_method = 'edit';
        }
    });

    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus tugas ini?',
            text: 'Semua jawaban siswa pada tugas ini juga akan terhapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/akademik/tugas/delete/') ?>${id}`, function(res) {
                    if (res.status) {
                        loadTugas();
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
        loadTugas();
    });
</script>

<style>
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
<?= $this->endSection(); ?>
