<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header + Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-sm-0 fw-bold">Manajemen Keuangan / SPP</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="openInvoiceModal()" class="btn btn-primary btn-sm">
                        <i data-feather="file-plus"></i> Buat Tagihan SPP
                    </button>
                    <button type="button" id="refreshList" onclick="loadInvoices()" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small fw-bold">Filter Kelas</label>
                    <select id="filterKelas" class="form-control form-control-sm" onchange="loadInvoices()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small fw-bold">Filter Status</label>
                    <select id="filterStatus" class="form-control form-control-sm" onchange="renderInvoices()">
                        <option value="">-- Semua Status --</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i data-feather="search"></i>
                        </span>
                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari nomor invoice atau nama siswa..." oninput="renderInvoices()">
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Invoice</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Bulan</th>
                                    <th>Tagihan</th>
                                    <th>Status</th>
                                    <th>Metode & Tgl Bayar</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceList">
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>

        </div>
    </div>
</div>

<!-- Modal Generate Invoices -->
<div class="modal fade" id="modal_generate" tabindex="-1">
    <div class="modal-dialog">
        <form id="generateForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buat Tagihan SPP Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="generateErrorMessages"></div>
                
                <div class="mb-3">
                    <label class="form-label">Kelas Sasaran</label>
                    <select name="kelas_id" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Tagihan akan dibuatkan untuk seluruh siswa aktif di kelas ini.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Untuk Bulan</label>
                    <select name="bulan" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Bulan --</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktber">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nominal Tagihan (Rp)</label>
                    <input type="number" name="nominal" class="form-control form-control-sm" required min="1000" value="150000" placeholder="Contoh: 150000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveGenerate()" class="btn btn-primary btn-sm">Buat Tagihan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pay Invoice -->
<div class="modal fade" id="modal_pay" tabindex="-1">
    <div class="modal-dialog">
        <form id="payForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catat Pembayaran SPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="payErrorMessages"></div>
                <input type="hidden" id="payInvoiceId">

                <div class="mb-3">
                    <label class="form-label">Nomor Invoice</label>
                    <input type="text" id="payInvoiceNumber" class="form-control form-control-sm" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <input type="text" id="payStudentName" class="form-control form-control-sm" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total Nominal</label>
                    <input type="text" id="payNominal" class="form-control form-control-sm font-weight-bold" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-control form-control-sm" required>
                        <option value="tunai">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS / Digital Payment</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="savePayment()" class="btn btn-success btn-sm">Selesaikan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let invoiceData = [];
    let currentPage = 1;
    const perPage = 15;

    function loadInvoices() {
        const kelasId = $('#filterKelas').val();
        $.get('<?= base_url('panel/akademik/keuangan/list') ?>', { kelas_id: kelasId }, function(res) {
            if (res.status) {
                invoiceData = res.data;
                renderInvoices();
            } else {
                $('#invoiceList').html(`<tr><td colspan="8" class="text-center py-4 text-danger">${res.message}</td></tr>`);
            }
        });
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function renderInvoices() {
        const query = $('#searchBox').val().toLowerCase();
        const status = $('#filterStatus').val();

        let filtered = invoiceData;

        if (status) {
            filtered = filtered.filter(i => i.status_bayar === status);
        }
        if (query) {
            filtered = filtered.filter(i => 
                i.invoice_number.toLowerCase().includes(query) || 
                i.nama_peserta.toLowerCase().includes(query)
            );
        }

        const totalPages = Math.ceil(filtered.length / perPage);
        currentPage = Math.min(currentPage, totalPages) || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        if (pageData.length === 0) {
            $('#invoiceList').html('<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data tagihan ditemukan.</td></tr>');
            $('#pagination').html('');
            return;
        }

        let html = '';
        pageData.forEach((i, idx) => {
            const dateStr = i.tanggal_bayar ? new Date(i.tanggal_bayar).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
            const payInfo = i.status_bayar === 'lunas' ? `<span class="text-capitalize text-success small fw-bold">${i.metode_bayar}</span><br><small class="text-muted">${dateStr}</small>` : '-';
            
            html += `
                <tr>
                    <td class="ps-3 fw-bold text-primary font-monospace">${i.invoice_number}</td>
                    <td class="fw-bold text-dark">${i.nama_peserta} <br><small class="text-muted">${i.username_peserta}</small></td>
                    <td><span class="badge bg-light-secondary text-secondary">${i.nama_kelas || '-'}</span></td>
                    <td>${i.bulan}</td>
                    <td class="fw-bold text-dark">${formatRupiah(i.nominal)}</td>
                    <td>
                        <span class="badge ${i.status_bayar === 'lunas' ? 'bg-light-success text-success' : 'bg-light-danger text-danger'} text-uppercase rounded px-3 py-1 font-weight-bold">
                            ${i.status_bayar.replace('_', ' ')}
                        </span>
                    </td>
                    <td>${payInfo}</td>
                    <td class="text-end pe-3">
                        ${i.status_bayar === 'belum_bayar' ? `
                            <button class="btn btn-sm btn-success p-1 px-2 me-1" onclick="payInvoice('${i.id}', '${i.invoice_number}', '${i.nama_peserta}', ${i.nominal})">
                                <i data-feather="check"></i> Bayar
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline-danger p-1 px-2" onclick="deleteInvoice('${i.id}')">
                            <i data-feather="trash-2"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#invoiceList').html(html);
        feather.replace();
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = '';
        if (totalPages <= 1) {
            $('#pagination').html('');
            return;
        }
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a href="javascript:void(0)" class="page-link" onclick="goPage(${i})">${i}</a>
            </li>`;
        }
        $('#pagination').html(html);
    }

    function goPage(page) {
        currentPage = page;
        renderInvoices();
    }

    function openInvoiceModal() {
        $('#generateForm')[0].reset();
        $('#generateErrorMessages').html('');
        $('#modal_generate').modal('show');
    }

    function saveGenerate() {
        if ($('#generateForm')[0].checkValidity() === false) {
            $('#generateForm').addClass('was-validated');
            return;
        }

        $.post('<?= base_url('panel/akademik/keuangan/invoice') ?>', $('#generateForm').serialize(), function(res) {
            if (res.status) {
                $('#modal_generate').modal('hide');
                loadInvoices();
                Swal.fire('Sukses', res.message, 'success');
            } else {
                let errors = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul></div>';
                $('#generateErrorMessages').html(errors);
            }
        }, 'json');
    }

    function payInvoice(id, invNum, name, nominal) {
        $('#payForm')[0].reset();
        $('#payErrorMessages').html('');
        $('#payInvoiceId').val(id);
        $('#payInvoiceNumber').val(invNum);
        $('#payStudentName').val(name);
        $('#payNominal').val(formatRupiah(nominal));
        $('#modal_pay').modal('show');
    }

    function savePayment() {
        const id = $('#payInvoiceId').val();
        if ($('#payForm')[0].checkValidity() === false) {
            $('#payForm').addClass('was-validated');
            return;
        }

        $.post('<?= base_url('panel/akademik/keuangan/pay') ?>/' + id, $('#payForm').serialize(), function(res) {
            if (res.status) {
                $('#modal_pay').modal('hide');
                loadInvoices();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger mb-3"><ul>';
                if (typeof res.message === 'object') {
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                } else {
                    errors += `<li>${res.message}</li>`;
                }
                errors += '</ul></div>';
                $('#payErrorMessages').html(errors);
            }
        }, 'json');
    }

    function deleteInvoice(id) {
        Swal.fire({
            title: 'Hapus tagihan ini?',
            text: 'Tindakan ini tidak bisa dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('<?= base_url('panel/akademik/keuangan/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        loadInvoices();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }
                }, 'json');
            }
        });
    }

    $(document).ready(function() {
        loadInvoices();
    });
</script>
<?= $this->endSection(); ?>
