<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three p-3 shadow-sm rounded bg-white">

                <!-- Header -->
                <div class="widget-heading d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="mb-2 mb-sm-0">Manajemen Pengguna</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" onclick="add()"
                            class="btn btn-primary flex-fill flex-sm-grow-0">
                            <i data-feather="plus-circle"></i> Tambah
                        </button>
                        <button type="button" onclick="showImportModal()"
                            class="btn btn-success flex-fill flex-sm-grow-0">
                            <i data-feather="upload"></i> Import
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table align-middle" id="userTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Roles</th>
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
                <h5 class="modal-title">Form Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="form-group mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="full_name" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-3">
                    <label>Roles</label>
                    <select name="roles" class="form-control form-control-sm" required>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                    </select>
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
<!-- Modal Import Excel -->
<div class="modal fade" id="modal_import" tabindex="-1">
    <div class="modal-dialog">
        <form id="importForm" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Import Pengguna dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label>Pilih File Excel (.xlsx)</label>
                    <input type="file" name="file_excel" accept=".xls,.xlsx" class="form-control" required>
                </div>
                <div class="alert alert-info">
                    <small>
                        Format kolom: <b>username | email | full_name | password | roles | is_active</b><br>
                        Baris pertama dianggap sebagai header.
                    </small>
                </div>
                <a href="<?= base_url('assets/template/template_users.xlsx') ?>">Download Template</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="importExcel()" class="btn btn-success">Upload</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>
    let save_method;

    $(document).ready(function() {
        $('#userTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/users/list') ?>',
                type: 'GET',
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'username'
                },
                {
                    data: 'full_name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'roles'
                },
                {
                    data: 'is_active',
                    render: d => d == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'
                },
                {
                    data: null,
                    render: row => `
                        <a href="javascript:void(0)" class="edit-btn" data-id="${row.id}">Edit</a> |
                        <a href="javascript:void(0)" class="delete-btn" data-id="${row.id}">Hapus</a>
                    `
                }
            ],
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

        $('#userTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            $.get('<?= base_url('panel/users/list') ?>', function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    $('[name="id"]').val(item.id);
                    $('[name="username"]').val(item.username);
                    $('[name="email"]').val(item.email);
                    $('[name="full_name"]').val(item.full_name);
                    $('[name="roles"]').val(item.roles);
                    $('[name="is_active"]').val(item.is_active);
                    $('[name="password"]').val(''); // kosongkan
                    $('.modal-title').text('Edit Pengguna');
                    $('#modal_form').modal('show');
                    save_method = 'edit';
                }
            });
        });

        $('#userTable').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus pengguna ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/users/delete/') ?>${id}`, function(res) {
                        $('#userTable').DataTable().ajax.reload();
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
        $('.modal-title').text('Tambah Pengguna');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ? '<?= base_url('panel/users/create') ?>' : '<?= base_url('panel/users/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                $('#userTable').DataTable().ajax.reload();
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

    function showImportModal() {
        $('#importForm')[0].reset();
        $('#modal_import').modal('show');
    }

    function importExcel() {
        let formData = new FormData($('#importForm')[0]);

        $.ajax({
            url: '<?= base_url('panel/users/importExcel') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                let message = '';

                if (res.inserted > 0) {
                    message += `Berhasil diimport: ${res.inserted} data.\n`;
                }

                if (res.duplicates && res.duplicates.length > 0) {
                    message += `Duplikat (tidak diimport) baris: ${res.duplicates.map(d => d.row).join(', ')}\n`;
                    message += `Username/email duplikat: ${res.duplicates.map(d => `${d.username}/${d.email}`).join(', ')}\n`;
                }

                if (res.status) {
                    $('#modal_import').modal('hide');
                    $('#userTable').DataTable().ajax.reload();
                    Snackbar.show({
                        text: message || res.message,
                        pos: 'top-center',
                        backgroundColor: '#28a745' // hijau
                    });
                } else {
                    Snackbar.show({
                        text: message || res.message,
                        pos: 'top-center',
                        backgroundColor: '#e7515a' // merah
                    });
                }
            },
            error: function(err) {
                Snackbar.show({
                    text: 'Terjadi kesalahan saat mengupload file',
                    pos: 'top-center',
                    backgroundColor: '#e7515a'
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>