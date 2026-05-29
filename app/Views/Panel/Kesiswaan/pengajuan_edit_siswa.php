<?= $this->extend('Layout/main'); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Pengajuan Perubahan Data Siswa</h4>
                    <p class="text-muted mb-0">Verifikasi dan setujui perubahan data penting yang diajukan oleh siswa.</p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Daftar Pengajuan Perubahan</h5>
                        <input type="text" id="pengajuanSearch" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari nama siswa / NISN...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="pengajuanTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Status</th>
                                    <th>Catatan Admin</th>
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

<!-- Modal Verifikasi Pengajuan -->
<div class="modal fade" id="modal_verifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form_verifikasi" class="modal-content text-dark">
            <input type="hidden" name="id" id="verify_id">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Verifikasi Pengajuan Biodata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 bg-light p-3 rounded-3">
                    <div class="row">
                        <div class="col-6"><strong>Nama Siswa:</strong> <span id="verify_nama">-</span></div>
                        <div class="col-6"><strong>Kelas:</strong> <span id="verify_kelas">-</span></div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">Perbandingan Perubahan Data:</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kolom / Field</th>
                                <th class="text-danger">Data Lama (Saat Ini)</th>
                                <th class="text-success">Data Baru (Diajukan)</th>
                            </tr>
                        </thead>
                        <tbody id="comparison_body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-3" id="catatan_container">
                    <label class="form-label fw-semibold">Catatan Verifikasi / Alasan Penolakan</label>
                    <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="2" placeholder="Tulis catatan jika diperlukan..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Tutup</button>
                <div id="action_buttons">
                    <button type="button" onclick="submitVerifikasi('ditolak')" class="btn btn-danger rounded-3 px-3">Tolak</button>
                    <button type="button" onclick="submitVerifikasi('disetujui')" class="btn btn-success rounded-3 px-3 ms-2">Setujui & Terapkan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;

    // Load Pengajuan List
    function loadPengajuan() {
        if ($.fn.DataTable.isDataTable('#pengajuanTable')) {
            $('#pengajuanTable').DataTable().destroy();
        }

        datatableInstance = $('#pengajuanTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/kesiswaan/pengajuan-edit/list') ?>',
                dataSrc: 'data'
            },
            columns: [
                { 
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return '-';
                        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                        return new Date(data).toLocaleDateString('id-ID', options);
                    }
                },
                { data: 'peserta_nama', className: 'fw-bold' },
                { data: 'peserta_nisn' },
                { 
                    data: 'kelas_nama',
                    render: function(data) {
                        return data ? `<span class="badge bg-light text-dark border px-2 py-1">${data}</span>` : '-';
                    }
                },
                { 
                    data: 'status',
                    className: 'text-center',
                    render: function(data) {
                        let badgeClass = 'bg-light text-dark border';
                        if (data === 'menunggu') badgeClass = 'bg-warning text-dark';
                        else if (data === 'disetujui') badgeClass = 'bg-success text-white';
                        else if (data === 'ditolak') badgeClass = 'bg-danger text-white';
                        
                        return `<span class="badge ${badgeClass} px-2.5 py-1 rounded-pill">${data}</span>`;
                    }
                },
                { 
                    data: 'catatan_admin',
                    render: d => d ? `<span class="text-muted small">${d}</span>` : '<span class="text-muted small">-</span>'
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        if (row.status === 'menunggu') {
                            return `
                                <button type="button" onclick="reviewPengajuan('${row.id}')" class="btn btn-primary btn-sm rounded-3 py-1 px-2.5">
                                    Tinjau
                                </button>
                            `;
                        } else {
                            return `
                                <button type="button" onclick="reviewPengajuan('${row.id}')" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-2.5">
                                    Detail
                                </button>
                            `;
                        }
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
    $('#pengajuanSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Review Pengajuan
    function reviewPengajuan(id) {
        const row = datatableInstance.rows().data().toArray().find(a => a.id == id);
        if (row) {
            $('#verify_id').val(row.id);
            $('#verify_nama').text(row.peserta_nama);
            $('#verify_kelas').text(row.kelas_nama || '-');
            $('#catatan_admin').val(row.catatan_admin || '');

            // Decode data_lama and data_baru JSON
            const dataLama = JSON.parse(row.data_lama);
            const dataBaru = JSON.parse(row.data_baru);

            let compHtml = '';
            $.each(dataBaru, function(field, newVal) {
                const oldVal = dataLama[field] !== undefined ? dataLama[field] : '-';
                
                // Format field names to be human readable
                const displayField = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                compHtml += `
                    <tr>
                        <td class="fw-semibold">${displayField}</td>
                        <td class="text-danger bg-light-danger fw-semibold">${oldVal || '<span class="italic small text-muted">(kosong)</span>'}</td>
                        <td class="text-success bg-light-success fw-bold">${newVal || '<span class="italic small text-muted">(kosong)</span>'}</td>
                    </tr>
                `;
            });
            $('#comparison_body').html(compHtml);

            if (row.status === 'menunggu') {
                $('#catatan_container').show();
                $('#action_buttons').show();
                $('#catatan_admin').prop('readonly', false);
            } else {
                $('#catatan_container').show();
                $('#catatan_admin').prop('readonly', true);
                $('#action_buttons').hide();
            }

            $('#modal_verifikasi').modal('show');
        }
    }

    // Submit Verification
    function submitVerifikasi(status) {
        const id = $('#verify_id').val();
        const catatan = $('#catatan_admin').val();

        let titleText = status === 'disetujui' ? 'Setujui Pengajuan?' : 'Tolak Pengajuan?';
        let confirmBtnColor = status === 'disetujui' ? '#10b981' : '#ef4444';

        Swal.fire({
            title: titleText,
            text: "Status pengajuan tidak dapat diubah setelah disimpan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            confirmButtonText: 'Ya, Proses',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('panel/kesiswaan/pengajuan-edit/verifikasi') ?>', {
                    id: id,
                    status: status,
                    catatan_admin: catatan
                }, function(res) {
                    if (res.status) {
                        $('#modal_verifikasi').modal('hide');
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                        loadPengajuan();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadPengajuan();
    });
</script>
<?= $this->endSection(); ?>
