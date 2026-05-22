<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>
<style>
    .userpass-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        /* kecil & rapih */
        cursor: pointer;
    }

    .userpass-password {
        font-family: monospace;
    }

    .userpass-eye {
        background: none;
        border: none;
        cursor: pointer;
        padding: 2px;
        color: #666;
    }

    .userpass-eye:hover {
        color: #000;
    }

    .userpass-copied {
        position: absolute;
        transform: translateX(-50%) translateY(-100%);
        background: #333;
        color: #fff;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        z-index: 9999;
    }

    .userpass-copied.show {
        opacity: 1;
    }
</style>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-2 mb-sm-0">Manajemen Peserta</h5>

                    <div class="d-flex flex-wrap gap-2 mb-2 mb-sm-0">
                        <a href="<?= base_url('panel/peserta/download') ?>" type="button" class="btn btn-outline-success flex-fill flex-sm-grow-0">
                            <i data-feather="download"></i> Download
                        </a>
                        <button type="button" class="btn btn-outline-success flex-fill flex-sm-grow-0"
                            data-bs-toggle="modal" data-bs-target="#modal_import">
                            <i data-feather="upload-cloud"></i> Import
                        </button>
                        <button type="button" class="btn btn-outline-warning flex-fill flex-sm-grow-0"
                            data-bs-toggle="modal" data-bs-target="#modal_update_password">
                            <i data-feather="lock"></i> Ubah Password
                        </button>

                        <button type="button" onclick="add()" class="btn btn-primary flex-fill flex-sm-grow-0">
                            <i data-feather="plus-circle"></i> Tambah
                        </button>
                        <button type="button" id="refreshtable" class="btn btn-outline-secondary flex-fill flex-sm-grow-0">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>
                </div>

                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table" id="pesertaTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Tingkat</th>
                                    <th>Kelas</th>
                                    <th>Jurusan</th>
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
                <h5 class="modal-title">Form Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="form-group mb-3">
                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label for="tingkat_id">Tingkat</label>
                    <select name="tingkat_id" class="form-control form-control-sm" required>
                        <option value="">- Pilih Tingkat -</option>
                        <?php foreach ($tingkat as $row): ?>
                            <option value="<?= $row['id'] ?>"><?= esc($row['nama']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="kelas_id">Kelas</label>
                    <select name="kelas_id" class="form-control form-control-sm" required>
                        <option value="">- Pilih Kelas -</option>
                        <?php foreach ($kelas as $row): ?>
                            <option value="<?= $row['id'] ?>"><?= esc($row['nama']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="jurusan_id">Jurusan</label>
                    <select name="jurusan_id" class="form-control form-control-sm" required>
                        <option value="">- Pilih Jurusan -</option>
                        <?php foreach ($jurusan as $row): ?>
                            <option value="<?= $row['id'] ?>"><?= esc($row['nama']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="agama_id">Agama</label>
                    <select name="agama_id" class="form-control form-control-sm" required>
                        <option value="">- Pilih Agama -</option>
                        <?php foreach ($agama as $row): ?>
                            <option value="<?= $row['id'] ?>"><?= esc($row['nama']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>


                <div class="form-group mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control form-control-sm">
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
<!-- Modal Import -->
<div class="modal fade" id="modal_import" tabindex="-1">
    <div class="modal-dialog">
        <form id="formImport" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Peserta dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    File harus berformat <strong>.xlsx</strong> dan kolom sesuai template.
                    <br>Urutan kolom: <code>No, Nama, NISN, Username, Password, Tingkat, Kelas, Jurusan, Agama, Status</code>
                </div>
                <a href="<?= base_url('assets/template/template_import_peserta.xlsx') ?>" class="btn btn-sm btn-outline-primary mb-2">
                    <i data-feather="download-cloud"></i> Download Template
                </a>

                <div class="mb-3">
                    <label>Pilih File Excel</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                </div>
                <div id="importResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Import</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Ubah Password -->
<div class="modal fade" id="modal_update_password" tabindex="-1">
    <div class="modal-dialog">
        <form id="formUpdatePassword" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Password Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small">
                    File harus berformat <strong>.xlsx</strong> dengan kolom:<br>
                    <code>No | NISN | Nama | Kelas | Password Baru</code><br>
                    Password akan diupdate berdasarkan <strong>NISN</strong>.
                </div>

                <!-- tombol download template dari controller -->
                <a href="<?= base_url('panel/peserta/downloadTemplateUpdatePassword') ?>"
                    class="btn btn-sm btn-outline-primary mb-3" target="_blank">
                    <i data-feather="download-cloud"></i> Download Template
                </a>

                <div class="mb-3">
                    <label class="form-label">Pilih File Excel</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                </div>

                <!-- hasil update (berhasil/gagal) -->
                <div id="updatePasswordResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">
                    <i data-feather="upload"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let save_method;

    $(document).ready(function() {
        $('#pesertaTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/peserta/getAll') ?>',
                type: 'GET',
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'nisn'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'username'
                },
                {
                    data: null,
                    title: 'Password',
                    render: function(data, type, row, meta) {
                        const esc = s => $('<div>').text(s ?? '').html();
                        const password = row.password ?? '';
                        const masked = password.length ? '•'.repeat(Math.min(password.length, 12)) : '';

                        return `
      <div class="userpass-cell" data-password="${esc(password)}">
        <span class="userpass-password userpass-password-masked">${esc(masked)}</span>
        <span class="userpass-password userpass-password-plain" style="display:none">${esc(password)}</span>

        <button type="button" class="userpass-eye" aria-label="Toggle password" title="Lihat password">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        </button>
      </div>
    `;
                    }
                },

                {
                    data: 'tingkat'
                },
                {
                    data: 'kelas'
                },
                {
                    data: 'jurusan'
                },
                {
                    data: 'is_active',
                    render: d => d == 1 ?
                        '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'
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
            $('#pesertaTable').DataTable().ajax.reload(null, false); // false = tetap di halaman aktif
        });
        $('#pesertaTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            $.get('<?= base_url('panel/peserta/getAll') ?>', function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    $('[name="id"]').val(item.id);
                    $('[name="nisn"]').val(item.nisn);
                    $('[name="nama"]').val(item.nama);
                    $('[name="tingkat_id"]').val(item.tingkat_id);
                    $('[name="kelas_id"]').val(item.kelas_id);
                    $('[name="jurusan_id"]').val(item.jurusan_id);
                    $('[name="agama_id"]').val(item.agama_id);
                    $('[name="username"]').val(item.username);
                    $('[name="is_active"]').val(item.is_active);
                    $('[name="password"]').val('');
                    $('.modal-title').text('Edit Peserta');
                    $('#modal_form').modal('show');
                    save_method = 'edit';
                }
            });
        });

        $('#pesertaTable').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus peserta ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/peserta/delete/') ?>${id}`, function(res) {
                        $('#pesertaTable').DataTable().ajax.reload();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    });
                }
            });
        });
    });
    // toggle show/hide password
    $('#pesertaTable').on('click', '.userpass-eye', function(e) {
        e.stopPropagation(); // jangan ikut trigger copy
        const $cell = $(this).closest('.userpass-cell');
        $cell.find('.userpass-password-masked').toggle();
        $cell.find('.userpass-password-plain').toggle();
    });

    // copy password saat klik teks password
    // copy password saat klik teks password
    $('#pesertaTable').on('click', '.userpass-password', function() {
        const $cell = $(this).closest('.userpass-cell');
        const text = $cell.data('password');

        navigator.clipboard.writeText(text).then(() => {
            showCopiedToast($cell, 'Copied!');
        });
    });

    // fungsi untuk bikin toast kecil
    function showCopiedToast($anchor, msg) {
        // hapus toast lama biar gak numpuk
        $('.userpass-copied').remove();

        const rect = $anchor[0].getBoundingClientRect();
        const toast = $('<div class="userpass-copied">').text(msg).appendTo('body');

        // posisi tepat di atas cell
        toast.css({
            left: rect.left + rect.width / 2 + 'px',
            top: rect.top - 8 + window.scrollY + 'px'
        });

        requestAnimationFrame(() => toast.addClass('show'));

        // auto hilang
        setTimeout(() => {
            toast.removeClass('show');
            setTimeout(() => toast.remove(), 300);
        }, 1200);
    }



    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.modal-title').text('Tambah Peserta');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/peserta/create') ?>' :
            '<?= base_url('panel/peserta/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                $('#pesertaTable').DataTable().ajax.reload();
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


    $('#formImport').on('submit', function(e) {
        e.preventDefault();
        if (!$('[name="file"]')[0].files.length) {
            $('#importResult').html('<div class="alert alert-danger">Silakan pilih file terlebih dahulu.</div>');
            return;
        }
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('panel/peserta/import') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: res => {
                let html = `<div class="alert alert-success">${res.message}</div>`;
                if (res.gagal && res.gagal.length) {
                    html += `
                    <div class="alert alert-warning mt-2">
                        <strong>Beberapa baris gagal diimpor:</strong>
                        <table class="table table-sm table-bordered mt-2">
                            <thead><tr><th>Baris</th><th>Alasan</th></tr></thead>
                            <tbody>
                            ${res.gagal.map(g => `<tr><td>${g.baris}</td><td>${g.alasan}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                }

                $('#importResult').html(html);
                $('#pesertaTable').DataTable().ajax.reload();

                if (res.status) {
                    setTimeout(() => {
                        $('#modal_import').modal('hide');
                        $('#formImport')[0].reset();
                        $('#importResult').html('');
                    }, 3000);
                }
            },
            error: err => {
                $('#importResult').html('<div class="alert alert-danger">Gagal mengimpor file.</div>');
            }
        });
    });

    $('#formUpdatePassword').on('submit', function(e) {
        e.preventDefault();

        const fileInput = $('[name="file"]', this)[0];
        if (!fileInput.files.length) {
            $('#updatePasswordResult').html(
                '<div class="alert alert-danger">Silakan pilih file terlebih dahulu.</div>'
            );
            return;
        }

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('panel/peserta/updatePasswordByExcel') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // penting supaya response otomatis di-parse
            beforeSend: () => {
                $('#updatePasswordResult').html(
                    '<div class="alert alert-info">Sedang memproses, harap tunggu...</div>'
                );
            },
            success: res => {
                let html = '';

                if (res.status) {
                    html += `<div class="alert alert-success">${res.message}</div>`;
                } else {
                    html += `<div class="alert alert-danger">${res.message || 'Gagal mengupdate password.'}</div>`;
                }

                if (res?.gagal?.length) {
                    html += `
                <div class="alert alert-warning mt-2">
                    <strong>Beberapa baris gagal diupdate:</strong>
                    <table class="table table-sm table-bordered mt-2">
                        <thead><tr><th>Baris</th><th>Alasan</th></tr></thead>
                        <tbody>
                        ${res.gagal.map(g => `
                            <tr>
                                <td>${g.baris}</td>
                                <td>${g.alasan}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;
                }

                $('#updatePasswordResult').html(html);
                $('#pesertaTable').DataTable().ajax.reload();

                // if (res.status) {
                //     setTimeout(() => {
                //         $('#modal_update_password').modal('hide');
                //         $('#formUpdatePassword')[0].reset();
                //         $('#updatePasswordResult').html('');
                //     }, 2500);
                // }
            },
            error: (xhr, status, error) => {
                console.error('Update password error:', error);
                $('#updatePasswordResult').html(
                    '<div class="alert alert-danger">Terjadi kesalahan server. Coba lagi nanti.</div>'
                );
            }
        });
    });
</script>
<?= $this->endSection(); ?>