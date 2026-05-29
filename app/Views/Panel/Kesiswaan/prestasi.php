<?= $this->extend('Layout/main'); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Prestasi Siswa</h4>
                    <p class="text-muted mb-0">Catat dan pantau pencapaian prestasi akademik dan non-akademik siswa.</p>
                </div>
                <button type="button" onclick="addPrestasi()" class="btn btn-success rounded-3 px-3 py-2">
                    <i data-feather="plus-circle" class="me-1"></i> Catat Prestasi
                </button>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Daftar Prestasi Siswa</h5>
                        <input type="text" id="prestasiSearch" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari nama siswa / prestasi...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="prestasiTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Nama Prestasi</th>
                                    <th>Tingkat</th>
                                    <th>Kategori</th>
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

<!-- Modal Form Prestasi -->
<div class="modal fade" id="modal_prestasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_prestasi" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Form Catat Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalAlerts"></div>
                <input type="hidden" name="id">
                
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Pilih Siswa</label>
                    <select name="peserta_id" id="pesertaSelect" class="form-control" required style="width: 100%;">
                        <option value="">-- Pilih Siswa --</option>
                        <?php foreach ($peserta as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['nama']) ?> (NISN: <?= esc($p['nisn']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Prestasi / Kegiatan</label>
                    <input type="text" name="nama_prestasi" class="form-control" placeholder="Contoh: Juara 1 Olimpiade Matematika" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Tingkat Prestasi</label>
                    <select name="tingkat" class="form-control" required>
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kabupaten">Kabupaten</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="Akademik">Akademik</option>
                        <option value="Non-Akademik">Non-Akademik</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Tanggal Diperoleh</label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success rounded-3">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;
    let tomSelectInstance = null;

    // Load list
    function loadPrestasi() {
        if ($.fn.DataTable.isDataTable('#prestasiTable')) {
            $('#prestasiTable').DataTable().destroy();
        }

        datatableInstance = $('#prestasiTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/kesiswaan/prestasi/list') ?>',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'tanggal',
                    render: function(data) {
                        if (!data) return '-';
                        const options = { year: 'numeric', month: 'long', day: 'numeric' };
                        return new Date(data).toLocaleDateString('id-ID', options);
                    }
                },
                { data: 'peserta_nisn' },
                { data: 'peserta_nama', className: 'fw-bold' },
                { 
                    data: 'kelas_nama',
                    render: function(data) {
                        return data ? `<span class="badge bg-light text-dark border px-2 py-1">${data}</span>` : '-';
                    }
                },
                { data: 'nama_prestasi' },
                { 
                    data: 'tingkat',
                    render: function(data) {
                        let badgeClass = 'bg-light text-dark border';
                        if (data === 'Nasional') badgeClass = 'bg-info text-white';
                        else if (data === 'Internasional') badgeClass = 'bg-primary text-white';
                        else if (data === 'Provinsi') badgeClass = 'bg-warning text-dark';
                        else if (data === 'Kabupaten') badgeClass = 'bg-secondary text-white';
                        
                        return `<span class="badge ${badgeClass} px-2 py-1">${data}</span>`;
                    }
                },
                { 
                    data: 'kategori',
                    render: function(data) {
                        let icon = data === 'Akademik' ? 'book' : 'award';
                        let color = data === 'Akademik' ? 'text-primary' : 'text-success';
                        return `<span class="fw-semibold"><i data-feather="${icon}" class="${color} me-1" style="width: 14px; height: 14px;"></i>${data}</span>`;
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button type="button" onclick="editPrestasi('${row.id}')" class="btn btn-warning btn-icon-only btn-sm rounded-circle p-1" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" onclick="deletePrestasi('${row.id}')" class="btn btn-danger btn-icon-only btn-sm rounded-circle p-1 ms-1" title="Hapus">
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

    // Search table
    $('#prestasiSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Add Prestasi
    function addPrestasi() {
        $('#form_prestasi')[0].reset();
        $('[name="id"]').val('');
        $('#modalAlerts').html('');
        $('.modal-title').text('Catat Prestasi Baru');
        
        if (tomSelectInstance) {
            tomSelectInstance.setValue('');
        }

        $('#modal_prestasi').modal('show');
    }

    // Edit Prestasi
    function editPrestasi(id) {
        $('#form_prestasi')[0].reset();
        $('#modalAlerts').html('');
        $('.modal-title').text('Edit Prestasi Siswa');

        const row = datatableInstance.rows().data().toArray().find(a => a.id == id);
        if (row) {
            $('[name="id"]').val(row.id);
            $('[name="nama_prestasi"]').val(row.nama_prestasi);
            $('[name="tingkat"]').val(row.tingkat);
            $('[name="kategori"]').val(row.kategori);
            $('[name="tanggal"]').val(row.tanggal);

            if (tomSelectInstance) {
                tomSelectInstance.setValue(row.peserta_id);
            } else {
                $('#pesertaSelect').val(row.peserta_id);
            }
            
            $('#modal_prestasi').modal('show');
        }
    }

    // Submit form
    $('#form_prestasi').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        const data = $(this).serialize();
        $.post('<?= base_url('panel/kesiswaan/prestasi/create') ?>', data, function(res) {
            if (res.status) {
                $('#modal_prestasi').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                loadPrestasi();
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

    // Delete Prestasi
    function deletePrestasi(id) {
        Swal.fire({
            title: 'Hapus data prestasi ini?',
            text: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/kesiswaan/prestasi/delete') ?>/${id}`, function(res) {
                    if (res.status) {
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                        loadPrestasi();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadPrestasi();

        // Initialize TomSelect if available on layout
        if (typeof TomSelect !== 'undefined') {
            tomSelectInstance = new TomSelect('#pesertaSelect', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?>
