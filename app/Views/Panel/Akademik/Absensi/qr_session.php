<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Back Link & Header -->
            <div class="d-flex align-items-center mb-3 gap-2">
                <a href="<?= base_url('panel/akademik/absensi') ?>" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
                <h5 class="mb-0 fw-bold">Sesi Absensi QR Live - <?= esc($kelas['nama']) ?></h5>
            </div>

            <div class="row g-4">
                <!-- QR Code Display -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0 text-center p-4">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-1">Pindai Kehadiran</h5>
                            <p class="text-muted small mb-4">Minta siswa membuka menu Absensi QR di aplikasi SmartSchool lalu arahkan kamera ke kode QR di bawah ini.</p>
                            
                            <!-- QR Code Container -->
                            <div class="d-inline-block bg-white p-3 rounded shadow-sm border mb-4">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=<?= urlencode($encryptedPayload) ?>" alt="QR Code Absensi" class="img-fluid" style="min-width: 250px; max-width: 350px;">
                            </div>

                            <div class="alert alert-light-primary border-0 small text-primary mb-0">
                                <i data-feather="info" class="me-1"></i> QR Code valid untuk hari ini: <strong><?= date('d M Y', strtotime($today)) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Presence List -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark"><i data-feather="users" class="text-primary me-1"></i> Siswa Hadir Hari Ini</h6>
                            <span class="badge bg-success rounded-pill px-3 py-1 fw-bold fs-6" id="studentCount">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="ps-3" style="width: 60px;">No</th>
                                            <th>Nama Siswa</th>
                                            <th>Waktu Scan</th>
                                            <th class="text-end pe-3" style="width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="liveList">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Menunggu scan dari siswa...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    const kelasId = '<?= $kelas['id'] ?>';
    let loadedIds = new Set();

    function pollAttendance() {
        $.get('<?= base_url('panel/akademik/absensi/polling') ?>/' + kelasId, function(res) {
            if (res.status) {
                renderLiveList(res.data);
            }
        });
    }

    function renderLiveList(data) {
        if (data.length === 0) {
            $('#liveList').html('<tr><td colspan="4" class="text-center py-4 text-muted">Menunggu scan dari siswa...</td></tr>');
            $('#studentCount').text('0');
            return;
        }

        // Update count
        $('#studentCount').text(data.length);

        let html = '';
        data.forEach((a, idx) => {
            let badgeClass = 'bg-light-success text-success';
            if (a.status === 'sakit') badgeClass = 'bg-light-info text-info';
            if (a.status === 'izin') badgeClass = 'bg-light-warning text-warning';
            if (a.status === 'alfa') badgeClass = 'bg-light-danger text-danger';

            // Check if we should play a notification sound or flash
            if (!loadedIds.has(a.id)) {
                loadedIds.add(a.id);
                // Simple visual alert or notification if user was just added
                if (loadedIds.size > 1) { // skip first load
                    Snackbar.show({
                        text: `${a.nama_peserta} berhasil absen!`,
                        pos: 'bottom-right',
                        actionText: 'OK',
                        actionTextColor: '#fff'
                    });
                }
            }

            html += `
                <tr>
                    <td class="ps-3">${idx + 1}</td>
                    <td class="fw-bold text-dark">${a.nama_peserta} <br><small class="text-muted">${a.username_peserta}</small></td>
                    <td><i data-feather="clock" class="text-muted p-1"></i> ${a.waktu_scan}</td>
                    <td class="text-end pe-3"><span class="badge ${badgeClass} text-uppercase font-weight-bold px-2 py-1 rounded">${a.status}</span></td>
                </tr>
            `;
        });

        $('#liveList').html(html);
        feather.replace();
    }

    $(document).ready(function() {
        feather.replace();
        
        // Initial call
        pollAttendance();

        // Start polling every 3 seconds
        const pollInterval = setInterval(pollAttendance, 3000);

        // Clear interval on navigate away
        $(window).on('unload', function() {
            clearInterval(pollInterval);
        });
    });
</script>
<?= $this->endSection(); ?>
