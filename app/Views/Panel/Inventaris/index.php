<?= $this->extend('Layout/main'); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Inventaris & Aset Sekolah</h4>
                    <p class="text-muted mb-0">Kelola sarana dan prasarana sekolah, kuantitas aset, lokasi penempatan, dan status kondisi barang.</p>
                </div>
                <button type="button" onclick="addBarang()" class="btn btn-primary rounded-3 px-3 py-2">
                    <i data-feather="plus-circle" class="me-1"></i> Tambah Barang
                </button>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Daftar Inventaris Sarpras</h5>
                        <input type="text" id="inventarisSearch" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari barang / kode / lokasi...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="inventarisTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jumlah / Kuantitas</th>
                                    <th>Kondisi</th>
                                    <th>Lokasi Penempatan</th>
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

<!-- Modal Form Inventaris -->
<div class="modal fade" id="modal_inventaris" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_inventaris" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Inventaris Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalAlerts"></div>
                <input type="hidden" name="id">

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Kode Barang / Barcode</label>
                    <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: BRG-2026-0001" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Barang / Aset</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Proyektor Epson EB-X400" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Jumlah (Unit/Pcs)</label>
                        <input type="number" name="jumlah" class="form-control" min="0" required value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-dark fw-semibold">Kondisi Barang</label>
                        <select name="kondisi" class="form-control" required>
                            <option value="baik">Baik / Layak Pakai</option>
                            <option value="rusak">Rusak / Butuh Perbaikan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Lokasi Penempatan</label>
                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Ruang Lab Komputer 1, Ruang Guru">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3">Simpan Aset</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;

    // Load Inventaris
    function loadInventaris() {
        if ($.fn.DataTable.isDataTable('#inventarisTable')) {
            $('#inventarisTable').DataTable().destroy();
        }

        datatableInstance = $('#inventarisTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/inventaris/list') ?>',
                dataSrc: 'data'
            },
            columns: [
                { 
                    data: 'kode_barang',
                    className: 'fw-bold text-primary'
                },
                { data: 'nama_barang', className: 'fw-bold' },
                { 
                    data: 'jumlah',
                    className: 'text-center fw-semibold'
                },
                { 
                    data: 'kondisi',
                    render: function(data) {
                        if (data === 'baik') {
                            return `<span class="badge bg-success rounded-pill px-2.5 py-1">Baik</span>`;
                        } else {
                            return `<span class="badge bg-danger rounded-pill px-2.5 py-1">Rusak</span>`;
                        }
                    }
                },
                { 
                    data: 'lokasi',
                    render: function(data) {
                        return data ? `<span class="text-muted"><i data-feather="map-pin" class="text-secondary me-1" style="width: 14px; height: 14px;"></i>${data}</span>` : '<span class="text-muted italic small">-</span>';
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button type="button" onclick="editBarang('${row.id}')" class="btn btn-warning btn-icon-only btn-sm rounded-circle p-1" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" onclick="deleteBarang('${row.id}')" class="btn btn-danger btn-icon-only btn-sm rounded-circle p-1 ms-1" title="Hapus">
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
    $('#inventarisSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Add Barang
    function addBarang() {
        $('#form_inventaris')[0].reset();
        $('[name="id"]').val('');
        $('#modalAlerts').html('');
        $('.modal-title').text('Tambah Barang Inventaris');
        $('#modal_inventaris').modal('show');
    }

    // Edit Barang
    function editBarang(id) {
        $('#form_inventaris')[0].reset();
        $('#modalAlerts').html('');
        $('.modal-title').text('Edit Inventaris Barang');

        const row = datatableInstance.rows().data().toArray().find(a => a.id == id);
        if (row) {
            $('[name="id"]').val(row.id);
            $('[name="kode_barang"]').val(row.kode_barang);
            $('[name="nama_barang"]').val(row.nama_barang);
            $('[name="jumlah"]').val(row.jumlah);
            $('[name="kondisi"]').val(row.kondisi);
            $('[name="lokasi"]').val(row.lokasi);
            
            $('#modal_inventaris').modal('show');
        }
    }

    // Submit Form
    $('#form_inventaris').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        const data = $(this).serialize();
        $.post('<?= base_url('panel/inventaris/create') ?>', data, function(res) {
            if (res.status) {
                $('#modal_inventaris').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                loadInventaris();
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

    // Delete Barang
    function deleteBarang(id) {
        Swal.fire({
            title: 'Hapus barang inventaris?',
            text: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/inventaris/delete') ?>/${id}`, function(res) {
                    if (res.status) {
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                        loadInventaris();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadInventaris();
    });
</script>
<?= $this->endSection(); ?>
