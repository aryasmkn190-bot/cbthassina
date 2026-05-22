<?= $this->extend('Layout/main') ?>

<?= $this->section('css') ?>
<style>
    /* Force main content container to display as block instead of flex row */
    #content > .container {
        display: block !important;
    }

    /* Glassmorphic Loading Screen */
    #loadingOverlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition: all 0.3s ease;
    }
    body.dark #loadingOverlay {
        background: rgba(17, 24, 39, 0.65);
    }
    
    /* Loading Dual Ring Spinner */
    .spinner-dual {
        display: inline-block;
        width: 60px;
        height: 60px;
        border: 4px solid rgba(99, 102, 241, 0.1);
        border-radius: 50%;
        border-top-color: #4f46e5;
        border-bottom-color: #a855f7;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Modern control action bar */
    .control-panel {
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 16px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    body.dark .control-panel {
        background: rgba(31, 41, 55, 0.6);
        border-color: rgba(255, 255, 255, 0.05);
    }

    .select-modern {
        border-radius: 30px !important;
        padding-left: 16px;
        padding-right: 32px;
        height: 38px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    body.dark .select-modern {
        background-color: #1f2937;
        color: #f3f4f6;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .select-modern:focus {
        border-color: #4f46e5;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15);
    }

    .btn-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff !important;
        border: none;
        border-radius: 30px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.3s ease;
        height: 38px;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-gradient-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.3);
    }
    
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
    .btn-outline-modern:hover svg {
        transform: rotate(30deg);
    }
    .btn-outline-modern svg {
        transition: transform 0.3s ease;
    }

    /* Premium Summary Cards */
    .stat-card {
        border: none;
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(0,0,0,0.02);
    }
    .stat-card:hover {
        transform: translateY(-6px);
    }
    .stat-card .stat-value {
        font-size: 2.25rem;
        font-weight: 850;
        line-height: 1;
        letter-spacing: -1px;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        opacity: 0.85;
        margin-top: 6px;
    }
    .stat-card .stat-badge {
        font-size: 0.7rem;
        padding: 4px 12px;
        border-radius: 50px;
        margin-top: 8px;
        display: inline-block;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card-blue {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.15);
    }
    .stat-card-green {
        background: linear-gradient(135deg, #0d9488 0%, #10b981 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(13, 148, 136, 0.15);
    }
    .stat-card-orange {
        background: linear-gradient(135deg, #ea580c 0%, #f43f5e 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(234, 88, 12, 0.15);
    }
    .stat-card-teal {
        background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(2, 132, 199, 0.15);
    }
    .stat-icon-bg {
        position: absolute;
        bottom: -15px;
        right: -10px;
        opacity: 0.12;
        color: #ffffff;
        pointer-events: none;
    }

    /* Chart Cards */
    .chart-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        background: #ffffff;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    body.dark .chart-card {
        background: #1f2937;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        font-weight: 750;
        font-size: 0.95rem;
        color: #1f2937;
        padding: 20px 24px;
    }
    body.dark .chart-card .card-header {
        border-color: rgba(255,255,255,0.05);
        color: #f3f4f6;
    }

    /* Table container & custom styles */
    .table-container {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    body.dark .table-container {
        border-color: rgba(255, 255, 255, 0.05);
    }
    .table-analisis th {
        background: #f9fafb !important;
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #4b5563;
        border-bottom: 2px solid rgba(0,0,0,0.04) !important;
        padding: 16px 20px;
    }
    body.dark .table-analisis th {
        background: #111827 !important;
        color: #9ca3af;
        border-bottom-color: rgba(255,255,255,0.06) !important;
    }
    .table-analisis td {
        font-size: 0.85rem;
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    body.dark .table-analisis td {
        border-bottom-color: rgba(255,255,255,0.05);
    }
    .table-analisis tbody tr {
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .table-analisis tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.02) !important;
    }
    body.dark .table-analisis tbody tr:hover {
        background-color: rgba(129, 140, 248, 0.04) !important;
    }

    /* Modern glass badges */
    .badge-modern {
        font-size: 0.725rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        display: inline-block;
        letter-spacing: 0.3px;
    }
    .badge-modern-success {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
    body.dark .badge-modern-success {
        background-color: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.25);
    }
    .badge-modern-warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.15);
    }
    body.dark .badge-modern-warning {
        background-color: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    .badge-modern-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.15);
    }
    body.dark .badge-modern-danger {
        background-color: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.25);
    }
    .badge-modern-info {
        background-color: rgba(6, 182, 212, 0.1);
        color: #0891b2;
        border: 1px solid rgba(6, 182, 212, 0.15);
    }
    body.dark .badge-modern-info {
        background-color: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
        border-color: rgba(6, 182, 212, 0.25);
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

    /* Modal styling & dark mode overrides */
    body.dark .modal-content {
        background-color: #1f2937;
        color: #f3f4f6;
    }
    body.dark .modal-header, body.dark .modal-footer {
        background-color: #111827 !important;
        border-color: rgba(255, 255, 255, 0.05);
    }
    body.dark #modalPertanyaanBox, body.dark .modal-body .bg-light {
        background-color: #374151 !important;
        color: #f3f4f6;
    }
    body.dark .modal-title {
        color: #f3f4f6;
    }
    body.dark .btn-close {
        filter: invert(1);
    }

    /* Distribution progress bar */
    .dist-bar-item {
        background: #f3f4f6;
        border-radius: 12px;
        padding: 12px 16px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    body.dark .dist-bar-item {
        background: #374151;
        border-color: rgba(255, 255, 255, 0.05);
    }
    .dist-bar-progress {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        background: rgba(99, 102, 241, 0.08);
        transition: width 0.6s ease;
        z-index: 1;
    }
    body.dark .dist-bar-progress {
        background: rgba(129, 140, 248, 0.12);
    }
    .dist-bar-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: justify;
        align-items: center;
        width: 100%;
    }
    .dist-bar-item.is-correct {
        border-color: rgba(16, 185, 129, 0.25);
    }
    .dist-bar-item.is-correct .dist-bar-progress {
        background: rgba(16, 185, 129, 0.12);
    }
    .dist-label-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        background: #e5e7eb;
        color: #4b5563;
        margin-right: 12px;
        flex-shrink: 0;
    }
    body.dark .dist-label-badge {
        background: #4b5563;
        color: #f3f4f6;
    }
    .is-correct .dist-label-badge {
        background: #10b981;
        color: #ffffff;
    }
    body.dark .is-correct .dist-label-badge {
        background: #10b981;
        color: #ffffff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="spinner-dual mb-3"></div>
    <p class="text-muted fw-semibold" id="loadingText">Menghitung analisis butir soal...</p>
</div>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('panel/analisis-butir') ?>">Analisis Butir Soal</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('panel/analisis-butir/ujian/' . esc($bankSoal['id'], 'url')) ?>"><?= esc($bankSoal['nama']) ?></a></li>
            <li class="breadcrumb-item active"><?= esc($ujian['nama_ujian']) ?></li>
        </ol>
    </nav>

    <!-- Header Panel Control Bar -->
    <div class="control-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-feather="bar-chart-2" style="width: 24px; height: 24px; color: #4f46e5;"></i>
                <?= esc($ujian['nama_ujian']) ?>
            </h4>
            <p class="text-muted mb-0">Mata Pelajaran: <strong><?= esc($ujian['nama_mapel'] ?? $bankSoal['nama']) ?></strong></p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <select id="filterKelas" class="form-select select-modern" style="max-width:180px;">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelasList as $kelas): ?>
                    <option value="<?= esc($kelas) ?>"><?= esc($kelas) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="btnRefresh" class="btn btn-outline-modern" title="Segarkan Data">
                <i data-feather="refresh-cw" style="width: 15px; height: 15px;"></i>
            </button>
            <button id="btnExport" class="btn btn-gradient-success" title="Ekspor Data ke Excel">
                <i data-feather="download" style="width: 15px; height: 15px;"></i> Export Excel
            </button>
            <a href="<?= base_url('panel/analisis-butir/ujian/' . esc($bankSoal['id'], 'url')) ?>" class="btn btn-outline-modern">
                <i data-feather="arrow-left" style="width: 15px; height: 15px;"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4" id="summaryCards">
        <div class="col-6 col-lg-3 d-flex align-items-stretch">
            <div class="stat-card stat-card-blue w-100">
                <div class="stat-value" id="statPeserta">-</div>
                <div class="stat-label">Total Peserta</div>
                <div class="stat-icon-bg"><i data-feather="users" style="width: 80px; height: 80px;"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex align-items-stretch">
            <div class="stat-card stat-card-green w-100">
                <div class="stat-value" id="statRataRata">-</div>
                <div class="stat-label">Rata-rata Nilai</div>
                <div class="stat-icon-bg"><i data-feather="check-square" style="width: 80px; height: 80px;"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex align-items-stretch">
            <div class="stat-card stat-card-orange w-100">
                <div class="stat-value" id="statKR20">-</div>
                <div class="stat-label">Reliabilitas KR-20</div>
                <span class="stat-badge bg-white bg-opacity-25" id="statKR20Badge">-</span>
                <div class="stat-icon-bg"><i data-feather="activity" style="width: 80px; height: 80px;"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex align-items-stretch">
            <div class="stat-card stat-card-teal w-100">
                <div class="stat-value" id="statBerkualitas">-</div>
                <div class="stat-label">Soal Berkualitas</div>
                <div class="stat-icon-bg"><i data-feather="award" style="width: 80px; height: 80px;"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts Section 1 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header d-flex align-items-center">
                    <i data-feather="bar-chart" class="me-2" style="width: 18px; height: 18px; color: #3b82f6;"></i>
                    Tingkat Kesukaran (P) per Soal
                </div>
                <div class="card-body">
                    <div id="chartKesukaran" style="min-height:350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header d-flex align-items-center">
                    <i data-feather="pie-chart" class="me-2" style="width: 18px; height: 18px; color: #a855f7;"></i>
                    Distribusi Kualitas Soal
                </div>
                <div class="card-body">
                    <div id="chartDistribusi" style="min-height:350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section 2 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header d-flex align-items-center">
                    <i data-feather="trending-up" class="me-2" style="width: 18px; height: 18px; color: #10b981;"></i>
                    Daya Pembeda (D) per Soal
                </div>
                <div class="card-body">
                    <div id="chartDayaPembeda" style="min-height:350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header d-flex align-items-center">
                    <i data-feather="activity" class="me-2" style="width: 18px; height: 18px; color: #ea580c;"></i>
                    Distribusi Daya Pembeda
                </div>
                <div class="card-body">
                    <div id="chartDayaPembedaDist" style="min-height:350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table detail list -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i data-feather="list" class="me-2" style="width: 18px; height: 18px; color: #4f46e5;"></i>
                        <span>Tabel Analisis Detail Butir Soal</span>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-bold" id="totalSoalBadge">0 soal</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive table-container">
                        <table class="table table-analisis table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">No</th>
                                    <th style="width:70px;">Jenis</th>
                                    <th>Teks Pertanyaan (Klik baris untuk detail)</th>
                                    <th class="text-center" style="width:80px;">P</th>
                                    <th class="text-center" style="width:100px;">Klsf. P</th>
                                    <th class="text-center" style="width:80px;">D</th>
                                    <th class="text-center" style="width:120px;">Klsf. D</th>
                                    <th class="text-center" style="width:100px;">Pengecoh</th>
                                    <th class="text-center" style="width:110px;">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelAnalisis">
                                <tr><td colspan="9" class="text-center text-muted py-5">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Soal -->
<div class="modal fade" id="modalDetailSoal" tabindex="-1" aria-labelledby="modalDetailSoalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalDetailSoalLabel">Detail Analisis Butir Soal</h5>
                    <span class="badge bg-primary-subtle text-primary mt-1" id="modalDetailNoSoal">Soal 0</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Pertanyaan -->
                <div class="mb-4">
                    <h6 class="fw-bold text-muted small uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.725rem;">Pertanyaan Soal</h6>
                    <div id="modalPertanyaanBox" class="p-3 bg-light rounded-3 border-0" style="max-height: 250px; overflow-y: auto;">
                        <!-- HTML Pertanyaan -->
                    </div>
                </div>

                <!-- Hasil Analisis Soal -->
                <div class="row g-2 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Kesukaran (P)</div>
                            <div class="fw-extrabold fs-5 text-dark" id="modalPVal">-</div>
                            <span class="badge rounded-pill mt-1" id="modalPBadge">-</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Daya Beda (D)</div>
                            <div class="fw-extrabold fs-5 text-dark" id="modalDVal">-</div>
                            <span class="badge rounded-pill mt-1" id="modalDBadge">-</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Pengecoh</div>
                            <div class="fw-extrabold fs-5 text-dark" id="modalPengecohVal">-</div>
                            <span class="badge rounded-pill mt-1" id="modalPengecohBadge">-</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Rekomendasi</div>
                            <div class="fw-extrabold fs-5 text-dark" id="modalStatusVal">-</div>
                            <span class="badge rounded-pill mt-1" id="modalStatusBadge">-</span>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Opsi Jawaban (PG) -->
                <div id="modalDistribusiContainer">
                    <h6 class="fw-bold text-muted small uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.725rem;">Distribusi Jawaban Peserta</h6>
                    <div id="modalDistribusiOpsiList" class="d-flex flex-column gap-2">
                        <!-- Opsi akan dimasukkan di sini secara dinamis -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pagejs') ?>
<script>
$(document).ready(function() {
    const ujianId = '<?= esc($ujianId, 'js') ?>';
    let chartKesukaran, chartDistribusi, chartDayaPembeda, chartDayaPembedaDist;
    let globalAnalisisData = [];

    // Helper functions for class badges
    function getPBadgeClass(klasifikasi) {
        switch (klasifikasi) {
            case 'Sedang': return 'badge-modern-success';
            case 'Mudah': return 'badge-modern-warning';
            case 'Sukar': return 'badge-modern-danger';
            default: return 'bg-secondary-subtle text-secondary';
        }
    }

    function getDBadgeClass(klasifikasi) {
        switch (klasifikasi) {
            case 'Sangat Baik': return 'badge-modern-success';
            case 'Baik': return 'badge-modern-info';
            case 'Cukup': return 'badge-modern-warning';
            case 'Jelek': return 'badge-modern-danger';
            case 'Negatif': return 'bg-danger text-white';
            default: return 'bg-secondary-subtle text-secondary';
        }
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Diterima': return 'badge-modern-success';
            case 'Perlu Revisi': return 'badge-modern-warning';
            case 'Dibuang': return 'badge-modern-danger';
            default: return 'bg-secondary-subtle text-secondary';
        }
    }

    function loadAnalisis() {
        const kelas = $('#filterKelas').val();
        let url = "<?= base_url('panel/analisis-butir/api/') ?>" + ujianId;
        if (kelas) url += '?kelas=' + encodeURIComponent(kelas);

        $('#loadingOverlay').show();

        $.getJSON(url, function(res) {
            $('#loadingOverlay').hide();

            if (!res.status || !res.data) {
                Snackbar.show({ text: res.message || 'Gagal memuat data.', pos: 'top-center', backgroundColor: '#e7515a' });
                return;
            }

            const data = res.data;
            globalAnalisisData = data.per_soal || [];
            
            renderSummary(data.ringkasan);
            renderCharts(data.per_soal, data.ringkasan);
            renderTable(data.per_soal);

        }).fail(function() {
            $('#loadingOverlay').hide();
            Snackbar.show({ text: 'Terjadi kesalahan server saat memuat data.', pos: 'top-center', backgroundColor: '#e7515a' });
        });
    }

    function renderSummary(r) {
        document.getElementById('statPeserta').textContent = r.total_peserta;
        document.getElementById('statRataRata').textContent = r.rata_rata;
        document.getElementById('statKR20').textContent = r.kr20 !== null ? r.kr20.toFixed(2) : 'N/A';
        
        const badge = document.getElementById('statKR20Badge');
        badge.textContent = r.klasifikasi_kr20 || '-';
        if (r.kr20 >= 0.60) {
            badge.className = 'stat-badge bg-white bg-opacity-20 text-white';
        } else {
            badge.className = 'stat-badge bg-danger bg-opacity-40 text-white';
        }
        
        document.getElementById('statBerkualitas').textContent = r.soal_berkualitas + '/' + r.total_soal;
    }

    function renderCharts(perSoal, ringkasan) {
        if (!perSoal || perSoal.length === 0) return;

        const isDark = document.body.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.04)';
        const textColor = isDark ? '#9ca3af' : '#4b5563';

        const labels = perSoal.map(function(s) { return 'Soal ' + s.soal_no; });
        const pValues = perSoal.map(function(s) { return s.p; });
        const dValues = perSoal.map(function(s) { return s.d !== null ? s.d : 0; });

        // Colors based on P classification
        const pColors = pValues.map(function(p) {
            if (p > 0.70) return '#fbbf24'; // Mudah (Yellow)
            if (p >= 0.30) return '#10b981'; // Sedang (Green)
            return '#f87171'; // Sukar (Red)
        });

        // Colors based on D classification
        const dColors = dValues.map(function(d) {
            if (d >= 0.40) return '#10b981'; // Sangat Baik
            if (d >= 0.30) return '#22d3ee'; // Baik
            if (d >= 0.20) return '#fbbf24'; // Cukup
            if (d >= 0.00) return '#f97316'; // Jelek
            return '#ef4444'; // Negatif
        });

        // Chart 1: Tingkat Kesukaran
        if (chartKesukaran) chartKesukaran.destroy();
        chartKesukaran = new ApexCharts(document.querySelector('#chartKesukaran'), {
            chart: { 
                type: 'bar', 
                height: 350, 
                toolbar: { show: true },
                background: 'transparent'
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            series: [{ name: 'Tingkat Kesukaran (P)', data: pValues }],
            xaxis: { 
                categories: labels, 
                labels: { style: { fontSize: '11px', colors: textColor } } 
            },
            yaxis: { 
                min: 0, 
                max: 1, 
                title: { text: 'Nilai P', style: { color: textColor } },
                labels: { style: { colors: textColor } }
            },
            colors: pColors,
            plotOptions: {
                bar: {
                    distributed: true,
                    borderRadius: 6,
                    columnWidth: '55%',
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            legend: { show: false },
            annotations: {
                yaxis: [
                    { y: 0.30, borderColor: '#ef4444', strokeDashArray: 4, label: { text: 'Batas Sukar (0.30)', style: { color: '#ef4444', fontSize: '10px' } } },
                    { y: 0.70, borderColor: '#fbbf24', strokeDashArray: 4, label: { text: 'Batas Mudah (0.70)', style: { color: '#d97706', fontSize: '10px' } } },
                ]
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(4); } }
            }
        });
        chartKesukaran.render();

        // Chart 2: Distribusi Kualitas
        let cDiterima = 0, cRevisi = 0, cDibuang = 0;
        perSoal.forEach(function(s) {
            if (s.status === 'Diterima') cDiterima++;
            else if (s.status === 'Perlu Revisi') cRevisi++;
            else cDibuang++;
        });

        if (chartDistribusi) chartDistribusi.destroy();
        chartDistribusi = new ApexCharts(document.querySelector('#chartDistribusi'), {
            chart: { 
                type: 'donut', 
                height: 350,
                background: 'transparent'
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            series: [cDiterima, cRevisi, cDibuang],
            labels: ['Diterima', 'Perlu Revisi', 'Dibuang'],
            colors: ['#10b981', '#fbbf24', '#f87171'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Soal',
                                color: textColor,
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce(function(a, b) { return a + b; }, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: { 
                position: 'bottom',
                labels: { colors: textColor }
            },
        });
        chartDistribusi.render();

        // Chart 3: Daya Pembeda
        if (chartDayaPembeda) chartDayaPembeda.destroy();
        chartDayaPembeda = new ApexCharts(document.querySelector('#chartDayaPembeda'), {
            chart: { 
                type: 'bar', 
                height: 350, 
                toolbar: { show: true },
                background: 'transparent'
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            series: [{ name: 'Daya Pembeda (D)', data: dValues }],
            xaxis: { 
                categories: labels, 
                labels: { style: { fontSize: '11px', colors: textColor } } 
            },
            yaxis: { 
                min: -1, 
                max: 1, 
                title: { text: 'Nilai D', style: { color: textColor } },
                labels: { style: { colors: textColor } }
            },
            colors: dColors,
            plotOptions: {
                bar: {
                    distributed: true,
                    borderRadius: 6,
                    columnWidth: '55%',
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            legend: { show: false },
            annotations: {
                yaxis: [
                    { y: 0.30, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Batas Baik (0.30)', style: { color: '#10b981', fontSize: '10px' } } },
                    { y: 0, borderColor: '#ef4444', strokeDashArray: 2, label: { text: 'Batas Kritis (0.00)', style: { color: '#ef4444', fontSize: '10px' } } },
                ]
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(4); } }
            }
        });
        chartDayaPembeda.render();

        // Chart 4: Distribusi Daya Pembeda
        let dSangatBaik = 0, dBaik = 0, dCukup = 0, dJelek = 0, dNegatif = 0;
        perSoal.forEach(function(s) {
            const d = s.d;
            if (d === null) return;
            if (d >= 0.40) dSangatBaik++;
            else if (d >= 0.30) dBaik++;
            else if (d >= 0.20) dCukup++;
            else if (d >= 0) dJelek++;
            else dNegatif++;
        });

        if (chartDayaPembedaDist) chartDayaPembedaDist.destroy();
        chartDayaPembedaDist = new ApexCharts(document.querySelector('#chartDayaPembedaDist'), {
            chart: { 
                type: 'donut', 
                height: 350,
                background: 'transparent'
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            series: [dSangatBaik, dBaik, dCukup, dJelek, dNegatif],
            labels: ['Sangat Baik', 'Baik', 'Cukup', 'Jelek', 'Negatif'],
            colors: ['#10b981', '#22d3ee', '#fbbf24', '#f97316', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Soal Teranalisis',
                                color: textColor,
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce(function(a, b) { return a + b; }, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: { 
                position: 'bottom',
                labels: { colors: textColor }
            },
        });
        chartDayaPembedaDist.render();
    }

    function renderTable(perSoal) {
        const tbody = document.getElementById('tabelAnalisis');
        tbody.replaceChildren();

        document.getElementById('totalSoalBadge').textContent = perSoal.length + ' soal';

        if (perSoal.length === 0) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 9;
            td.className = 'text-center text-muted py-5';
            td.textContent = 'Tidak ada data analisis untuk ditampilkan.';
            tr.appendChild(td);
            tbody.appendChild(tr);
            return;
        }

        perSoal.forEach(function(s, idx) {
            const tr = document.createElement('tr');
            tr.setAttribute('data-index', idx);

            // No
            const tdNo = document.createElement('td');
            tdNo.className = 'text-center fw-semibold text-dark-light';
            tdNo.textContent = s.soal_no;
            tr.appendChild(tdNo);

            // Jenis
            const tdJenis = document.createElement('td');
            const jenisBadge = document.createElement('span');
            jenisBadge.className = 'glass-badge glass-badge-primary';
            jenisBadge.textContent = s.jenis_soal.toUpperCase();
            tdJenis.appendChild(jenisBadge);
            tr.appendChild(tdJenis);

            // Pertanyaan
            const tdPertanyaan = document.createElement('td');
            tdPertanyaan.className = 'text-dark-light fw-medium';
            tdPertanyaan.textContent = s.pertanyaan;
            tdPertanyaan.style.maxWidth = '300px';
            tdPertanyaan.style.overflow = 'hidden';
            tdPertanyaan.style.textOverflow = 'ellipsis';
            tdPertanyaan.style.whiteSpace = 'nowrap';
            tr.appendChild(tdPertanyaan);

            // P
            const tdP = document.createElement('td');
            tdP.className = 'text-center fw-bold text-dark';
            tdP.textContent = s.p.toFixed(2);
            tr.appendChild(tdP);

            // Klasifikasi P
            const tdKlasP = document.createElement('td');
            tdKlasP.className = 'text-center';
            const badgeP = document.createElement('span');
            badgeP.className = 'badge-modern ' + getPBadgeClass(s.klasifikasi_p);
            badgeP.textContent = s.klasifikasi_p;
            tdKlasP.appendChild(badgeP);
            tr.appendChild(tdKlasP);

            // D
            const tdD = document.createElement('td');
            tdD.className = 'text-center fw-bold text-dark';
            tdD.textContent = s.d !== null ? s.d.toFixed(2) : 'N/A';
            tr.appendChild(tdD);

            // Klasifikasi D
            const tdKlasD = document.createElement('td');
            tdKlasD.className = 'text-center';
            const badgeD = document.createElement('span');
            badgeD.className = 'badge-modern ' + getDBadgeClass(s.klasifikasi_d);
            badgeD.textContent = s.klasifikasi_d;
            tdKlasD.appendChild(badgeD);
            tr.appendChild(tdKlasD);

            // Pengecoh
            const tdPengecoh = document.createElement('td');
            tdPengecoh.className = 'text-center fw-semibold text-dark-light';
            if (s.pengecoh_efektif !== null) {
                tdPengecoh.textContent = s.pengecoh_efektif + '/' + s.total_pengecoh;
            } else {
                tdPengecoh.textContent = 'N/A';
                tdPengecoh.classList.add('text-muted');
            }
            tr.appendChild(tdPengecoh);

            // Status
            const tdStatus = document.createElement('td');
            tdStatus.className = 'text-center';
            const badgeStatus = document.createElement('span');
            badgeStatus.className = 'badge-modern ' + getStatusBadgeClass(s.status);
            badgeStatus.textContent = s.status;
            tdStatus.appendChild(badgeStatus);
            tr.appendChild(tdStatus);

            tbody.appendChild(tr);
        });
    }

    // Modal Details on Row Click
    $('#tabelAnalisis').on('click', 'tr', function() {
        const index = $(this).attr('data-index');
        if (index === undefined) return;
        const item = globalAnalisisData[index];
        showModalDetail(item);
    });

    function showModalDetail(item) {
        document.getElementById('modalDetailNoSoal').textContent = 'SOAL NOMOR ' + item.soal_no;
        
        // Render HTML Pertanyaan
        const qBox = document.getElementById('modalPertanyaanBox');
        qBox.innerHTML = item.pertanyaan_html || item.pertanyaan;

        // Stats Values and Badges
        document.getElementById('modalPVal').textContent = item.p.toFixed(2);
        const pBadge = document.getElementById('modalPBadge');
        pBadge.textContent = item.klasifikasi_p;
        pBadge.className = 'badge rounded-pill ' + getPBadgeClass(item.klasifikasi_p);

        document.getElementById('modalDVal').textContent = item.d !== null ? item.d.toFixed(2) : 'N/A';
        const dBadge = document.getElementById('modalDBadge');
        dBadge.textContent = item.klasifikasi_d;
        dBadge.className = 'badge rounded-pill ' + getDBadgeClass(item.klasifikasi_d);

        let pengecohStr = 'N/A';
        let pengecohBadgeClass = 'bg-secondary-subtle text-secondary';
        let pengecohClassText = 'N/A';
        if (item.pengecoh_efektif !== null) {
            pengecohStr = item.pengecoh_efektif + '/' + item.total_pengecoh;
            if (item.pengecoh_efektif === item.total_pengecoh) {
                pengecohBadgeClass = 'bg-success-subtle text-success';
                pengecohClassText = 'Semua Efektif';
            } else if (item.pengecoh_efektif > 0) {
                pengecohBadgeClass = 'bg-warning-subtle text-warning';
                pengecohClassText = 'Sebagian Efektif';
            } else {
                pengecohBadgeClass = 'bg-danger-subtle text-danger';
                pengecohClassText = 'Tidak Efektif';
            }
        }
        document.getElementById('modalPengecohVal').textContent = pengecohStr;
        const pcoBadge = document.getElementById('modalPengecohBadge');
        pcoBadge.textContent = pengecohClassText;
        pcoBadge.className = 'badge rounded-pill ' + pengecohBadgeClass;

        const statusBadge = document.getElementById('modalStatusBadge');
        document.getElementById('modalStatusVal').textContent = item.status;
        statusBadge.textContent = item.status;
        statusBadge.className = 'badge rounded-pill ' + getStatusBadgeClass(item.status);

        // Render Opsi List
        const distContainer = document.getElementById('modalDistribusiContainer');
        const distList = document.getElementById('modalDistribusiOpsiList');
        distList.replaceChildren();

        if (item.jenis_soal === 'pg' && item.distribusi && item.distribusi.length > 0) {
            distContainer.style.display = 'block';

            item.distribusi.forEach(function(dist) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'dist-bar-item';
                if (dist.is_true) itemDiv.classList.add('is-correct');

                const progress = document.createElement('div');
                progress.className = 'dist-bar-progress';
                progress.style.width = dist.persen + '%';
                itemDiv.appendChild(progress);

                const content = document.createElement('div');
                content.className = 'dist-bar-content';

                const left = document.createElement('div');
                left.className = 'd-flex align-items-center';

                const badge = document.createElement('div');
                badge.className = 'dist-label-badge';
                badge.textContent = dist.label;
                left.appendChild(badge);

                const textSpan = document.createElement('span');
                textSpan.className = 'small fw-medium text-dark-light text-wrap';
                
                // Strip HTML tag for preview text inside options
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = dist.teks;
                textSpan.textContent = tempDiv.textContent || tempDiv.innerText || '';
                left.appendChild(textSpan);

                const right = document.createElement('div');
                right.className = 'ms-auto fw-bold text-dark d-flex align-items-center gap-2';
                right.style.fontSize = '0.825rem';
                
                let checkMark = '';
                if (dist.is_true) {
                    checkMark = '<span class="badge bg-success-subtle text-success small rounded-pill px-2 py-1" style="font-size:0.55rem; letter-spacing: 0.3px;">KUNCI</span>';
                }
                right.innerHTML = dist.count + ' siswa (' + dist.persen.toFixed(1) + '%) ' + checkMark;

                content.appendChild(left);
                content.appendChild(right);
                itemDiv.appendChild(content);
                distList.appendChild(itemDiv);
            });

            // Handle non-answering students
            if (item.tidak_menjawab > 0) {
                const tmDiv = document.createElement('div');
                tmDiv.className = 'dist-bar-item';

                const progress = document.createElement('div');
                progress.className = 'dist-bar-progress';
                const totalP = item.tidak_menjawab + item.distribusi.reduce((acc, curr) => acc + curr.count, 0);
                const tmPersen = totalP > 0 ? (item.tidak_menjawab / totalP) * 100 : 0;
                progress.style.width = tmPersen + '%';
                tmDiv.appendChild(progress);

                const content = document.createElement('div');
                content.className = 'dist-bar-content';

                const left = document.createElement('div');
                left.className = 'd-flex align-items-center';

                const badge = document.createElement('div');
                badge.className = 'dist-label-badge';
                badge.textContent = '-';
                left.appendChild(badge);

                const textSpan = document.createElement('span');
                textSpan.className = 'small fw-medium text-muted fst-italic';
                textSpan.textContent = 'Tidak Menjawab / Kosong';
                left.appendChild(textSpan);

                const right = document.createElement('div');
                right.className = 'ms-auto fw-bold text-dark';
                right.style.fontSize = '0.825rem';
                right.textContent = item.tidak_menjawab + ' siswa (' + tmPersen.toFixed(1) + '%)';

                content.appendChild(left);
                content.appendChild(right);
                tmDiv.appendChild(content);
                distList.appendChild(tmDiv);
            }
        } else {
            distContainer.style.display = 'none';
        }

        const myModal = new bootstrap.Modal(document.getElementById('modalDetailSoal'));
        myModal.show();
    }

    // Event listeners
    $('#filterKelas').on('change', function() {
        loadAnalisis();
    });

    $('#btnRefresh').on('click', function() {
        loadAnalisis();
    });

    $('#btnExport').on('click', function() {
        const kelas = $('#filterKelas').val();
        let url = "<?= base_url('panel/analisis-butir/export/') ?>" + ujianId;
        if (kelas) url += '?kelas=' + encodeURIComponent(kelas);
        window.location.href = url;
    });

    // Run first load
    loadAnalisis();
    feather.replace();
});
</script>
<?= $this->endSection() ?>
