<?= $this->extend('Layout/main'); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Kegiatan Ekstrakurikuler (Ekskul)</h4>
                    <p class="text-muted mb-0">Kelola jadwal kegiatan ekstrakurikuler, pembina/pelatih, dan waktu pelaksanaan ekskul.</p>
                </div>
                <button type="button" onclick="addEkstra()" class="btn btn-primary rounded-3 px-3 py-2">
                    <i data-feather="plus-circle" class="me-1"></i> Tambah Ekskul
                </button>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Daftar Kegiatan Ekstrakurikuler</h5>
                        <input type="text" id="ekstraSearch" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari ekskul / pembina...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="ekstraTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Ekstrakurikuler</th>
                                    <th>Pembina / Pelatih</th>
                                    <th>Hari Kegiatan</th>
                                    <th class="text-center">Jam Mulai</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form Ekskul -->
<div class="modal fade" id="modal_ekstra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_ekstra" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalAlerts"></div>
                <input type="hidden" name="id">

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Ekstrakurikuler</label>
                    <input type="text" name="nama_ekstra" class="form-control" placeholder="Contoh: Pramuka, Futsal, Seni Tari" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Pembina / Pelatih</label>
                    <input type="text" name="pembina_nama" class="form-control" placeholder="Nama lengkap pembina/pelatih" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Hari Latihan</label>
                        <select name="jadwal_hari" class="form-control" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Waktu / Jam Latihan</label>
                        <input type="time" name="waktu" class="form-control" required value="15:00">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;

    // Load Ekstra List
    function loadEkstra() {
        if ($.fn.DataTable.isDataTable('#ekstraTable')) {
            $('#ekstraTable').DataTable().destroy();
        }

        datatableInstance = $('#ekstraTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/ekstra/list') ?>',
                dataSrc: 'data'
            },
            columns: [
                { 
                    data: 'nama_ekstra',
                    className: 'fw-bold text-primary fs-6'
                },
                { 
                    data: 'pembina_nama', 
                    className: 'fw-semibold',
                    render: function(data) {
                        return `<i data-feather="user" class="text-muted me-1" style="width: 14px; height: 14px;"></i>${data}`;
                    }
                },
                { 
                    data: 'jadwal_hari',
                    render: function(data) {
                        return `<span class="badge bg-light text-dark border px-2.5 py-1"><i data-feather="calendar" class="text-secondary me-1" style="width: 12px; height: 12px;"></i>${data}</span>`;
                    }
                },
                { 
                    data: 'waktu',
                    className: 'text-center fw-bold text-success',
                    render: function(data) {
                        if (!data) return '-';
                        // Trim seconds if there
                        return data.substring(0, 5);
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button type="button" onclick="editEkstra('${row.id}')" class="btn btn-warning btn-icon-only btn-sm rounded-circle p-1" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" onclick="deleteEkstra('${row.id}')" class="btn btn-danger btn-icon-only btn-sm rounded-circle p-1 ms-1" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        `;
                    }
                }
            ],
            dom: "t" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            oLanguage: {
                oPaginate: {
                    sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    sNext: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                sInfo: "Menampilkan halaman _PAGE_ dari _PAGES_",
                sSearchPlaceholder: "Cari...",
                sLengthMenu: "Hasil :  _MENU_",
            },
            drawCallback: function() {
                feather.replace();
            }
        });
    }

    // Search Box
    $('#ekstraSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Add Ekskul
    function addEkstra() {
        $('#form_ekstra')[0].reset();
        $('[name="id"]').val('');
        $('#modalAlerts').html('');
        $('.modal-title').text('Tambah Kegiatan Ekstrakurikuler');
        $('#modal_ekstra').modal('show');
    }

    // Edit Ekskul
    function editEkstra(id) {
        $('#form_ekstra')[0].reset();
        $('#modalAlerts').html('');
        $('.modal-title').text('Edit Kegiatan Ekstrakurikuler');

        const row = datatableInstance.rows().data().toArray().find(a => a.id == id);
        if (row) {
            $('[name="id"]').val(row.id);
            $('[name="nama_ekstra"]').val(row.nama_ekstra);
            $('[name="pembina_nama"]').val(row.pembina_nama);
            $('[name="jadwal_hari"]').val(row.jadwal_hari);
            $('[name="waktu"]').val(row.waktu.substring(0, 5));
            
            $('#modal_ekstra').modal('show');
        }
    }

    // Submit Form
    $('#form_ekstra').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        const data = $(this).serialize();
        $.post('<?= base_url('panel/ekstra/create') ?>', data, function(res) {
            if (res.status) {
                $('#modal_ekstra').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                loadEkstra();
            } else {
                let errHtml = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errHtml += `<li>${v}</li>`);
                } else {
                    errHtml += `<li>${res.message}</li>`;
                }
                errHtml += '</ul></div>';
                $('#modalAlerts').html(errHtml);
            }
        }, 'json');
    });

    // Delete Ekskul
    function deleteEkstra(id) {
        Swal.fire({
            title: 'Hapus kegiatan ekstra?',
            text: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/ekstra/delete') ?>/${id}`, function(res) {
                    if (res.status) {
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                        loadEkstra();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadEkstra();
    });
</script>
<?= $this->endSection(); ?>
