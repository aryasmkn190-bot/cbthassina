<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex justify-content-between align-items-center">
                    <h5 class="">Manajemen Bank Soal</h5>
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
                        <table class="table" id="bankSoalTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Topik Soal</th>
                                    <th>Jumlah Soal</th>
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

<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Bank Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="form-group mb-3">
                    <label>Kode</label>
                    <input type="text" name="kode" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control form-control-sm"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Pembuat (Guru)</label>
                    <select name="created_by" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?></option>
                        <?php endforeach ?>
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

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>
    let save_method;

    $(document).ready(function() {
        $('#bankSoalTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/banksoal/list') ?>',
                type: 'GET',
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <div>
                                <div class="text-muted small mb-1">Kode: <span class="badge bg-secondary">${row.kode}</span></div>
                                <div class="fw-bold text-primary">${row.nama}</div>
                                <div class="text-muted small">${row.deskripsi ?? '-'}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    className: 'text-start',
                    render: function(data, type, row) {
                        let daftarTopik = Array.isArray(row.daftar_topik) ? row.daftar_topik : [];
                        let badgeTopik = daftarTopik.map(nama =>
                            `<span class="badge rounded-pill bg-info text-dark me-1">${nama}</span>`
                        ).join(' ');

                        return `
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <i class="bi bi-tags-fill text-primary me-1"></i>
                                <strong>${row.jumlah_topik}</strong> Topik
                            </div>
                            <div class="d-flex flex-wrap">${badgeTopik || '<span class="text-muted small">Belum ada topik</span>'}</div>
                            <div>
                                <a href="<?= base_url() ?>panel/banksoal/topik/${row.id}" class="btn btn-sm btn-outline-primary mt-1">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Topik
                                </a>
                            </div>
                        </div>
                    `;
                    }
                },

                {
                    data: null,
                    className: 'text-start',
                    render: function(data, type, row) {
                        const badge = (label, count, colorClass = 'bg-secondary') =>
                            count > 0 ? `<span class="badge rounded-pill ${colorClass}">${label} ${count}</span>` : '';

                        return `
                                <div class="d-flex flex-column gap-1">
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-list-ol me-1 text-primary"></i>
                                        <strong>${row.jumlah_total_soal}</strong> Soal
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        ${badge('PG', row.jumlah_pg, 'bg-primary')}
                                        ${badge('MPG', row.jumlah_mpg, 'bg-success')}
                                        ${badge('BS', row.jumlah_bs, 'bg-warning')}
                                        ${badge('Jodohkan', row.jumlah_jodohkan, 'bg-info')}
                                        ${badge('Isian', row.jumlah_isian, 'bg-secondary')}
                                        ${badge('Esai', row.jumlah_esai, 'bg-dark')}
                                    </div>
                                </div>
                            `;
                    }
                },
                {
                    data: 'is_active',
                    render: data => data == 1 ?
                        '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: row => `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= base_url() ?>panel/banksoal/soal/${row.id}" class="btn btn-sm btn-outline-primary" title="Buat Soal">
                                <i data-feather="file-plus"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-warning edit-btn" data-id="${row.id}" title="Edit">
                                <i data-feather="edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Hapus">
                                <i data-feather="trash-2"></i>
                            </button>
                        </div>
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
            drawCallback: function() {
                feather.replace();
            }
        });

        $('#refreshtable').on('click', function() {
            $('#bankSoalTable').DataTable().ajax.reload(null, false); // false = tetap di halaman aktif
        });
        $('#bankSoalTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            $.get('<?= base_url('panel/banksoal/list') ?>', function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    $('[name="id"]').val(item.id);
                    $('[name="kode"]').val(item.kode);
                    $('[name="nama"]').val(item.nama);
                    $('[name="deskripsi"]').val(item.deskripsi);
                    $('[name="is_active"]').val(item.is_active);
                    $('[name="created_by"]').val(item.created_by);
                    $('.modal-title').text('Edit Bank Soal');
                    $('#modal_form').modal('show');
                    save_method = 'edit';
                }
            });
        });

        $('#bankSoalTable').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/banksoal/delete/') ?>${id}`, function(res) {
                        $('#bankSoalTable').DataTable().ajax.reload();
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
        $('.modal-title').text('Tambah Bank Soal');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/banksoal/create') ?>' :
            '<?= base_url('panel/banksoal/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                $('#bankSoalTable').DataTable().ajax.reload();
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
</script>
<?= $this->endSection(); ?>