<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    /* ===== Premium Banner Carousel ===== */
    .carousel-inner {
        border-radius: 20px;
        box-shadow: var(--card-shadow);
    }
    .banner-slide-item {
        height: 160px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding: 24px;
    }
    .bg-gradient-banner-1 {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
    }
    .bg-gradient-banner-2 {
        background: linear-gradient(135deg, #7c3aed 0%, #b84fe5 100%);
    }
    .bg-gradient-banner-3 {
        background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
    }
    .banner-circle-1 {
        position: absolute;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        top: -30px;
        right: 80px;
    }
    .banner-circle-2 {
        position: absolute;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        bottom: -70px;
        right: -30px;
    }
    .banner-bg-icon {
        right: 15px;
        bottom: -15px;
        opacity: 0.15;
        transform: rotate(-10deg);
        color: white;
    }

    /* ===== Digital Student ID Card (Credit Card Ratio) ===== */
    .student-id-card {
        background: var(--card-gradient);
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(30, 27, 75, 0.18);
        transition: transform 0.3s ease;
    }
    .student-id-card:hover {
        transform: translateY(-4px) rotate(0.5deg);
    }
    .card-bg-mesh {
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(0,0,0,0) 70%);
        top: -60px;
        right: -60px;
    }
    .card-chip {
        width: 38px;
        height: 28px;
        background: linear-gradient(135deg, #ffe082 0%, #ffb300 100%);
        border-radius: 6px;
        position: relative;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);
    }
    .card-chip::after {
        content: '';
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        border: 1.5px solid rgba(0,0,0,0.15);
        border-radius: 3px;
    }
    .small-stats-label {
        font-size: 0.65rem;
        opacity: 0.75;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .small-stats-val {
        font-size: 0.95rem;
        font-weight: 700;
        display: block;
        color: #ffffff;
    }

    /* ===== Layanan Akademik Responsive Launcher Grid ===== */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px 10px;
        text-align: center;
    }
    @media (max-width: 575.98px) {
        .features-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none !important;
        cursor: pointer;
        position: relative;
        transition: transform 0.2s;
    }
    .feature-item:active {
        transform: scale(0.92);
    }
    .feature-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .feature-item:hover .feature-icon-wrapper {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    .feature-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-color);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.25;
    }

    /* Service Color Systems */
    .fi-blue { background: rgba(59, 130, 246, 0.09) !important; color: #2563eb !important; }
    .fi-purple { background: rgba(139, 92, 246, 0.09) !important; color: #7c3aed !important; }
    .fi-orange { background: rgba(249, 115, 22, 0.09) !important; color: #ea580c !important; }
    .fi-green { background: rgba(16, 185, 129, 0.09) !important; color: #059669 !important; }
    .fi-teal { background: rgba(20, 184, 166, 0.09) !important; color: #0d9488 !important; }
    .fi-rose { background: rgba(244, 63, 94, 0.09) !important; color: #e11d48 !important; }
    .fi-amber { background: rgba(245, 158, 11, 0.09) !important; color: #d97706 !important; }
    .fi-cyan { background: rgba(6, 182, 212, 0.09) !important; color: #0891b2 !important; }

    /* ===== Core Ujian Section ===== */
    .card-ujian {
        border-radius: 18px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: var(--card-shadow);
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.03);
        padding: 1.4rem;
    }
    .card-ujian:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }
    .card-ujian .btn {
        border-radius: 12px;
        font-weight: 700;
        padding: 0.65rem;
        transition: all 0.2s ease;
    }
    .card-ujian .btn:hover {
        transform: translateY(-1px);
    }
    #searchUjian {
        height: 52px;
        border-radius: 26px;
        font-size: 0.92rem;
        border: 1px solid #e2e8f0;
        padding-left: 50px !important;
    }
    #sortTanggal, #filterHariIni {
        height: 52px;
        border-radius: 26px;
        font-size: 0.88rem;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
    }
    #searchUjian:focus, #sortTanggal:focus, #filterHariIni:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        outline: none;
    }

    /* ===== Agenda Widget ===== */
    .agenda-card {
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: var(--card-shadow);
    }
    .agenda-item-date {
        width: 48px;
        min-width: 48px;
        border-radius: 12px;
        text-align: center;
        padding: 8px;
        font-weight: 800;
    }

    /* Dark Mode overrides */
    body.dark .card-ujian {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
    body.dark #searchUjian, body.dark #sortTanggal, body.dark #filterHariIni {
        background-color: #1b2e4b;
        border-color: #253b5c;
        color: #fff;
    }
    body.dark .agenda-card {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
    body.dark .fi-blue { background: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    body.dark .fi-purple { background: rgba(139, 92, 246, 0.15) !important; color: #a78bfa !important; }
    body.dark .fi-orange { background: rgba(249, 115, 22, 0.15) !important; color: #fdba74 !important; }
    body.dark .fi-green { background: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; }
    body.dark .fi-teal { background: rgba(20, 184, 166, 0.15) !important; color: #2dd4bf !important; }
    body.dark .fi-rose { background: rgba(244, 63, 94, 0.15) !important; color: #fb7185 !important; }
    body.dark .fi-amber { background: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    body.dark .fi-cyan { background: rgba(6, 182, 212, 0.15) !important; color: #67e8f9 !important; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- 🖥️ Desktop Top Welcome Panel (Hidden on mobile) -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center gap-2 bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Selamat Datang, <?= esc($peserta['nama'] ?? 'Siswa') ?>! 👋</h4>
                <p class="mb-0 text-muted small">NISN: <?= esc($peserta['nisn'] ?? '-') ?> | Kelas: <?= esc($peserta['kelas'] ?? '-') ?> | Sekolah: <?= esc($setting->nama_sekolah ?? 'Candy School') ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted fw-bold px-3 py-2 bg-light rounded-pill small" id="datetime">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span id="datetime-text"></span>
            </div>
        </div>

        <!-- 🏢 Main Layout Row -->
        <div class="row">
            <!-- 👈 LEFT COLUMN: Carousel Banner, Layanan Akademik, Ujian Section (Width 8 on Desktop, 12 on Mobile) -->
            <div class="col-12 col-md-8 mb-4">
                
                <!-- 📢 Banner Carousel / Slider (Announcements) -->
                <div id="schoolAnnouncements" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#schoolAnnouncements" data-bs-slide-to="0" class="active" aria-current="true"></button>
                        <button type="button" data-bs-target="#schoolAnnouncements" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#schoolAnnouncements" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <!-- Slide 1 -->
                        <div class="carousel-item active" data-bs-interval="4500">
                            <div class="banner-slide-item bg-gradient-banner-1 text-white">
                                <div class="banner-circle-1"></div>
                                <div class="banner-circle-2"></div>
                                <div class="z-1 py-1" style="max-width: 80%;">
                                    <span class="badge mb-2 py-1 px-3 rounded-pill small" style="background: rgba(255,255,255,0.22); font-weight: 700;">INFORMASI UTS</span>
                                    <h5 class="fw-bold mb-1" style="font-size: 1.15rem;">Pekan Penilaian Tengah Semester</h5>
                                    <p class="small text-white-80 mb-0">Persiapkan diri Anda untuk pekan ujian UTS Genap mulai Senin depan.</p>
                                </div>
                                <div class="banner-bg-icon position-absolute">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                </div>
                            </div>
                        </div>
                        <!-- Slide 2 -->
                        <div class="carousel-item" data-bs-interval="4500">
                            <div class="banner-slide-item bg-gradient-banner-2 text-white">
                                <div class="banner-circle-1"></div>
                                <div class="banner-circle-2"></div>
                                <div class="z-1 py-1" style="max-width: 80%;">
                                    <span class="badge mb-2 py-1 px-3 rounded-pill small" style="background: rgba(255,255,255,0.22); font-weight: 700;">E-LIBRARY</span>
                                    <h5 class="fw-bold mb-1" style="font-size: 1.15rem;">Materi & E-Book Baru Tersedia</h5>
                                    <p class="small text-white-80 mb-0">Kini tersedia 200+ materi pelajaran digital baru di tab Materi Belajar.</p>
                                </div>
                                <div class="banner-bg-icon position-absolute">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                </div>
                            </div>
                        </div>
                        <!-- Slide 3 -->
                        <div class="carousel-item" data-bs-interval="4500">
                            <div class="banner-slide-item bg-gradient-banner-3 text-white">
                                <div class="banner-circle-1"></div>
                                <div class="banner-circle-2"></div>
                                <div class="z-1 py-1" style="max-width: 80%;">
                                    <span class="badge mb-2 py-1 px-3 rounded-pill small" style="background: rgba(255,255,255,0.22); font-weight: 700;">PRESTASI</span>
                                    <h5 class="fw-bold mb-1" style="font-size: 1.15rem;">Juara 1 Lomba Coding Nasional</h5>
                                    <p class="small text-white-80 mb-0">Selamat kepada Tim Web Dev Sekolah atas raihan Medali Emas tingkat Nasional!</p>
                                </div>
                                <div class="banner-bg-icon position-absolute">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🪪 Mobile Student ID Card (Only visible on mobile screens below Carousel) -->
                <div class="student-id-card position-relative overflow-hidden mb-4 shadow-sm d-md-none">
                    <div class="card-bg-mesh"></div>
                    <div class="card-content-wrapper p-3 text-white position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-uppercase tracking-wider small text-white-50 mb-0" style="font-size: 0.62rem; font-weight: 800;">Digital Student Card</p>
                                <h6 class="fw-bold mb-0 text-white" style="font-size: 0.88rem;"><?= esc($setting->nama_sekolah ?? 'SMART SCHOOL SYSTEM') ?></h6>
                            </div>
                            <div class="card-chip"></div>
                        </div>
                        
                        <div class="my-3 py-1">
                            <h4 class="fw-bold tracking-wide mb-1 text-truncate" style="font-size: 1.2rem;"><?= esc($peserta['nama'] ?? 'Nama Siswa') ?></h4>
                            <p class="mb-0 text-white-80 small" style="font-size: 0.75rem;">NISN: <span class="text-white fw-bold"><?= esc($peserta['nisn'] ?? '-') ?></span> | Kelas: <span class="text-white fw-bold"><?= esc($peserta['kelas'] ?? '-') ?></span></p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end mt-3 pt-2 border-top" style="border-top-color: rgba(255,255,255,0.15) !important;">
                            <div class="d-flex gap-4 text-center">
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Kehadiran</span>
                                    <span class="small-stats-val attendance-pct-val">98%</span>
                                </div>
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Ujian Aktif</span>
                                    <span class="small-stats-val font-ujian-count">0</span>
                                </div>
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Point XP</span>
                                    <span class="small-stats-val">150 XP</span>
                                </div>
                            </div>
                            <div class="qr-code-icon" style="cursor: pointer; background: rgba(255,255,255,0.15); padding: 7px; border-radius: 8px;" onclick="showQRCard()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🎛️ Layanan Akademik Launcher Grid -->
                <div class="features-section mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">Layanan Akademik</h5>
                        <span class="text-primary small fw-bold" style="cursor: pointer;" onclick="scrollToExams()">Lihat Ujian</span>
                    </div>
                    
                    <div class="features-grid">
                        <!-- 1. Ujian Online -->
                        <a href="javascript:void(0)" class="feature-item hover-scale" onclick="scrollToExams()">
                            <div class="feature-icon-wrapper fi-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </div>
                            <span class="feature-label">Ujian Online</span>
                        </a>
                        
                        <!-- 2. Jadwal Pelajaran -->
                        <a href="<?= base_url('siswa/jadwal') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-purple">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <span class="feature-label">Jadwal Kelas</span>
                        </a>
                        
                        <!-- 3. Tugas & PR -->
                        <a href="<?= base_url('siswa/tugas') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-orange">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </div>
                            <span class="feature-label">Tugas & PR</span>
                        </a>
                        
                        <!-- 4. Materi Belajar -->
                        <a href="<?= base_url('siswa/materi') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </div>
                            <span class="feature-label">Materi</span>
                        </a>
                        
                        <!-- 5. Absensi QR -->
                        <a href="<?= base_url('siswa/absensi') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-teal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"></path><path d="M17 3h2a2 2 0 0 1 2 2v2"></path><path d="M21 17v2a2 2 0 0 1-2 2h-2"></path><path d="M7 21H5a2 2 0 0 1-2-2v-2"></path><rect x="7" y="7" width="10" height="10" rx="1"></rect></svg>
                            </div>
                            <span class="feature-label">Absensi QR</span>
                        </a>
                        
                        <!-- 6. Rapor Digital -->
                        <a href="<?= base_url('siswa/rapor') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-rose">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            </div>
                            <span class="feature-label">Rapor Nilai</span>
                        </a>
                        
                        <!-- 7. Biaya SPP -->
                        <a href="<?= base_url('siswa/keuangan') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-amber">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            </div>
                            <span class="feature-label">Biaya SPP</span>
                        </a>

                        <!-- 8. Kesiswaan -->
                        <a href="<?= base_url('siswa/kesiswaan') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            </div>
                            <span class="feature-label">Kesiswaan</span>
                        </a>

                        <!-- 9. Ekstrakurikuler -->
                        <a href="<?= base_url('siswa/ekstra') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-purple">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <span class="feature-label">Ekstra</span>
                        </a>
                        
                        <!-- 10. Info Sekolah -->
                        <a href="<?= base_url('siswa/info') ?>" class="feature-item hover-scale">
                            <div class="feature-icon-wrapper fi-cyan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </div>
                            <span class="feature-label">Info Sekolah</span>
                        </a>
                    </div>
                </div>

                <!-- 📝 Exam List Section -->
                <div id="section-ujian-core" class="pt-3">
                    <div class="section-header d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">Daftar Ujian Aktif</h5>
                        <div class="d-md-none text-muted small" id="datetime-mobile">
                            <span id="datetime-text-mobile" style="font-size: 0.72rem; font-weight: 700;"></span>
                        </div>
                    </div>

                    <!-- Search, Sort & Filters -->
                    <div class="d-flex align-items-center mb-4 gap-2 flex-wrap">
                        <div class="flex-grow-1 position-relative">
                            <input type="text" id="searchUjian" class="form-control shadow-sm" placeholder="Cari ujian...">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="position-absolute" style="top:50%; left:18px; transform:translateY(-50%); color:#94a3b8;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="sortTanggal" class="form-select shadow-sm pe-4">
                                <option value="desc">Terbaru</option>
                                <option value="asc">Terlama</option>
                            </select>
                            <select id="filterHariIni" class="form-select shadow-sm pe-4">
                                <option value="all">Semua</option>
                                <option value="today">Hari Ini</option>
                            </select>
                        </div>
                    </div>

                    <!-- Target Grid for Exam Cards -->
                    <div class="row" id="ujian-list"></div>

                    <nav>
                        <ul class="pagination mt-3" id="pagination"></ul>
                    </nav>
                </div>

            </div>

            <!-- 👉 RIGHT COLUMN: Digital ID Card, Quick Calendar Widget (Desktop Only) -->
            <div class="col-12 col-md-4 d-none d-md-block mb-4">
                
                <!-- Desktop Digital Student ID Card -->
                <div class="student-id-card position-relative overflow-hidden mb-4">
                    <div class="card-bg-mesh"></div>
                    <div class="card-content-wrapper p-4 text-white position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <p class="text-uppercase tracking-wider small text-white-50 mb-0" style="font-size: 0.65rem; font-weight: 800;">Digital Student Card</p>
                                <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;"><?= esc($setting->nama_sekolah ?? 'SMART SCHOOL SYSTEM') ?></h6>
                            </div>
                            <div class="card-chip"></div>
                        </div>
                        
                        <div class="my-4 py-2">
                            <h4 class="fw-bold tracking-wide mb-1 text-truncate" style="font-size: 1.35rem;"><?= esc($peserta['nama'] ?? 'Nama Siswa') ?></h4>
                            <p class="mb-0 text-white-85 small" style="font-size: 0.82rem;">NISN: <span class="text-white fw-bold"><?= esc($peserta['nisn'] ?? '-') ?></span> | Kelas: <span class="text-white fw-bold"><?= esc($peserta['kelas'] ?? '-') ?></span></p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end mt-4 pt-3 border-top" style="border-top-color: rgba(255,255,255,0.15) !important;">
                            <div class="d-flex gap-4 text-center">
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Kehadiran</span>
                                    <span class="small-stats-val attendance-pct-val">98%</span>
                                </div>
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Ujian Aktif</span>
                                    <span class="small-stats-val font-ujian-count">0</span>
                                </div>
                                <div>
                                    <span class="d-block text-white-50 small-stats-label">Point XP</span>
                                    <span class="small-stats-val">150 XP</span>
                                </div>
                            </div>
                            <div class="qr-code-icon" style="cursor: pointer; background: rgba(255,255,255,0.15); padding: 8px; border-radius: 10px;" onclick="showQRCard()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Upcoming Events Agenda Widget -->
                <div class="card agenda-card border-0 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-4"><span class="text-primary me-2">•</span>Agenda Terdekat</h6>
                    <div class="agenda-list">
                        <!-- Agenda 1 -->
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <div class="agenda-item-date bg-primary-subtle text-primary" style="background-color: #eff6ff;">
                                <span class="d-block text-uppercase" style="font-size: 0.6rem; opacity: 0.8; letter-spacing: 0.5px;">Jun</span>
                                <span class="d-block leading-none" style="font-size: 1.1rem; line-height: 1;">08</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Ujian Tengah Semester</h6>
                                <p class="text-muted small mb-0">PTS Semester Genap TA 2025/2026.</p>
                            </div>
                        </div>
                        
                        <!-- Agenda 2 -->
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <div class="agenda-item-date bg-purple-subtle text-purple" style="background-color: #f5f3ff; color: #8b5cf6;">
                                <span class="d-block text-uppercase" style="font-size: 0.6rem; opacity: 0.8; letter-spacing: 0.5px;">Jun</span>
                                <span class="d-block leading-none" style="font-size: 1.1rem; line-height: 1;">12</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Batas Upload Laporan PR</h6>
                                <p class="text-muted small mb-0">Praktikum Kimia Asam Basa wajib dikumpul.</p>
                            </div>
                        </div>
                        
                        <!-- Agenda 3 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="agenda-item-date bg-success-subtle text-success" style="background-color: #f0fdf4; color: #10b981;">
                                <span class="d-block text-uppercase" style="font-size: 0.6rem; opacity: 0.8; letter-spacing: 0.5px;">Jun</span>
                                <span class="d-block leading-none" style="font-size: 1.1rem; line-height: 1;">20</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Pembagian Rapor Semester</h6>
                                <p class="text-muted small mb-0">Pengambilan hasil belajar didampingi Wali Murid.</p>
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
    // Realtime digital clock script
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const localeString = now.toLocaleString('id-ID', options).replace('pukul', ' ');
        
        const desktopClock = document.getElementById('datetime-text');
        if (desktopClock) desktopClock.innerText = localeString;
        
        const mobileClock = document.getElementById('datetime-text-mobile');
        if (mobileClock) mobileClock.innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' WIB';
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Scroll helper methods
    function scrollToExams() {
        $('html, body').animate({
            scrollTop: $("#section-ujian-core").offset().top - 20
        }, 300);
    }

    function scrollToTop() {
        $('html, body').animate({
            scrollTop: 0
        }, 300);
    }

    // Core variables
    let ujianData = [];
    let currentPage = 1;
    const perPage = 6;

    // Render active exam cards
    function renderUjian() {
        const container = $("#ujian-list").empty();
        const search = $("#searchUjian").val().toLowerCase();
        const sort = $("#sortTanggal").val();
        const filter = $("#filterHariIni").val();
        const today = new Date().toISOString().split('T')[0];

        let filtered = ujianData.filter(u =>
            u.nama_bank_soal.toLowerCase().includes(search) ||
            u.kode_ujian.toLowerCase().includes(search)
        );

        if (filter === "today") filtered = filtered.filter(u => u.waktu_mulai.startsWith(today));
        filtered.sort((a, b) => sort === "asc" ? new Date(a.waktu_mulai) - new Date(b.waktu_mulai) : new Date(b.waktu_mulai) - new Date(a.waktu_mulai));

        // Sync exam statistics across student card
        const pendingExams = filtered.filter(u => u.status_peserta !== 'selesai').length;
        $(".font-ujian-count").text(pendingExams);

        const totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const paginated = filtered.slice(start, end);

        if (paginated.length === 0) {
            container.html('<div class="col-12 text-center py-4"><p class="text-muted small">Tidak ada ujian aktif saat ini.</p></div>');
            $("#pagination").empty();
            return;
        }

        paginated.forEach(u => {
            const mulai = new Date(u.waktu_mulai);
            const selesai = new Date(u.waktu_selesai);

            let statusBadge = '', buttonHtml = '';
            if (u.status_peserta === 'selesai') {
                statusBadge = '<span class="badge bg-success-subtle text-success rounded-pill fw-bold" style="background-color: #e0f2fe; color: #0284c7 !important;">Selesai</span>';
                buttonHtml = u.tampil_nilai == 1 ?
                    `<button class="btn btn-success btn-sm w-100 lihat-hasil-btn shadow-sm"><i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>Lihat Hasil Ujian</button>` :
                    `<button class="btn btn-outline-success btn-sm w-100" disabled>Ujian Selesai</button>`;
            } else if (u.status_peserta === 'sedang_mengerjakan') {
                statusBadge = '<span class="badge bg-primary-subtle text-primary rounded-pill fw-bold" style="background-color: #eff6ff; color: #3b82f6 !important;">Dikerjakan</span>';
                buttonHtml = `<button class="btn btn-warning text-white btn-sm w-100 melanjutkan-ujian-btn shadow-sm" data-id="${u.id}" data-pakai-token="${u.pakai_token}"><i data-feather="play-circle" class="me-1" style="width: 14px; height: 14px;"></i>Lanjutkan Ujian</button>`;
            } else if (u.status_waktu === 'belum_mulai') {
                statusBadge = '<span class="badge bg-warning-subtle text-warning rounded-pill fw-bold" style="background-color: #fffbeb; color: #d97706 !important;">Belum Mulai</span>';
                buttonHtml = '<button class="btn btn-outline-secondary btn-sm w-100" disabled>Belum Mulai</button>';
            } else if (u.status_waktu === 'dibuka') {
                statusBadge = '<span class="badge bg-success-subtle text-success rounded-pill fw-bold" style="background-color: #f0fdf4; color: #16a34a !important;">Dibuka</span>';
                buttonHtml = `<button class="btn btn-primary btn-sm w-100 mulai-ujian-btn shadow-sm" data-id="${u.id}" data-pakai-token="${u.pakai_token}"><i data-feather="play" class="me-1" style="width: 14px; height: 14px;"></i>Mulai Mengerjakan</button>`;
            } else if (u.status_waktu === 'terlambat') {
                statusBadge = '<span class="badge bg-danger-subtle text-danger rounded-pill fw-bold" style="background-color: #fef2f2; color: #dc2626 !important;">Terlambat</span>';
                buttonHtml = '<button class="btn btn-outline-danger btn-sm w-100" disabled>Waktu Habis</button>';
            }

            container.append(`
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mb-3">
                    <div class="card-ujian h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 75%; font-size: 0.88rem;">${u.nama_ujian}</h6>
                                <span class="text-muted small fw-semibold">(${u.kode_ujian})</span>
                            </div>

                            <div class="text-muted small mb-3" style="font-size: 0.72rem;">
                                <div class="mb-1"><i data-feather="calendar" class="me-1" style="width: 11px; height: 11px;"></i> Mulai: ${mulai.toLocaleDateString('id-ID')} - ${mulai.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} WIB</div>
                                <div><i data-feather="clock" class="me-1" style="width: 11px; height: 11px;"></i> Selesai: ${selesai.toLocaleDateString('id-ID')} - ${selesai.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} WIB</div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-info-subtle text-info rounded-pill fw-bold" style="background-color: #ecfeff; color:#0891b2 !important; font-size: 0.72rem;">${u.durasi_ujian} Menit</span>
                                ${statusBadge}
                            </div>
                            ${buttonHtml}
                        </div>
                    </div>
                </div>
            `);
        });

        let paginationHtml = '';
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        $("#pagination").html(paginationHtml);
        feather.replace();
    }

    $(document).ready(function() {
        // Load exam data
        $.ajax({
            url: "<?= base_url('peserta/ujian/getall') ?>",
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    ujianData = res.data;
                    renderUjian();
                }
            }
        });

        // Filter event listeners
        $("#searchUjian, #sortTanggal, #filterHariIni").on("input change", function() {
            currentPage = 1;
            renderUjian();
        });
        
        $(document).on("click", "#pagination .page-link", function() {
            currentPage = parseInt($(this).data("page"));
            renderUjian();
        });

        // Action routes
        $(document).on('click', '.lihat-hasil-btn', function() {
            window.location.href = `<?= base_url('peserta/ujian/hasil/') ?>${$(this).data('id')}`;
        });

        $(document).on('click', '.mulai-ujian-btn', function() {
            const id = $(this).data('id');
            const pakaiToken = $(this).data('pakai-token') == 1;
            if (pakaiToken) {
                Swal.fire({
                    title: 'Masukkan Token Ujian',
                    input: 'text',
                    inputPlaceholder: 'Token Ujian',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Mulai',
                    showLoaderOnConfirm: true,
                    customClass: {
                        popup: 'rounded-4'
                    },
                    preConfirm: (token) => {
                        if (!token || token.trim() === '') {
                            Swal.showValidationMessage('Token tidak boleh kosong.');
                            return false;
                        }
                        return $.ajax({
                            url: `<?= base_url('peserta/ujian/cektoken') ?>`,
                            method: 'POST',
                            data: {
                                ujian_id: id,
                                token: token
                            },
                            dataType: 'json'
                        }).then(res => {
                            if (!res.status) throw new Error(res.message || 'Token salah.');
                            return true;
                        }).catch(err => {
                            Swal.showValidationMessage(err.message);
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed) window.location.href = `<?= base_url('peserta/ujian/mulai/') ?>${id}`;
                });
            } else window.location.href = `<?= base_url('peserta/ujian/mulai/') ?>${id}`;
        });

        $(document).on('click', '.lanjutkan-ujian-btn', function() {
            const id = $(this).data('id');
            const pakaiToken = $(this).data('pakai-token') == 1;

            if (pakaiToken) {
                Swal.fire({
                    title: 'Masukkan Token Ujian',
                    input: 'text',
                    inputPlaceholder: 'Token Ujian',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan',
                    showLoaderOnConfirm: true,
                    customClass: {
                        popup: 'rounded-4'
                    },
                    preConfirm: (token) => {
                        if (!token || token.trim() === '') {
                            Swal.showValidationMessage('Token tidak boleh kosong.');
                            return false;
                        }
                        return $.ajax({
                            url: `<?= base_url('peserta/ujian/cektoken') ?>`,
                            method: 'POST',
                            data: {
                                ujian_id: id,
                                token: token
                            },
                            dataType: 'json'
                        }).then(res => {
                            if (!res.status) throw new Error(res.message || 'Token salah.');
                            return true;
                        }).catch(err => {
                            Swal.showValidationMessage(err.message);
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed) window.location.href = `<?= base_url('peserta/ujian/lanjut/') ?>${id}`;
                });
            } else {
                window.location.href = `<?= base_url('peserta/ujian/lanjut/') ?>${id}`;
            }
        });
    });
</script>
<?= $this->endSection(); ?>