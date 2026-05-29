<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .keuangan-card {
        border-radius: 20px;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    body.dark .keuangan-card {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Informasi Keuangan & SPP</h4>
                <p class="mb-0 text-muted small">Pantau status pembayaran iuran sekolah bulanan, tagihan, dan riwayat transaksi.</p>
            </div>
            <div class="text-amber">
                <i data-feather="credit-card" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Summary Card Column -->
            <div class="col-md-5 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white keuangan-card h-100">
                    <h5 class="fw-bold mb-4 text-dark"><i data-feather="dollar-sign" class="text-primary me-2"></i>Status Tagihan</h5>

                    <?php
                    $unpaid = array_filter($invoices, function($i) {
                        return $i['status_bayar'] === 'belum_bayar';
                    });
                    
                    if (!empty($unpaid)):
                        $totalUnpaid = 0;
                        foreach ($unpaid as $u) {
                            $totalUnpaid += (int)$u['nominal'];
                        }
                    ?>
                        <div class="p-4 border rounded-4 text-center mb-4" style="background-color: #fef2f2; border-color: rgba(239,68,68,0.15) !important;">
                            <h6 class="text-danger mb-1 fw-bold">Total Tagihan Belum Dibayar</h6>
                            <h3 class="fw-bold text-danger mb-0">Rp <?= number_format($totalUnpaid, 0, ',', '.') ?></h3>
                            <span class="badge bg-danger mt-2.5 rounded-pill py-1.5 px-3"><?= count($unpaid) ?> Bulan Belum Lunas</span>
                        </div>
                        
                        <div class="alert alert-info py-3 border-0 rounded-4" role="alert" style="background: rgba(59, 130, 246, 0.08); color: #2563eb;">
                            <p class="small mb-0 text-center fw-bold"><i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i> Silakan melakukan pembayaran SPP langsung ke loket Tata Usaha sekolah.</p>
                        </div>
                    <?php else: ?>
                        <div class="p-4 border rounded-4 text-center mb-4" style="background-color: #f0fdf4; border-color: rgba(34,197,94,0.15) !important;">
                            <h6 class="text-success mb-1 fw-bold">Status Pembayaran SPP</h6>
                            <h3 class="fw-bold text-success mb-0">LUNAS</h3>
                            <span class="badge bg-success mt-2.5 rounded-pill py-1.5 px-3">Semua Iuran Terbayar</span>
                        </div>
                        
                        <div class="text-center py-3 text-muted small">
                            <i data-feather="check-circle" class="text-success mb-2" style="width: 32px; height: 32px;"></i>
                            <p class="mb-0">Terima kasih telah membayar SPP tepat waktu untuk mendukung operasional sekolah.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Transaction History Column -->
            <div class="col-md-7 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white keuangan-card h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i data-feather="clock" class="text-primary me-2"></i>Riwayat Pembayaran</h5>

                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-bordered table-striped align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>SPP Bulan</th>
                                    <th>Invoice / Tgl Bayar</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($invoices)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada catatan tagihan SPP.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($invoices as $i): 
                                        $isPaid = $i['status_bayar'] === 'lunas';
                                        $dateStr = $i['tanggal_bayar'] ? date('d M Y', strtotime($i['tanggal_bayar'])) : 'Belum dibayar';
                                        $methodStr = $i['metode_bayar'] ? strtoupper($i['metode_bayar']) : 'Belum Bayar';
                                    ?>
                                        <tr>
                                            <td><strong>SPP Bulan <?= esc($i['bulan']) ?></strong></td>
                                            <td>
                                                <div class="small text-dark font-weight-bold"><?= esc($i['invoice_number']) ?></div>
                                                <div class="small text-muted" style="font-size: 0.72rem;"><?= $dateStr ?> | <?= $methodStr ?></div>
                                            </td>
                                            <td><strong>Rp <?= number_format($i['nominal'], 0, ',', '.') ?></strong></td>
                                            <td>
                                                <?php if ($isPaid): ?>
                                                    <span class="badge bg-success py-1 px-2.5 rounded-pill">LUNAS</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger py-1 px-2.5 rounded-pill">BELUM</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    // Invoicing client logic if any
</script>
<?= $this->endSection(); ?>
