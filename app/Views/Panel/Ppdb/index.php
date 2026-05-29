<?= $this->extend('Layout/main'); ?>

<?= $this->section('css'); ?>
<style>
    .ppdb-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .ppdb-card:hover {
        transform: translateY(-3px);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Penerimaan Peserta Didik Baru (PPDB)</h4>
                    <p class="text-muted mb-0">Kelola pendaftaran siswa baru, verifikasi data, dan status penerimaan.</p>
                </div>
                <button type="button" onclick="addPendaftar()" class="btn btn-primary rounded-3 px-3 py-2">
                    <i data-feather="user-plus" class="me-1"></i> Tambah Pendaftar
                </button>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card ppdb-card bg-light border p-3 shadow-sm text-center">
                        <small class="text-muted fw-semibold">Total Pendaftar</small>
                        <h3 class="fw-bold mt-1 text-primary mb-0"><?= $stats['total'] ?></h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card ppdb-card bg-light border p-3 shadow-sm text-center">
                        <small class="text-muted fw-semibold">Menunggu Verifikasi</small>
                        <h3 class="fw-bold mt-1 text-warning mb-0"><?= $stats['menunggu'] ?></h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card ppdb-card bg-light border p-3 shadow-sm text-center">
                        <small class="text-muted fw-semibold">Dalam Proses</small>
                        <h3 class="fw-bold mt-1 text-info mb-0"><?= $stats['proses'] ?></h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card ppdb-card bg-light border p-3 shadow-sm text-center">
                        <small class="text-muted fw-semibold">Diterima</small>
                        <h3 class="fw-bold mt-1 text-success mb-0"><?= $stats['diterima'] ?></h3>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Daftar Pendaftar PPDB</h5>
                        <input type="text" id="ppdbSearch" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari nama / NISN / no daftar...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="ppdbTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Daftar</th>
                                    <th>Nama Pendaftar</th>
                                    <th>NISN</th>
                                    <th>Email / Telepon</th>
                                    <th>Sekolah Asal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ubah Status</th>
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

<!-- Modal Form Pendaftar PPDB -->
<div class="modal fade" id="modal_pendaftar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_pendaftar" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Pendaftar PPDB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalAlerts"></div>
                <input type="hidden" name="id">

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Pendaftar" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">NISN</label>
                    <input type="text" name="nisn" class="form-control" placeholder="10 Digit Nomor Induk Siswa Nasional" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Sekolah Asal</label>
                    <input type="text" name="sekolah_asal" class="form-control" placeholder="Contoh: SMP Negeri 1 Jakarta">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Telepon / WhatsApp</label>
                        <input type="text" name="telepon" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;

    // Load PPDB List
    function loadPpdb() {
        if ($.fn.DataTable.isDataTable('#ppdbTable')) {
            $('#ppdbTable').DataTable().destroy();
        }

        datatableInstance = $('#ppdbTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/ppdb/list') ?>',
                dataSrc: 'data'
            },
            columns: [
                { 
                    data: 'nomor_daftar',
                    className: 'fw-bold text-primary'
                },
                { data: 'nama', className: 'fw-bold' },
                { data: 'nisn' },
                { 
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <div><small class="text-muted">${row.email || '-'}</small></div>
                            <div><small class="text-muted">${row.telepon || '-'}</small></div>
                        `;
                    }
                },
                { data: 'sekolah_asal' },
                { 
                    data: 'status',
                    className: 'text-center',
                    render: function(data) {
                        let badgeClass = 'bg-light text-dark border';
                        let label = 'Menunggu';
                        if (data === 'menunggu') {
                            badgeClass = 'bg-warning text-dark';
                            label = 'Menunggu';
                        } else if (data === 'proses') {
                            badgeClass = 'bg-info text-white';
                            label = 'Diproses';
                        } else if (data === 'diterima') {
                            badgeClass = 'bg-success text-white';
                            label = 'Diterima';
                        } else if (data === 'ditolak') {
                            badgeClass = 'bg-danger text-white';
                            label = 'Ditolak';
                        }
                        return `<span class="badge ${badgeClass} px-2.5 py-1 rounded-pill">${label}</span>`;
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <select class="form-select form-select-sm" onchange="changeStatus('${data}', this.value)" style="width: 110px; font-size: 12px; padding: 0.25rem 0.5rem;">
                                <option value="menunggu" ${row.status === 'menunggu' ? 'selected' : ''}>Menunggu</option>
                                <option value="proses" ${row.status === 'proses' ? 'selected' : ''}>Proses</option>
                                <option value="diterima" ${row.status === 'diterima' ? 'selected' : ''}>Terima</option>
                                <option value="ditolak" ${row.status === 'ditolak' ? 'selected' : ''}>Tolak</option>
                            </select>
                        `;
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button type="button" onclick="editPendaftar('${row.id}')" class="btn btn-warning btn-icon-only btn-sm rounded-circle p-1" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" onclick="deletePendaftar('${row.id}')" class="btn btn-danger btn-icon-only btn-sm rounded-circle p-1 ms-1" title="Hapus">
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

    // Search Box Table
    $('#ppdbSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Add Pendaftar
    function addPendaftar() {
        $('#form_pendaftar')[0].reset();
        $('[name="id"]').val('');
        $('#modalAlerts').html('');
        $('.modal-title').text('Tambah Pendaftar PPDB');
        $('#modal_pendaftar').modal('show');
    }

    // Edit Pendaftar
    function editPendaftar(id) {
        $('#form_pendaftar')[0].reset();
        $('#modalAlerts').html('');
        $('.modal-title').text('Edit Data Pendaftar');

        const row = datatableInstance.rows().data().toArray().find(a => a.id == id);
        if (row) {
            $('[name="id"]').val(row.id);
            $('[name="nama"]').val(row.nama);
            $('[name="nisn"]').val(row.nisn);
            $('[name="sekolah_asal"]').val(row.sekolah_asal);
            $('[name="telepon"]').val(row.telepon);
            $('[name="email"]').val(row.email);
            
            $('#modal_pendaftar').modal('show');
        }
    }

    // Submit form pendaftar
    $('#form_pendaftar').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        const data = $(this).serialize();
        $.post('<?= base_url('panel/ppdb/create') ?>', data, function(res) {
            if (res.status) {
                $('#modal_pendaftar').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                setTimeout(() => window.location.reload(), 1000);
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

    // Change status via dropdown
    function changeStatus(id, status) {
        $.post('<?= base_url('panel/ppdb/update-status') ?>', { id: id, status: status }, function(res) {
            if (res.status) {
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                setTimeout(() => window.location.reload(), 1000);
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    }

    // Delete Pendaftar
    function deletePendaftar(id) {
        Swal.fire({
            title: 'Hapus data pendaftar?',
            text: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/ppdb/delete') ?>/${id}`, function(res) {
                    if (res.status) {
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadPpdb();
    });
</script>
<?= $this->endSection(); ?>
