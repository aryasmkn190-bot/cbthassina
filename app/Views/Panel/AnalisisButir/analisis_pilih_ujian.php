<?= $this->extend('Layout/main') ?>

<?= $this->section('css') ?>
<style>
    /* Force main content container to display as block instead of flex row */
    body #content > .container,
    body.dark #content > .container,
    .layout-boxed #content > .container {
        display: block !important;
    }

    /* Hero Panel with subtle gradient mesh */
    .hero-panel {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
        border: 1px solid rgba(99, 102, 241, 0.1);
        border-radius: 16px;
        padding: 18px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }
    body.dark .hero-panel {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(168, 85, 247, 0.12) 100%);
        border-color: rgba(99, 102, 241, 0.2);
    }
    .hero-pattern {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Search Box styling */
    .search-wrapper {
        position: relative;
        width: 100%;
        max-width: 280px;
    }
    .search-wrapper input {
        padding-left: 36px !important;
        border-radius: 30px !important;
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 38px;
        font-size: 0.85rem;
    }
    body.dark .search-wrapper input {
        border-color: rgba(255, 255, 255, 0.1);
        background: #1f2937;
        color: #f9fafb;
    }
    .search-wrapper input:focus {
        border-color: #4f46e5;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15);
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
        width: 14px;
        height: 14px;
    }
    
    /* Exam list card design */
    .ujian-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 6px 15px rgba(0,0,0,0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border-left: 4px solid transparent;
        cursor: pointer;
    }
    body.dark .ujian-card {
        background: #1f2937;
        box-shadow: 0 6px 15px rgba(0,0,0,0.12);
    }
    .ujian-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.10);
    }
    body.dark .ujian-card:hover {
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.20);
    }
    
    .ujian-card.accent-active { border-left-color: #10b981; }
    .ujian-card.accent-inactive { border-left-color: #ef4444; }
    
    /* Round icon circles */
    .icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .icon-circle-blue {
        background: rgba(99, 102, 241, 0.08);
        color: #4f46e5;
    }
    body.dark .icon-circle-blue {
        background: rgba(129, 140, 248, 0.18);
        color: #818cf8;
    }
    .icon-circle-purple {
        background: rgba(168, 85, 247, 0.08);
        color: #a855f7;
    }
    body.dark .icon-circle-purple {
        background: rgba(192, 132, 252, 0.18);
        color: #c084fc;
    }
    
    /* Finished indicator with pulsing green dot */
    .pulse-indicator {
        display: inline-flex;
        align-items: center;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    body.dark .pulse-indicator {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        margin-right: 6px;
        position: relative;
    }
    .pulse-dot::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: #10b981;
        border-radius: 50%;
        animation: pulse-ring 1.2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        top: 0; left: 0;
    }
    
    @keyframes pulse-ring {
        0% { transform: scale(0.95); opacity: 0.5; }
        50% { opacity: 0.3; }
        100% { transform: scale(2.5); opacity: 0; }
    }
    
    .status-empty-banner {
        border-radius: 10px;
        padding: 8px 12px;
        background: rgba(239, 68, 68, 0.04);
        border: 1px solid rgba(239, 68, 68, 0.12);
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
    }
    body.dark .status-empty-banner {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.2);
    }

    /* Badges */
    .glass-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 30px;
        display: inline-block;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .glass-badge-primary {
        background-color: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
    }
    body.dark .glass-badge-primary {
        background-color: rgba(129, 140, 248, 0.18);
        color: #818cf8;
    }
    .glass-badge-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    body.dark .glass-badge-danger {
        background-color: rgba(239, 68, 68, 0.18);
        color: #f87171;
    }
    
    /* Modern button styling */
    .btn-outline-modern {
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        border-radius: 30px;
        background: #ffffff;
        color: #4b5563;
        font-weight: 600;
        transition: all 0.3s ease;
        height: 38px;
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }
    body.dark .btn-outline-modern {
        background: #1f2937;
        border-color: rgba(255, 255, 255, 0.1);
        color: #d1d5db;
    }
    .btn-outline-modern:hover {
        background: #f3f4f6;
        color: #4f46e5;
        border-color: rgba(79, 70, 229, 0.3);
        transform: translateY(-2px);
    }
    body.dark .btn-outline-modern:hover {
        background: #374151;
        color: #818cf8;
    }

    /* Modern breadcrumbs */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 12px;
    }
    .breadcrumb-item a {
        color: #4f46e5;
        font-weight: 500;
        text-decoration: none;
    }
    body.dark .breadcrumb-item a {
        color: #818cf8;
    }
    .breadcrumb-item.active {
        color: #6b7280;
        font-weight: 500;
    }
    body.dark .breadcrumb-item.active {
        color: #9ca3af;
    }

    .ujian-card:hover .text-primary .transition-transform {
        transform: translateX(4px);
    }

    /* Responsive Mobile styling */
    @media (max-width: 575.98px) {
        .hero-panel {
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .hero-panel h4 {
            font-size: 1.15rem !important;
        }
        .hero-panel p {
            font-size: 0.75rem !important;
        }
        .w-100-mobile {
            width: 100%;
        }
        .search-wrapper {
            max-width: none !important;
        }
        .ujian-card {
            border-radius: 14px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('panel/analisis-butir') ?>">Analisis Butir Soal</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($bankSoal['nama']) ?></li>
        </ol>
    </nav>

    <!-- Bank Soal Info Card -->
    <div class="hero-panel">
        <div class="hero-pattern"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="glass-badge glass-badge-primary mb-2"><?= esc($bankSoal['kode']) ?></span>
                <h4 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-feather="layers" style="width: 24px; height: 24px; color: #4f46e5;"></i>
                    Pilih Jadwal Ujian
                </h4>
                <p class="text-muted mb-0 small">Bank Soal: <strong class="text-dark-light"><?= esc($bankSoal['nama']) ?></strong></p>
            </div>
            <div class="d-flex gap-2 align-items-center w-100-mobile">
                <div class="search-wrapper flex-grow-1 flex-sm-grow-0">
                    <i data-feather="search" class="search-icon"></i>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari jadwal ujian...">
                </div>
                <a href="<?= base_url('panel/analisis-butir') ?>" class="btn-outline-modern">
                    <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Ujian List Grid -->
    <div class="row g-3 mb-5" id="ujianListContainer">
        <?php if (empty($ujianList)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <h5 class="fw-bold text-dark mb-1">Belum Ada Ujian</h5>
                <p class="text-muted">Jadwal ujian yang menggunakan bank soal ini belum dibuat atau belum berlangsung.</p>
            </div>
        <?php else: ?>
            <?php foreach ($ujianList as $ujian): ?>
                <?php
                    $pesertaSelesai = (int)($ujian['jumlah_peserta_selesai'] ?? 0);
                    $accentClass = $pesertaSelesai > 0 ? 'accent-active' : 'accent-inactive';
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch">
                    <a href="<?= base_url('panel/analisis-butir/detail/' . esc($ujian['id'], 'url')) ?>" class="text-decoration-none w-100 d-flex">
                        <div class="card ujian-card <?= $accentClass ?> w-100">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="pe-2" style="flex: 1;">
                                            <span class="glass-badge glass-badge-primary mb-2"><?= esc($ujian['kode_ujian']) ?></span>
                                            <h6 class="fw-bold text-dark mb-0 lh-sm fs-6"><?= esc($ujian['nama_ujian']) ?></h6>
                                        </div>
                                        <?php if ($pesertaSelesai > 0): ?>
                                            <span class="pulse-indicator flex-shrink-0">
                                                <span class="pulse-dot"></span>
                                                <?= $pesertaSelesai ?> Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="glass-badge glass-badge-danger flex-shrink-0">
                                                0 Selesai
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Info Meta Grid -->
                                    <div class="mt-3 pt-2 border-top d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center text-muted">
                                            <div class="icon-circle icon-circle-blue">
                                                <i data-feather="calendar" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Ujian</div>
                                                <div class="fw-semibold text-dark small" style="font-size: 0.8rem;"><?= esc(date('d M Y', strtotime($ujian['waktu_mulai']))) ?></div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <div class="icon-circle icon-circle-purple">
                                                <i data-feather="clock" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Durasi Ujian</div>
                                                <div class="fw-semibold text-dark small" style="font-size: 0.8rem;"><?= esc($ujian['durasi_ujian'] ?? '-') ?> Menit</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($pesertaSelesai === 0): ?>
                                    <div class="mt-3">
                                        <div class="status-empty-banner small fw-medium">
                                            <i data-feather="alert-circle" width="14" height="14" class="flex-shrink-0"></i>
                                            <span>Menunggu peserta menyelesaikan ujian...</span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3 pt-2 d-flex justify-content-end align-items-center">
                                        <span class="small fw-bold text-primary d-flex align-items-center gap-1">
                                            Buka Dashboard Analisis
                                            <i data-feather="arrow-right" width="14" height="14" class="transition-transform"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pagejs') ?>
<script>
$(document).ready(function() {
    feather.replace();

    // Client-side search filtering logic
    $('#searchBox').on('input', function() {
        const query = this.value.toLowerCase().trim();
        let hasVisibleCard = false;

        $('#ujianListContainer > div').each(function() {
            const col = $(this);
            // Don't process empty state div
            if (col.hasClass('text-center') && col.find('svg').length > 0) {
                return;
            }

            const title = col.find('.ujian-card h6').text().toLowerCase();
            const kode = col.find('.ujian-card .glass-badge').text().toLowerCase();
            
            if (title.indexOf(query) !== -1 || kode.indexOf(query) !== -1) {
                col.removeClass('d-none').addClass('d-flex');
                hasVisibleCard = true;
            } else {
                col.removeClass('d-flex').addClass('d-none');
            }
        });

        // Show/hide empty state if no search matches
        let emptyState = $('#noSearchMatches');
        if (!hasVisibleCard && query !== '') {
            if (emptyState.length === 0) {
                $('#ujianListContainer').append(`
                    <div class="col-12 text-center py-5" id="noSearchMatches">
                        <div class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Hasil</h5>
                        <p class="text-muted">Tidak ada jadwal ujian yang cocok dengan pencarian Anda.</p>
                    </div>
                `);
            } else {
                emptyState.show();
            }
        } else {
            if (emptyState.length > 0) {
                emptyState.remove();
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
