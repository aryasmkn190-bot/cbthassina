<?= $this->extend('Layout/main'); ?>

<?= $this->section('css'); ?>
<style>
    .financial-card {
        border: none;
        border-radius: 16px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .financial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .bg-debit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .bg-kredit {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .bg-balance {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .chart-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
    }
    body.dark .chart-container {
        background: #1e293b;
        border-color: #334155;
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
                    <h4 class="mb-1 fw-bold">Jurnal & Transparansi Keuangan</h4>
                    <p class="text-muted mb-0">Kelola arus kas, transparansi keuangan sekolah, dan neraca saldo.</p>
                </div>
                <button type="button" onclick="addTransaction()" class="btn btn-primary rounded-3 px-3 py-2">
                    <i data-feather="plus-circle" class="me-1"></i> Catat Transaksi
                </button>
            </div>

            <!-- Financial Metrics Cards -->
            <div class="row g-3 mb-4">
                <!-- Total Debit (Pemasukan) -->
                <div class="col-12 col-md-4">
                    <div class="card financial-card bg-debit shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-6 fw-semibold opacity-75">Total Pemasukan (Debit)</span>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i data-feather="trending-up" class="text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($totalDebit, 2, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Total Kredit (Pengeluaran) -->
                <div class="col-12 col-md-4">
                    <div class="card financial-card bg-kredit shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-6 fw-semibold opacity-75">Total Pengeluaran (Kredit)</span>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i data-feather="trending-down" class="text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($totalKredit, 2, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Saldo Bersih -->
                <div class="col-12 col-md-4">
                    <div class="card financial-card bg-balance shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-6 fw-semibold opacity-75">Saldo Kas (Neraca)</span>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i data-feather="dollar-sign" class="text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($balance, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Graphs & Distribution -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="chart-container p-4 h-100">
                        <h5 class="fw-bold mb-3">Distribusi Pemasukan</h5>
                        <?php if (empty($debitCategories)): ?>
                            <div class="text-center py-5 text-muted">Belum ada data pemasukan.</div>
                        <?php else: ?>
                            <div id="debitChart"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="chart-container p-4 h-100">
                        <h5 class="fw-bold mb-3">Distribusi Pengeluaran</h5>
                        <?php if (empty($kreditCategories)): ?>
                            <div class="text-center py-5 text-muted">Belum ada data pengeluaran.</div>
                        <?php else: ?>
                            <div id="kreditChart"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Transaction Table Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-5">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Riwayat Jurnal Transaksi</h5>
                        <div class="d-flex gap-2">
                            <input type="text" id="transactionSearch" class="form-control form-control-sm py-1" style="max-width: 250px;" placeholder="Cari keterangan/kategori...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="transactionTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Kategori</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Nominal</th>
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

<!-- Modal Form Catat Transaksi -->
<div class="modal fade" id="modal_transaction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_transaction" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catat Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalAlerts"></div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="tipe" class="form-control" required>
                        <option value="debit">Debit (Pemasukan)</option>
                        <option value="kredit">Kredit (Pengeluaran)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Contoh: SPP, Inventaris, ATK, Gaji, Hibah" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal (Rupiah)</label>
                    <input type="number" step="0.01" min="0.01" name="nominal" class="form-control" placeholder="Masukkan angka nominal" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan / Memo</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi ringkas transaksi..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let datatableInstance = null;

    // Load Transactions list
    function loadTransactions() {
        if ($.fn.DataTable.isDataTable('#transactionTable')) {
            $('#transactionTable').DataTable().destroy();
        }

        datatableInstance = $('#transactionTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/keuangan/jurnal/list') ?>',
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
                { data: 'keterangan' },
                { 
                    data: 'kategori',
                    render: function(data) {
                        return `<span class="badge bg-light text-dark border px-2.5 py-1 rounded">${data}</span>`;
                    }
                },
                { 
                    data: 'tipe',
                    render: function(data) {
                        if (data === 'debit') {
                            return `<span class="badge bg-success rounded-pill px-2.5 py-1">Debit</span>`;
                        } else {
                            return `<span class="badge bg-danger rounded-pill px-2.5 py-1">Kredit</span>`;
                        }
                    }
                },
                { 
                    data: 'nominal',
                    className: 'text-end fw-semibold',
                    render: function(data) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2 });
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `
                            <button type="button" onclick="deleteTransaction('${data}')" class="btn btn-danger btn-icon-only btn-sm rounded-circle p-1 ms-1" title="Hapus">
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

    // Search transaction table
    $('#transactionSearch').on('keyup', function() {
        if (datatableInstance) {
            datatableInstance.search(this.value).draw();
        }
    });

    // Add Transaction
    function addTransaction() {
        $('#form_transaction')[0].reset();
        $('#modalAlerts').html('');
        $('#modal_transaction').modal('show');
    }

    // Submit form transaction
    $('#form_transaction').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        const data = $(this).serialize();
        $.post('<?= base_url('panel/keuangan/jurnal/create') ?>', data, function(res) {
            if (res.status) {
                $('#modal_transaction').modal('hide');
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
                // Reload page to update metrics cards and charts
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

    // Delete transaction
    function deleteTransaction(id) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/keuangan/jurnal/delete') ?>/${id}`, function(res) {
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

    // Charts Setup
    $(document).ready(function() {
        loadTransactions();

        // ApexCharts for Debit
        <?php if (!empty($debitCategories)): ?>
        const debitData = <?= json_encode(array_values($debitCategories)) ?>;
        const debitLabels = <?= json_encode(array_keys($debitCategories)) ?>;
        
        const debitOptions = {
            series: debitData,
            chart: {
                type: 'donut',
                height: 280
            },
            labels: debitLabels,
            colors: ['#10b981', '#34d399', '#059669', '#6ee7b7', '#047857'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toLocaleString('id-ID');
                    }
                }
            }
        };

        const debitChart = new ApexCharts(document.querySelector("#debitChart"), debitOptions);
        debitChart.render();
        <?php endif; ?>

        // ApexCharts for Kredit
        <?php if (!empty($kreditCategories)): ?>
        const kreditData = <?= json_encode(array_values($kreditCategories)) ?>;
        const kreditLabels = <?= json_encode(array_keys($kreditCategories)) ?>;
        
        const kreditOptions = {
            series: kreditData,
            chart: {
                type: 'donut',
                height: 280
            },
            labels: kreditLabels,
            colors: ['#ef4444', '#f87171', '#dc2626', '#fca5a5', '#b91c1c'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toLocaleString('id-ID');
                    }
                }
            }
        };

        const kreditChart = new ApexCharts(document.querySelector("#kreditChart"), kreditOptions);
        kreditChart.render();
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>
