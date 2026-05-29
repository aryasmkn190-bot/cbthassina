<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .scanner-view {
        width: 100%;
        height: 240px;
        background: #0b0f19;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.6);
    }
    .scanner-overlay-box {
        width: 140px;
        height: 140px;
        border: 2px dashed rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        position: relative;
    }
    .stat-circle {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 auto 12px auto;
        border: 6px solid #eff6ff;
        background: white;
    }
    body.dark .stat-circle {
        background: #1e293b;
        border-color: #0f172a;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Presensi Kehadiran QR</h4>
                <p class="mb-0 text-muted small">Lakukan presensi mandiri harian menggunakan fitur scan QR Code dari guru.</p>
            </div>
            <div class="text-teal">
                <i data-feather="check-square" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Left Side: QR Scanner Simulator -->
            <div class="col-md-6 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white h-100">
                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center"><i data-feather="aperture" class="text-primary me-2"></i>Simulasi Kamera Scanner</h5>
                    
                    <div class="scan-step-1">
                        <p class="text-muted small mb-3">Salin Kode QR Absensi Harian dari layar proyektor/monitor kelas atau Guru, kemudian tempelkan di kotak bawah untuk mensimulasikan proses scan kamera.</p>
                        
                        <div class="scanner-view mb-3">
                            <div class="laser-line" id="laserLine" style="display: none;"></div>
                            <div class="scanner-overlay-box d-flex align-items-center justify-content-center">
                                <i data-feather="qr-code" class="text-white-50" style="width: 50px; height: 50px;"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <textarea id="qrCodeInput" class="form-control text-center small rounded-3" rows="2" placeholder="Tempel kode QR terenkripsi di sini..."></textarea>
                        </div>
                        <button class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold w-100 start-scan-btn"><i data-feather="camera" class="me-1.5" style="width: 16px; height: 16px;"></i>Simulasikan Scan QR</button>
                    </div>

                    <div class="scan-step-2 d-none text-center py-4">
                        <div class="spinner-border text-primary my-4" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="fw-bold mt-2 text-dark">Mengirimkan Data Kehadiran...</h6>
                        <p class="text-muted small">Menghubungkan ke server Candy Exam.</p>
                    </div>

                    <div class="scan-step-3 d-none text-center py-4">
                        <div class="p-4 rounded-4 mb-4 text-center border" style="background-color: #f0fdf4; border-color: rgba(34,197,94,0.15) !important;">
                            <div class="rounded-circle bg-success text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                                <i data-feather="check" style="width: 30px; height: 30px;"></i>
                            </div>
                            <h5 class="fw-bold text-success mb-1">Presensi Masuk Berhasil!</h5>
                            <p class="text-muted small mb-3">Tercatat aman pada sistem SmartSchool</p>
                            
                            <div class="row g-2 justify-content-center text-start">
                                <div class="col-11 bg-white p-3 rounded-3 border small text-dark">
                                    <div>Siswa: <span class="fw-bold text-dark"><?= esc($peserta['nama'] ?? 'Siswa') ?></span></div>
                                    <div>Pukul: <span class="fw-bold text-dark realtime-scan-time">-- : --</span></div>
                                    <div>Status: <span class="badge bg-success">Hadir</span></div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary rounded-pill px-4 py-2 w-100 btn-selesai-scan">Selesai</button>
                    </div>

                </div>
            </div>

            <!-- Right Side: Attendance stats & history -->
            <div class="col-md-6 col-12">
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white h-100">
                    <h5 class="fw-bold mb-4 text-dark"><i data-feather="bar-chart-2" class="text-primary me-2"></i>Statistik & Riwayat Hadir</h5>

                    <!-- Stats Row -->
                    <div class="row text-center mb-4 g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="stat-circle text-primary">98%</div>
                                <span class="text-uppercase tracking-wider text-muted font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.5px;">Rasio Hadir</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="stat-circle text-success" id="count-hadir"><?= count($logs) ?></div>
                                <span class="text-uppercase tracking-wider text-muted font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.5px;">Hari Hadir</span>
                            </div>
                        </div>
                    </div>

                    <!-- History Timeline -->
                    <h6 class="fw-bold mb-3 text-dark">Log 10 Absensi Terakhir</h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered table-striped align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Scan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada riwayat kehadiran tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($logs, 0, 10) as $l): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($l['tanggal'])) ?></td>
                                            <td><strong><?= $l['waktu_scan'] ?> WIB</strong></td>
                                            <td><span class="badge bg-success">Hadir</span></td>
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
    $(document).ready(function() {
        $(".start-scan-btn").on("click", function() {
            const qrcode = $('#qrCodeInput').val().trim();
            if (!qrcode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Kode QR kosong! Salin kode QR absensi dari monitor/halaman Guru.',
                    customClass: { popup: 'rounded-4' }
                });
                return;
            }

            // Show laser scanner
            $("#laserLine").show();

            // Simulate scan delay
            setTimeout(() => {
                $("#laserLine").hide();
                $(".scan-step-1").addClass("d-none");
                $(".scan-step-2").removeClass("d-none");

                // Post presence to API
                $.post('<?= base_url('peserta/akademik/scan-absen') ?>', { qrcode: qrcode }, function(res) {
                    $(".scan-step-2").addClass("d-none");
                    if (res.status) {
                        playScanBeep();
                        $(".realtime-scan-time").text((res.time || new Date().toLocaleTimeString()) + ' WIB');
                        $(".scan-step-3").removeClass("d-none");
                        
                        // Increment Hari Hadir counter
                        const curHadir = parseInt($("#count-hadir").text());
                        $("#count-hadir").text(curHadir + 1);
                    } else {
                        $(".scan-step-1").removeClass("d-none");
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Presensi',
                            text: res.message,
                            customClass: { popup: 'rounded-4' }
                        });
                    }
                }, 'json').fail(function() {
                    $(".scan-step-2").addClass("d-none");
                    $(".scan-step-1").removeClass("d-none");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Format QR Code salah atau telah kadaluarsa.',
                        customClass: { popup: 'rounded-4' }
                    });
                });
            }, 1200);
        });

        $(".btn-selesai-scan").on("click", function() {
            window.location.reload();
        });
    });
</script>
<?= $this->endSection(); ?>
