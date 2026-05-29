<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .ekstra-card {
        border-radius: 20px;
        transition: all 0.25s ease;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .ekstra-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
    }
    body.dark .ekstra-card {
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
                <h4 class="mb-1 fw-bold text-dark">Kegiatan Ekstrakurikuler Sekolah</h4>
                <p class="mb-0 text-muted small">Pilih, ikuti, dan kembangkan minat bakat Anda melalui klub ekstrakurikuler resmi sekolah.</p>
            </div>
            <div class="text-primary">
                <i data-feather="users" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="row g-4">
            
            <?php if (empty($ekstras)): ?>
                <div class="col-12">
                    <div class="card p-5 rounded-4 shadow-sm border-0 text-center bg-white">
                        <i data-feather="slash" class="text-muted mx-auto mb-3" style="width: 48px; height: 48px;"></i>
                        <h6 class="fw-bold text-dark">Data Ekstrakurikuler Kosong</h6>
                        <p class="text-muted mb-0 small">Belum ada daftar kegiatan ekstrakurikuler yang diinput oleh admin.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($ekstras as $e): 
                    // Set random icon based on name
                    $icon = 'star';
                    $colorClass = 'text-primary';
                    if (str_contains(strtolower($e['nama_ekstra']), 'pramuka')) {
                        $icon = 'compass';
                        $colorClass = 'text-warning';
                    } elseif (str_contains(strtolower($e['nama_ekstra']), 'futsal') || str_contains(strtolower($e['nama_ekstra']), 'bola')) {
                        $icon = 'activity';
                        $colorClass = 'text-success';
                    } elseif (str_contains(strtolower($e['nama_ekstra']), 'paskibra')) {
                        $icon = 'flag';
                        $colorClass = 'text-danger';
                    }
                ?>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="card ekstra-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="p-2 bg-light rounded-3 d-inline-block">
                                        <i data-feather="<?= $icon ?>" class="<?= $colorClass ?>" style="width: 28px; height: 28px;"></i>
                                    </div>
                                    <span class="badge bg-light text-dark border py-1.5 px-2.5 rounded-pill small fw-bold"><?= esc($e['jadwal_hari']) ?></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;"><?= esc($e['nama_ekstra']) ?></h5>
                                <p class="text-muted small mb-3">Dapatkan kesempatan mengasah kepemimpinan, kerja tim, dan skill taktis di klub ini.</p>
                            </div>
                            
                            <div>
                                <div class="text-muted small border-top pt-2.5 mb-3" style="font-size: 0.76rem;">
                                    <div class="mb-1"><i data-feather="user" class="me-1" style="width: 12px; height: 12px;"></i> Pembina: <strong><?= esc($e['pembina_nama']) ?></strong></div>
                                    <div><i data-feather="clock" class="me-1" style="width: 12px; height: 12px;"></i> Waktu: <strong><?= substr($e['waktu'], 0, 5) ?> WIB</strong></div>
                                </div>
                                <button class="btn btn-primary rounded-pill w-100 py-2 btn-daftar-ekstra fw-bold" data-name="<?= esc($e['nama_ekstra']) ?>"><i data-feather="plus-circle" class="me-1" style="width: 14px; height: 14px;"></i>Ikuti Kegiatan</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        $(".btn-daftar-ekstra").on("click", function() {
            const name = $(this).data("name");
            Swal.fire({
                title: 'Ikuti Ekstrakurikuler?',
                text: `Apakah Anda yakin ingin bergabung dengan kegiatan ekstrakurikuler ${name}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Gabung',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil bergabung!',
                        text: `Pendaftaran Anda di kegiatan ${name} berhasil dikonfirmasi. Silakan hubungi Pembina pada hari latihan.`,
                        customClass: { popup: 'rounded-4' }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
