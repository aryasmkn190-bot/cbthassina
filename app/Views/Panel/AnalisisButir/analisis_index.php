<?= $this->extend('Layout/main') ?>

<?= $this->section('css') ?>
<style>
    /* Force main content container to display as block instead of flex row */
    #content > .container {
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
    
    /* Refresh button */
    .btn-action-refresh {
        border-radius: 50%;
        width: 38px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        background: #ffffff;
        color: #4b5563;
        transition: all 0.3s ease;
    }
    body.dark .btn-action-refresh {
        background: #1f2937;
        border-color: rgba(255, 255, 255, 0.1);
        color: #d1d5db;
    }
    .btn-action-refresh:hover {
        background: #f3f4f6;
        color: #4f46e5;
        border-color: rgba(79, 70, 229, 0.3);
        transform: rotate(30deg);
    }
    body.dark .btn-action-refresh:hover {
        background: #374151;
        color: #818cf8;
    }
    
    /* Bank Soal Cards */
    .bank-soal-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border-left: 4px solid transparent;
        cursor: pointer;
    }
    body.dark .bank-soal-card {
        background: #1f2937;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
    }
    .bank-soal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.10);
    }
    body.dark .bank-soal-card:hover {
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.20);
    }
    
    /* Left accent borders with gradients */
    .accent-blue { border-left-color: #4f46e5; }
    .accent-purple { border-left-color: #9333ea; }
    .accent-pink { border-left-color: #db2777; }
    .accent-teal { border-left-color: #0d9488; }
    .accent-emerald { border-left-color: #059669; }
    
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
    
    /* Stats Grid */
    .card-stat-item {
        background: #f9fafb;
        border-radius: 10px;
        padding: 6px 8px;
        flex: 1;
        text-align: center;
        transition: background 0.3s ease;
    }
    body.dark .card-stat-item {
        background: #374151;
    }
    .bank-soal-card:hover .card-stat-item {
        background: rgba(99, 102, 241, 0.05);
    }
    body.dark .bank-soal-card:hover .card-stat-item {
        background: rgba(129, 140, 248, 0.08);
    }
    .card-stat-value {
        font-weight: 800;
        font-size: 1rem;
        color: #1f2937;
        line-height: 1.1;
    }
    body.dark .card-stat-value {
        color: #f3f4f6;
    }
    .card-stat-label {
        font-size: 0.65rem;
        color: #6b7280;
        margin-top: 1px;
    }
    body.dark .card-stat-label {
        color: #9ca3af;
    }
    
    /* Card Action Arrow */
    .card-arrow-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        transition: all 0.3s ease;
    }
    body.dark .card-arrow-btn {
        background: #374151;
        color: #818cf8;
    }
    .bank-soal-card:hover .card-arrow-btn {
        background: #4f46e5;
        color: #ffffff;
        transform: translateX(4px);
    }
    body.dark .bank-soal-card:hover .card-arrow-btn {
        background: #818cf8;
        color: #111827;
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
        .bank-soal-card {
            border-radius: 14px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <!-- Hero panel header -->
    <div class="hero-panel">
        <div class="hero-pattern"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-feather="bar-chart-2" style="width: 24px; height: 24px; color: #4f46e5;"></i>
                    Analisis Butir Soal
                </h4>
                <p class="text-muted mb-0 small">Pilih bank soal di bawah ini untuk menganalisis kualitas butir-butir soal yang diujikan.</p>
            </div>
            <div class="d-flex gap-2 align-items-center w-100-mobile">
                <div class="search-wrapper flex-grow-1 flex-sm-grow-0">
                    <i data-feather="search" class="search-icon"></i>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari bank soal...">
                </div>
                <button id="refreshBtn" class="btn btn-action-refresh flex-shrink-0" title="Segarkan Data">
                    <i data-feather="refresh-cw" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Cards container -->
    <div id="bankSoalList" class="row g-3 mb-5">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
            <p class="text-muted mt-3 fw-medium">Memuat data bank soal...</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pagejs') ?>
<script>
$(document).ready(function() {
    let allData = [];
    const accents = ['accent-blue', 'accent-purple', 'accent-pink', 'accent-teal', 'accent-emerald'];

    function loadData() {
        $('#bankSoalList').html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                <p class="text-muted mt-3 fw-medium">Memuat data bank soal...</p>
            </div>
        `);

        $.getJSON("<?= base_url('panel/banksoal/list') ?>", function(res) {
            if (res.status && res.data) {
                allData = res.data;
                renderList(allData);
            } else {
                $('#bankSoalList').html('<div class="col-12 text-center text-muted py-5"><i data-feather="inbox" class="mb-2" style="width:48px;height:48px;"></i><p>Tidak ada data bank soal.</p></div>');
                feather.replace();
            }
        }).fail(function() {
            $('#bankSoalList').html('<div class="col-12"><div class="alert alert-danger shadow-sm border-0 rounded-3">Gagal memuat data bank soal. Hubungi administrator.</div></div>');
        });
    }

    function renderList(data) {
        const container = document.getElementById('bankSoalList');
        container.replaceChildren();

        if (data.length === 0) {
            const emptyCol = document.createElement('div');
            emptyCol.className = 'col-12 text-center text-muted py-5';
            
            const emptyIcon = document.createElement('div');
            emptyIcon.className = 'mb-2';
            emptyIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>';
            
            const emptyText = document.createElement('p');
            emptyText.className = 'fw-medium mb-0';
            emptyText.textContent = 'Tidak ada bank soal yang ditemukan.';
            
            emptyCol.appendChild(emptyIcon);
            emptyCol.appendChild(emptyText);
            container.appendChild(emptyCol);
            return;
        }

        data.forEach(function(item, idx) {
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch';

            const card = document.createElement('div');
            const accent = accents[idx % accents.length];
            card.className = `card bank-soal-card ${accent} w-100`;

            card.addEventListener('click', function() {
                window.location.href = "<?= base_url('panel/analisis-butir/ujian/') ?>" + item.id;
            });

            const cardBody = document.createElement('div');
            cardBody.className = 'card-body p-3 d-flex flex-column justify-content-between';

            // Top Section (Header & Info)
            const topSec = document.createElement('div');

            const header = document.createElement('div');
            header.className = 'd-flex justify-content-between align-items-start mb-2';

            const titleDiv = document.createElement('div');
            titleDiv.className = 'pe-2';

            const kodeBadge = document.createElement('span');
            kodeBadge.className = 'glass-badge glass-badge-primary mb-2';
            kodeBadge.textContent = item.kode;
            titleDiv.appendChild(kodeBadge);

            const title = document.createElement('h6');
            title.className = 'fw-bold text-dark mb-0 lh-sm fs-6';
            title.textContent = item.nama;
            titleDiv.appendChild(title);

            const arrowBtn = document.createElement('div');
            arrowBtn.className = 'card-arrow-btn flex-shrink-0';
            arrowBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';

            header.appendChild(titleDiv);
            header.appendChild(arrowBtn);
            topSec.appendChild(header);

            // Description
            const desc = document.createElement('p');
            desc.className = 'small text-muted mb-2 lh-sm';
            if (item.deskripsi) {
                desc.textContent = item.deskripsi.substring(0, 60) + (item.deskripsi.length > 60 ? '...' : '');
            } else {
                desc.textContent = 'Tidak ada deskripsi untuk bank soal ini.';
                desc.className += ' fst-italic opacity-75';
            }
            topSec.appendChild(desc);
            cardBody.appendChild(topSec);

            // Bottom Section (Stats)
            const bottomSec = document.createElement('div');
            bottomSec.className = 'mt-3';

            const statsDiv = document.createElement('div');
            statsDiv.className = 'd-flex gap-2';

            // Stat 1: Total Soal
            const statSoal = document.createElement('div');
            statSoal.className = 'card-stat-item';
            
            const valSoal = document.createElement('div');
            valSoal.className = 'card-stat-value';
            valSoal.textContent = item.jumlah_total_soal || 0;
            
            const labelSoal = document.createElement('div');
            labelSoal.className = 'card-stat-label';
            labelSoal.textContent = 'Total Soal';
            
            statSoal.appendChild(valSoal);
            statSoal.appendChild(labelSoal);

            // Stat 2: PG Soal
            const statPg = document.createElement('div');
            statPg.className = 'card-stat-item';
            
            const valPg = document.createElement('div');
            valPg.className = 'card-stat-value';
            valPg.textContent = item.jumlah_pg || 0;
            
            const labelPg = document.createElement('div');
            labelPg.className = 'card-stat-label';
            labelPg.textContent = 'Pilihan Ganda';
            
            statPg.appendChild(valPg);
            statPg.appendChild(labelPg);

            statsDiv.appendChild(statSoal);
            statsDiv.appendChild(statPg);
            bottomSec.appendChild(statsDiv);

            cardBody.appendChild(bottomSec);
            card.appendChild(cardBody);
            col.appendChild(card);
            container.appendChild(col);
        });

        // Trigger feather icon replacement inside container
        feather.replace();
    }

    // Search logic
    $('#searchBox').on('input', function() {
        const query = this.value.toLowerCase();
        const filtered = allData.filter(function(item) {
            return item.nama.toLowerCase().indexOf(query) !== -1 ||
                   item.kode.toLowerCase().indexOf(query) !== -1;
        });
        renderList(filtered);
    });

    $('#refreshBtn').on('click', function() {
        loadData();
    });

    // Initial load
    loadData();
    feather.replace();
});
</script>
<?= $this->endSection() ?>
