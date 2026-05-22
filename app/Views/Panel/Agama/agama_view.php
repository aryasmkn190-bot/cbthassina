<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0">Manajemen Agama</h5>
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
                <input type="text" id="searchBox" class="form-control" placeholder="Cari agama...">
            </div>

            <!-- List -->
            <div id="agamaList" class="row g-3"></div>

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
                <h5 class="modal-title">Form Agama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="mb-3">
                    <label>Nama Agama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
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
    let agamaData = [];
    let currentPage = 1;
    const perPage = 8;
    let save_method;

    // Render single card
    function renderAgamaCard(agama) {
        return `
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card h-100 shadow-sm position-relative p-2">

            <div class="card-body d-flex flex-column">
                <h6 class="card-title mb-2 text-truncate">${agama.nama}</h6>
                <span class="badge ${agama.is_active == 1 ? 'bg-success' : 'bg-danger'} rounded-pill px-2 py-1 small">
                    ${agama.is_active == 1 ? 'Aktif' : 'Tidak Aktif'}
                </span>
            </div>

             <!-- Tombol edit/hapus di pojok kanan atas -->
            <div class="position-absolute top-0 end-0 d-flex gap-1 m-2">
                <button class="btn btn-sm edit-btn p-1" data-id="${agama.id}" title="Edit">
                    Edit
                </button>
                <button class="btn btn-sm delete-btn p-1" data-id="${agama.id}" title="Hapus">
                    Hapus
                </button>
            </div>
        </div>
    </div>`;
    }

    // Load data
    function loadAgama() {
        $.get('<?= base_url('panel/agama/list') ?>', function(res) {
            if (res.status) {
                agamaData = res.data;
                renderAgamaList();
            }
        });
    }

    // Render list + pagination
    function renderAgamaList() {
        const query = $('#searchBox').val().toLowerCase();
        const filtered = agamaData.filter(a => a.nama.toLowerCase().includes(query));

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        $('#agamaList').html(filtered.slice(start, end).map(renderAgamaCard).join(''));

        feather.replace(); // icon
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
        renderAgamaList();
    }

    // Search
    $('#searchBox').on('input', function() {
        currentPage = 1;
        renderAgamaList();
    });

    // Add
    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.modal-title').text('Tambah Agama');
        $('#modal_form').modal('show');
    }

    // Save add/edit
    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/agama/create') ?>' :
            '<?= base_url('panel/agama/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadAgama();
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

    // Edit
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const item = agamaData.find(a => a.id == id);
        if (item) {
            $('[name="id"]').val(item.id);
            $('[name="nama"]').val(item.nama);
            $('[name="is_active"]').val(item.is_active);
            $('.modal-title').text('Edit Agama');
            $('#modal_form').modal('show');
            save_method = 'edit';
        }
    });

    // Delete
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus agama ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/agama/delete/') ?>${id}`, function(res) {
                    agamaData = agamaData.filter(a => a.id != id);
                    renderAgamaList();
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                }, 'json');
            }
        });
    });

    // Submit form
    $('#form').on('submit', function(e) {
        e.preventDefault();
        save();
    });

    // Initial load
    $(document).ready(loadAgama);
</script>
<?= $this->endSection(); ?>