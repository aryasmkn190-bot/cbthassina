<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .info-card {
        border-radius: 20px;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .info-item {
        transition: transform 0.2s ease;
    }
    .info-item:hover {
        transform: translateX(4px);
    }
    body.dark .info-card {
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
                <h4 class="mb-1 fw-bold text-dark">Informasi & Pengumuman Sekolah</h4>
                <p class="mb-0 text-muted small">Dapatkan pengumuman terbaru mengenai kegiatan belajar mengajar, libur, dan agenda penting sekolah.</p>
            </div>
            <div class="text-info">
                <i data-feather="bell" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white info-card mb-4">
            <h5 class="fw-bold mb-4 text-dark"><i data-feather="list" class="text-primary me-2"></i>Pengumuman Terbaru</h5>

            <div class="info-list">
                
                <!-- Notice 1 -->
                <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-primary info-item">
                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                        <span class="text-primary small fw-bold">26 Mei 2026</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill py-1 px-2.5" style="font-size: 0.65rem; background: rgba(79, 70, 229, 0.08); color: #4f46e5 !important;">PENTING</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Pengumuman Libur Hari Raya Waisak</h6>
                    <p class="text-muted small mb-0">Sehubungan dengan peringatan Hari Raya Waisak, kegiatan belajar mengajar ditiadakan pada hari Kamis, tanggal 28 Mei 2026. Siswa kembali masuk sekolah seperti biasa pada hari Jumat, 29 Mei 2026.</p>
                </div>

                <!-- Notice 2 -->
                <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-purple info-item" style="border-left-color: #8b5cf6 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                        <span class="text-purple small fw-bold" style="color: #8b5cf6;">20 Mei 2026</span>
                        <span class="badge bg-purple-subtle text-purple rounded-pill py-1 px-2.5" style="font-size: 0.65rem; background: rgba(139, 92, 246, 0.08); color: #8b5cf6 !important;">AKADEMIK</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Jadwal Ulang Ujian Susulan Praktek Olahraga</h6>
                    <p class="text-muted small mb-0">Ujian susulan praktek lari 12 menit bagi siswa yang berhalangan sakit pada jadwal utama akan dilaksanakan hari Kamis di stadion utama sekolah pukul 14:00 WIB. Harap hadir dengan pakaian olahraga lengkap.</p>
                </div>

                <!-- Notice 3 -->
                <div class="p-3 bg-light rounded-4 border-start border-4 border-success info-item" style="border-left-color: #10b981 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                        <span class="text-success small fw-bold" style="color: #10b981;">15 Mei 2026</span>
                        <span class="badge bg-success-subtle text-success rounded-pill py-1 px-2.5" style="font-size: 0.65rem; background: rgba(16, 185, 129, 0.08); color: #059669 !important;">KEGIATAN</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Vaksinasi Booster Gratis Tahap-2</h6>
                    <p class="text-muted small mb-0">Bagi siswa yang ingin mendaftar vaksinasi booster gratis kerjasama dengan Puskesmas, silakan mengumpulkan fotokopi KK ke ruang UKS maksimal hari Jumat ini pukul 11:30 WIB.</p>
                </div>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    // Info details logic if any
</script>
<?= $this->endSection(); ?>
