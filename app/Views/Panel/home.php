<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>
<!-- Tanggal & Waktu -->
<!-- Tanggal & Waktu Modern -->
<div class="container mt-3">
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <div class="d-flex align-items-center px-3 py-2 shadow-sm rounded bg-white border" style="gap:1.5rem;">
                <!-- Tanggal -->
                <div class="d-flex align-items-center text-muted">
                    <i data-feather="calendar" class="me-1"></i>
                    <span id="currentDate">--</span>
                </div>
                <!-- Jam -->
                <div class="d-flex align-items-center fw-bold text-primary fs-6">
                    <i data-feather="clock" class="me-1"></i>
                    <span id="currentTime">--:--:--</span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container mt-3">

    <div class="row g-4">
        <!-- Panel Kanan Gabung Grup + Statistik -->
        <div class="col-12">
            <div class="card shadow-sm border-0 d-flex flex-row p-3" style="gap:1rem; flex-wrap:wrap;">
                <!-- Info Grup -->
                <div class="flex-fill d-flex align-items-center p-3 rounded bg-light shadow-sm" style="min-width:250px;">
                    <i data-feather="message-circle" class="me-3 text-info fs-3"></i>
                    <div>
                        <div class="fw-bold">Gabung Grup Telegram/WhatsApp</div>
                        <small class="text-muted">Dapatkan info terbaru dan tips ujian</small>
                        <div class="mt-2">
                            <a href="https://t.me/+H3JNAkTkvx4wNTc1" target="_blank" class="btn btn-sm btn-info me-2">Telegram</a>
                            <!-- <a href="https://chat.whatsapp.com/EBrO9TFuclUCphgZmqjS1G?mode=ac_t" target="_blank" class="btn btn-sm btn-success">WhatsApp</a> -->
                        </div>
                    </div>
                </div>

                <!-- Statistik Ujian -->
                <div class="flex-fill d-flex justify-content-around align-items-center p-3 rounded bg-light shadow-sm" style="min-width:350px;">
                    <div class="text-center">
                        <div class="fs-3 fw-bold"><?= esc($settingujian->total_ujian ?? 0) ?></div>
                        <div class="text-muted">Total Ujian</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-3 fw-bold"><?= esc($settingujian->total_peserta ?? 0) ?></div>
                        <div class="text-muted">Total Peserta</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-3 fw-bold"><?= esc($settingujian->total_soal ?? 0) ?></div>
                        <div class="text-muted">Total Soal</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-3 fw-bold"><?= esc($settingujian->total_bank_soal ?? 0) ?></div>
                        <div class="text-muted">Bank Soal</div>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-success fs-6">Server Online</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <!-- Header -->
                <div class="card-header bg-gradient-primary d-flex align-items-center" style="border-radius:0.5rem 0.5rem 0 0;">
                    <i data-feather="monitor" class="me-2"></i>
                    <span class="fs-5 fw-bold">Informasi Aplikasi</span>
                </div>

                <!-- Body -->
                <div class="card-body p-3">
                    <div class="row g-3">
                        <!-- Nama Aplikasi -->
                        <div class="col-12 d-flex align-items-center shadow-sm p-2 rounded bg-light">
                            <i data-feather="layers" class="me-2 text-primary"></i>
                            <div>
                                <div class="text-muted small">Nama Aplikasi</div>
                                <div id="config_app_name" class="fw-bold">-</div>
                            </div>
                        </div>
                        <!-- Versi -->
                        <div class="col-12 d-flex align-items-center shadow-sm p-2 rounded bg-light">
                            <i data-feather="package" class="me-2 text-success"></i>
                            <div>
                                <div class="text-muted small">Versi</div>
                                <div id="config_version" class="fw-bold">-</div>
                            </div>
                        </div>
                        <!-- Password Keluar -->
                        <div class="col-12 d-flex align-items-center shadow-sm p-2 rounded bg-light">
                            <i data-feather="lock" class="me-2 text-danger"></i>
                            <div>
                                <div class="text-muted small">Password Keluar</div>
                                <div id="config_password_exit" class="fw-bold">-</div>
                            </div>
                        </div>
                        <!-- Bluetooth -->
                        <div class="col-12 d-flex align-items-center shadow-sm p-2 rounded bg-light">
                            <i data-feather="bluetooth" class="me-2 text-info"></i>
                            <div>
                                <div class="text-muted small">Bluetooth</div>
                                <div id="config_bluetooth" class="fw-bold">-</div>
                            </div>
                        </div>
                        <!-- Headset -->
                        <div class="col-12 d-flex align-items-center shadow-sm p-2 rounded bg-light">
                            <i data-feather="headphones" class="me-2 text-warning"></i>
                            <div>
                                <div class="text-muted small">Headset</div>
                                <div id="config_headset" class="fw-bold">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Ujian Terdekat -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">

                <div class="card-header bg-gradient-primary d-flex align-items-center" style="border-radius:0.5rem 0.5rem 0 0;">
                    <i data-feather="calendar" class="me-2"></i>
                    <span class="fs-5 fw-bold">Jadwal Ujian Terdekat</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($settingujian->jadwal_ujian)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($settingujian->jadwal_ujian as $jadwal): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center rounded mb-2 shadow-sm">
                                    <div>
                                        <div class="fw-bold"><?= esc($jadwal['nama_ujian']) ?></div>
                                        <small class="text-muted">
                                            <?= esc(date('d M Y', strtotime($jadwal['waktu_mulai']))) ?>
                                            | <?= esc(date('H:i', strtotime($jadwal['waktu_mulai']))) ?> - <?= esc(date('H:i', strtotime($jadwal['waktu_selesai']))) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">Aktif</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Belum ada jadwal ujian terdekat.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Keterangan Tambahan -->
        <div class="col-12 mt-3">
            <div class="alert alert-info shadow-sm">
                <i class="me-1" data-feather="info"></i>
                Silakan gunakan menu di sebelah kiri untuk mengelola soal, peserta, ujian, dan konfigurasi aplikasi.
            </div>
        </div>

    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('pagejs') ?>
<script>
    $(document).ready(function() {
        // Load config data
        $.getJSON("<?= site_url('panel/exambro/setting/getdata') ?>", function(data) {
            $('#config_app_name').text(data.app_name || '-');
            $('#config_version').text(data.version || '-');
            $('#config_password_exit').text(data.password_exit || '-');
            $('#config_bluetooth').text(data.bluetooth == 1 ? 'Aktif' : 'Nonaktif');
            $('#config_headset').text(data.headset == 1 ? 'Wajib' : 'Tidak Wajib');
        });

        // Update date & time
        function updateDateTime() {
            const now = new Date();
            const optionsDate = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const optionsTime = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', optionsDate);
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', optionsTime);
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        feather.replace();
    });
</script>
<?= $this->endSection() ?>