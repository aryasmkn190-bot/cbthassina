<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex justify-content-between align-items-center">
                    <h5 class="">Manajemen Kelas</h5>
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="button" onclick="add()" class="btn btn-primary">
                            <i data-feather="plus-circle"></i> Tambah
                        </button>
                        <button type="button" id="refreshtable" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table" id="kelasTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kelas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="form-group mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select name="is_active" class="form-control form-control-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="save()" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>
    let save_method;

    $(document).ready(function() {
        $('#kelasTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/kelas/list') ?>',
                type: 'GET',
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'nama'
                },
                {
                    data: 'is_active',
                    render: d => d == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: row => `
                        <div class="d-flex justify-content-center gap-2">
                           
                            <button class="btn btn-sm btn-outline-warning edit-btn" data-id="${row.id}" title="Edit">
                                <i data-feather="edit-2"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Hapus">
                                <i data-feather="trash-2"></i>
                            </button>
                        </div>
                    `
                }
            ],
            drawCallback: function() {
                feather.replace();
            },
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
            },
        });
        $('#refreshtable').on('click', function() {
            $('#kelasTable').DataTable().ajax.reload(null, false); // false = tetap di halaman aktif
        });
        $('#kelasTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            $.get('<?= base_url('panel/kelas/list') ?>', function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    $('[name="id"]').val(item.id);
                    $('[name="nama"]').val(item.nama);
                    $('[name="is_active"]').val(item.is_active);
                    $('.modal-title').text('Edit Kelas');
                    $('#modal_form').modal('show');
                    save_method = 'edit';
                }
            });
        });

        $('#kelasTable').on('click', '.delete-btn', function() {
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
                        $('#kelasTable').DataTable().ajax.reload();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    });
                }
            });
        });
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.modal-title').text('Tambah Kelas');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ? '<?= base_url('panel/kelas/create') ?>' : '<?= base_url('panel/kelas/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                $('#kelasTable').DataTable().ajax.reload();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => {
                    errors += `<li>${v}</li>`;
                });
                errors += '</ul></div>';
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }
    // Tambahkan ini setelah fungsi save()
    $('#form').on('submit', function(e) {
        e.preventDefault();
        save();
    });
</script>
<?= $this->endSection(); ?>