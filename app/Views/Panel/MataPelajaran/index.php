<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0">Manajemen Mata Pelajaran</h5>
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
                <input type="text" id="searchBox" class="form-control" placeholder="Cari mata pelajaran...">
            </div>

            <!-- List -->
            <div id="mapelList" class="row g-3"></div>

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
                <h5 class="modal-title">Form Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="mb-3">
                    <label>Kode Mapel</label>
                    <input type="text" name="kode" class="form-control form-control-sm" placeholder="Contoh: MTK, FIS, BING" required>
                </div>
                <div class="mb-3">
                    <label>Nama Mata Pelajaran</label>
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Matematika" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="is_active" class="form-control form-control-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
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
    let mapelData = [];
    let currentPage = 1;
    const perPage = 8;
    let save_method;

    function renderMapelCard(item) {
        return `
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card h-100 shadow-sm position-relative p-2">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <span class="badge bg-light-primary text-primary mb-2">${item.kode}</span>
                    <h6 class="card-title mb-2 text-truncate text-dark fw-bold">${item.nama}</h6>
                </div>
                <span class="badge align-self-start ${item.is_active == 1 ? 'bg-success' : 'bg-danger'} rounded-pill px-2 py-1 small">
                    ${item.is_active == 1 ? 'Aktif' : 'Nonaktif'}
                </span>
            </div>
            <!-- Action buttons -->
            <div class="position-absolute top-0 end-0 d-flex gap-1 m-2">
                <button class="btn btn-sm btn-light p-1 edit-btn" data-id="${item.id}" title="Edit" style="font-size:0.75rem;">
                    Edit
                </button>
                <button class="btn btn-sm btn-light p-1 delete-btn" data-id="${item.id}" title="Hapus" style="font-size:0.75rem;">
                    Hapus
                </button>
            </div>
        </div>
    </div>`;
    }

    // Load data
    function loadMapel() {
        $.get('<?= base_url('panel/mata-pelajaran/list') ?>', function(res) {
            if (res.status) {
                mapelData = res.data;
                renderMapelList();
            }
        });
    }

    // Render list + pagination
    function renderMapelList() {
        const query = $('#searchBox').val().toLowerCase();
        const filtered = mapelData.filter(m => 
            m.nama.toLowerCase().includes(query) || 
            m.kode.toLowerCase().includes(query)
        );

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        $('#mapelList').html(filtered.slice(start, end).map(renderMapelCard).join(''));
        
        renderPagination(totalPages);
        feather.replace();
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
        renderMapelList();
    }

    // Search
    $('#searchBox').on('input', function() {
        currentPage = 1;
        renderMapelList();
    });

    // Refresh
    $('#refreshList').on('click', loadMapel);

    // Add
    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('#errorMessages').html('');
        $('.modal-title').text('Tambah Mata Pelajaran');
        $('#modal_form').modal('show');
    }

    // Save add/edit
    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/mata-pelajaran/create') ?>' :
            '<?= base_url('panel/mata-pelajaran/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadMapel();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
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

    // Submit form
    $('#form').on('submit', function(e) {
        e.preventDefault();
        save();
    });

    // Edit
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const item = mapelData.find(m => m.id == id);
        if (item) {
            $('#errorMessages').html('');
            $('[name="id"]').val(item.id);
            $('[name="kode"]').val(item.kode);
            $('[name="nama"]').val(item.nama);
            $('[name="is_active"]').val(item.is_active);
            $('.modal-title').text('Edit Mata Pelajaran');
            $('#modal_form').modal('show');
            save_method = 'edit';
        }
    });

    // Delete
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus mata pelajaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/mata-pelajaran/delete/') ?>${id}`, function(res) {
                    if (res.status) {
                        mapelData = mapelData.filter(m => m.id != id);
                        renderMapelList();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // Initial load
    $(document).ready(loadMapel);
</script>
<?= $this->endSection(); ?>
