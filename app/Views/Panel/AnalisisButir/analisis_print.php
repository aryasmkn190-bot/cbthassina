<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?> - <?= esc($ujian['nama_ujian']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stylesheets -->
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/apex/apexcharts.css" rel="stylesheet" type="text/css">
    
    <!-- Google Fonts -->
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            font-style: normal;
        }
        
        body {
            font-family: 'Nunito', 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 20px 0;
            font-size: 14px;
        }

        /* Glassmorphic Loading Screen */
        #loadingOverlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
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

        /* Report Container styling */
        .report-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        /* Kop Surat (School Header) */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #1f2937;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 20px;
        }
        .kop-text h2 {
            font-weight: 850;
            font-size: 1.5rem;
            margin: 0 0 4px 0;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            font-weight: 700;
            font-size: 1.15rem;
            margin: 0 0 4px 0;
            color: #374151;
        }
        .kop-text p {
            margin: 0;
            font-size: 0.85rem;
            color: #6b7280;
        }

        /* Title Area */
        .report-title {
            text-align: center;
            font-weight: 800;
            font-size: 1.3rem;
            color: #111827;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .info-table td.label-info {
            font-weight: 700;
            color: #4b5563;
            width: 180px;
        }
        .info-table td.colon-info {
            width: 10px;
        }

        /* Summary Cards Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            background-color: #f9fafb;
        }
        .summary-val {
            font-size: 1.6rem;
            font-weight: 850;
            color: #111827;
            line-height: 1.1;
        }
        .summary-lbl {
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
        .summary-sub {
            font-size: 0.75rem;
            font-weight: 700;
            color: #4f46e5;
            margin-top: 2px;
        }

        /* Section Title */
        .section-title {
            font-weight: 800;
            font-size: 1rem;
            color: #1f2937;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 6px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Chart Cards */
        .chart-row {
            margin-bottom: 35px;
        }
        .chart-box {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            background-color: #ffffff;
            height: 310px;
        }
        .chart-box-header {
            font-weight: 750;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 6px;
        }

        /* Badges styles */
        .badge-modern {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 30px;
            display: inline-block;
            letter-spacing: 0.3px;
        }
        .badge-modern-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }
        .badge-modern-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.15);
        }
        .badge-modern-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }
        .badge-modern-info {
            background-color: rgba(6, 182, 212, 0.1);
            color: #0891b2;
            border: 1px solid rgba(6, 182, 212, 0.15);
        }

        /* Table styles */
        .table-print {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            margin-top: 10px;
        }
        .table-print th {
            background-color: #f3f4f6 !important;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            border: 1.5px solid #d1d5db;
            padding: 8px 10px;
            text-align: center;
        }
        .table-print td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            vertical-align: middle;
        }
        .table-print tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Print Media Queries */
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .report-container {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .print-page {
                page-break-after: always;
                page-break-inside: avoid;
            }
            .print-page:last-child {
                page-break-after: avoid;
            }
            .chart-box {
                border: 1px solid #d1d5db !important;
            }
            .table-print th {
                border: 1px solid #9ca3af !important;
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table-print td {
                border: 1px solid #d1d5db !important;
            }
            tr {
                page-break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 12mm 15mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Loading Screen -->
    <div id="loadingOverlay">
        <div class="spinner-dual mb-3"></div>
        <p class="text-muted fw-semibold">Menyiapkan Laporan PDF...</p>
    </div>

    <!-- Floating Actions for PDF manual trigger -->
    <div class="no-print d-flex justify-content-center gap-3 mb-4">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow">
            Cetak Laporan / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-4">
            Tutup Halaman
        </button>
    </div>

    <div class="report-container">
        
        <!-- ================= PAGE 1 ================= -->
        <div class="print-page">
            <!-- Kop Surat -->
            <div class="kop-surat">
                <?php if (!empty($setting->logo)): ?>
                    <img class="kop-logo" src="<?= base_url('assets/img/' . $setting->logo) ?>" alt="Logo Sekolah">
                <?php endif; ?>
                <div class="kop-text">
                    <h2><?= esc($setting->nama_sekolah) ?></h2>
                    <h3>Laporan Hasil Analisis Butir Soal</h3>
                    <p>Alamat: <?= esc($setting->alamat ?? '-') ?> | Telp: <?= esc($setting->telp ?? '-') ?></p>
                </div>
            </div>

            <div class="report-title">
                Analisis Butir Soal Detail
            </div>

            <!-- Info Metadata -->
            <table class="info-table">
                <tr>
                    <td class="label-info">Nama Ujian</td>
                    <td class="colon-info">:</td>
                    <td><strong><?= esc($ujian['nama_ujian']) ?></strong></td>
                    
                    <td class="label-info" style="padding-left: 30px;">Tanggal Cetak</td>
                    <td class="colon-info">:</td>
                    <td><?= date('d-m-Y H:i') ?></td>
                </tr>
                <tr>
                    <td class="label-info">Mata Pelajaran</td>
                    <td class="colon-info">:</td>
                    <td><?= esc($ujian['nama_mapel'] ?? $bankSoal['nama']) ?></td>
                    
                    <td class="label-info" style="padding-left: 30px;">Filter Kelas</td>
                    <td class="colon-info">:</td>
                    <td><?= esc($filterKelas ?: 'Semua Kelas') ?></td>
                </tr>
            </table>

            <!-- Summary Info Grid -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-val" id="sumPeserta">-</div>
                    <div class="summary-lbl">Total Peserta</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val" id="sumRataRata">-</div>
                    <div class="summary-lbl">Rata-rata Nilai</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val" id="sumKR20">-</div>
                    <div class="summary-lbl">Reliabilitas KR-20</div>
                    <div class="summary-sub" id="sumKR20Badge">-</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val" id="sumBerkualitas">-</div>
                    <div class="summary-lbl">Soal Berkualitas</div>
                </div>
            </div>

            <!-- Charts Section 1 -->
            <div class="section-title">I. Tingkat Kesukaran & Kualitas</div>
            <div class="row chart-row g-3">
                <div class="col-8">
                    <div class="chart-box">
                        <div class="chart-box-header">Grafik Tingkat Kesukaran (P) per Soal</div>
                        <div id="chartKesukaran"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="chart-box">
                        <div class="chart-box-header">Distribusi Kualitas</div>
                        <div id="chartDistribusi"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 2 ================= -->
        <div class="print-page">
            <div class="section-title">II. Daya Pembeda Soal</div>
            <div class="row chart-row g-3">
                <div class="col-8">
                    <div class="chart-box">
                        <div class="chart-box-header">Grafik Daya Pembeda (D) per Soal</div>
                        <div id="chartDayaPembeda"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="chart-box">
                        <div class="chart-box-header">Distribusi Daya Pembeda</div>
                        <div id="chartDayaPembedaDist"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 3 ================= -->
        <div class="print-page">
            <div class="section-title">III. Tabel Analisis Detail</div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">Jenis</th>
                        <th style="width: 35%;">Pertanyaan</th>
                        <th style="width: 8%;">P</th>
                        <th style="width: 12%;">Klsf. P</th>
                        <th style="width: 8%;">D</th>
                        <th style="width: 12%;">Klsf. D</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody id="tabelAnalisisBody">
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Scripts -->
    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/apex/apexcharts.min.js"></script>
    
    <script>
    $(document).ready(function() {
        const ujianId = '<?= esc($ujianId, 'js') ?>';
        const filterKelas = '<?= esc($filterKelas, 'js') ?>';
        
        let url = "<?= base_url('panel/analisis-butir/api/') ?>" + ujianId;
        if (filterKelas) url += '?kelas=' + encodeURIComponent(filterKelas);

        $.getJSON(url, function(res) {
            $('#loadingOverlay').hide();

            if (!res.status || !res.data) {
                alert('Gagal memuat data analisis.');
                return;
            }

            const data = res.data;
            
            // 1. Populate summary
            const r = data.ringkasan;
            $('#sumPeserta').text(r.total_peserta);
            $('#sumRataRata').text(r.rata_rata);
            $('#sumKR20').text(r.kr20 !== null ? r.kr20.toFixed(2) : 'N/A');
            $('#sumKR20Badge').text(r.klasifikasi_kr20 || '-');
            $('#sumBerkualitas').text(r.soal_berkualitas + '/' + r.total_soal);

            // 2. Render Charts
            renderCharts(data.per_soal, r);

            // 3. Render Table
            renderTable(data.per_soal);

            // 4. Trigger print automatically after charts render
            setTimeout(function() {
                window.print();
            }, 1000);

        }).fail(function() {
            $('#loadingOverlay').hide();
            alert('Terjadi kesalahan koneksi ke server.');
        });

        // Window onafterprint action
        window.onafterprint = function() {
            window.close();
        };

        // Helpers for classes
        function getPBadgeClass(klasifikasi) {
            switch (klasifikasi) {
                case 'Sedang': return 'badge-modern-success';
                case 'Mudah': return 'badge-modern-warning';
                case 'Sukar': return 'badge-modern-danger';
                default: return 'bg-light text-dark border';
            }
        }

        function getDBadgeClass(klasifikasi) {
            switch (klasifikasi) {
                case 'Sangat Baik': return 'badge-modern-success';
                case 'Baik': return 'badge-modern-info';
                case 'Cukup': return 'badge-modern-warning';
                case 'Jelek': return 'badge-modern-danger';
                default: return 'bg-light text-dark border';
            }
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'Diterima': return 'badge-modern-success';
                case 'Perlu Revisi': return 'badge-modern-warning';
                case 'Dibuang': return 'badge-modern-danger';
                default: return 'bg-light text-dark border';
            }
        }

        function renderCharts(perSoal, ringkasan) {
            if (!perSoal || perSoal.length === 0) return;

            const labels = perSoal.map(s => 'Soal ' + s.soal_no);
            const pValues = perSoal.map(s => s.p);
            const dValues = perSoal.map(s => s.d !== null ? s.d : 0);

            // Colors based on classification
            const pColors = pValues.map(p => {
                if (p > 0.70) return '#fbbf24'; // Mudah
                if (p >= 0.30) return '#10b981'; // Sedang
                return '#f87171'; // Sukar
            });

            const dColors = dValues.map(d => {
                if (d >= 0.40) return '#10b981'; // Sangat Baik
                if (d >= 0.30) return '#22d3ee'; // Baik
                if (d >= 0.20) return '#fbbf24'; // Cukup
                if (d >= 0.00) return '#f97316'; // Jelek
                return '#ef4444'; // Negatif
            });

            // 1. Chart Kesukaran
            new ApexCharts(document.querySelector('#chartKesukaran'), {
                chart: { 
                    type: 'bar', 
                    height: 250, 
                    toolbar: { show: false },
                    animations: { enabled: false }
                },
                series: [{ name: 'Tingkat Kesukaran (P)', data: pValues }],
                xaxis: { 
                    categories: labels, 
                    labels: { style: { fontSize: '10px' } } 
                },
                yaxis: { 
                    min: 0, 
                    max: 1, 
                    title: { text: 'Nilai P', style: { fontSize: '10px' } },
                    labels: { formatter: val => val.toFixed(1) }
                },
                colors: pColors,
                plotOptions: {
                    bar: { distributed: true, borderRadius: 3, columnWidth: '60%' }
                },
                grid: { borderColor: '#f1f1f1', strokeDashArray: 2 },
                legend: { show: false },
                annotations: {
                    yaxis: [
                        { y: 0.30, borderColor: '#ef4444', strokeDashArray: 3, label: { text: 'Sukar', style: { color: '#ef4444', fontSize: '9px' } } },
                        { y: 0.70, borderColor: '#fbbf24', strokeDashArray: 3, label: { text: 'Mudah', style: { color: '#d97706', fontSize: '9px' } } },
                    ]
                }
            }).render();

            // 2. Chart Distribusi Kualitas
            let cDiterima = 0, cRevisi = 0, cDibuang = 0;
            perSoal.forEach(s => {
                if (s.status === 'Diterima') cDiterima++;
                else if (s.status === 'Perlu Revisi') cRevisi++;
                else cDibuang++;
            });

            new ApexCharts(document.querySelector('#chartDistribusi'), {
                chart: { 
                    type: 'donut', 
                    height: 250,
                    animations: { enabled: false }
                },
                series: [cDiterima, cRevisi, cDibuang],
                labels: ['Diterima', 'Perlu Revisi', 'Dibuang'],
                colors: ['#10b981', '#fbbf24', '#f87171'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                legend: { position: 'bottom', fontSize: '10px' }
            }).render();

            // 3. Chart Daya Pembeda
            new ApexCharts(document.querySelector('#chartDayaPembeda'), {
                chart: { 
                    type: 'bar', 
                    height: 250, 
                    toolbar: { show: false },
                    animations: { enabled: false }
                },
                series: [{ name: 'Daya Pembeda (D)', data: dValues }],
                xaxis: { 
                    categories: labels, 
                    labels: { style: { fontSize: '10px' } } 
                },
                yaxis: { 
                    min: -1, 
                    max: 1, 
                    title: { text: 'Nilai D', style: { fontSize: '10px' } },
                    labels: { formatter: val => val.toFixed(1) }
                },
                colors: dColors,
                plotOptions: {
                    bar: { distributed: true, borderRadius: 3, columnWidth: '60%' }
                },
                grid: { borderColor: '#f1f1f1', strokeDashArray: 2 },
                legend: { show: false },
                annotations: {
                    yaxis: [
                        { y: 0.30, borderColor: '#10b981', strokeDashArray: 3, label: { text: 'Baik', style: { color: '#10b981', fontSize: '9px' } } },
                        { y: 0, borderColor: '#ef4444', strokeDashArray: 2 }
                    ]
                }
            }).render();

            // 4. Chart Distribusi Daya Pembeda
            let dSangatBaik = 0, dBaik = 0, dCukup = 0, dJelek = 0, dNegatif = 0;
            perSoal.forEach(s => {
                const d = s.d;
                if (d === null) return;
                if (d >= 0.40) dSangatBaik++;
                else if (d >= 0.30) dBaik++;
                else if (d >= 0.20) dCukup++;
                else if (d >= 0) dJelek++;
                else dNegatif++;
            });

            new ApexCharts(document.querySelector('#chartDayaPembedaDist'), {
                chart: { 
                    type: 'donut', 
                    height: 250,
                    animations: { enabled: false }
                },
                series: [dSangatBaik, dBaik, dCukup, dJelek, dNegatif],
                labels: ['Sangat Baik', 'Baik', 'Cukup', 'Jelek', 'Negatif'],
                colors: ['#10b981', '#22d3ee', '#fbbf24', '#f97316', '#ef4444'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                legend: { position: 'bottom', fontSize: '9px' }
            }).render();
        }

        function renderTable(perSoal) {
            const tbody = $('#tabelAnalisisBody');
            tbody.empty();

            if (perSoal.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center text-muted">Tidak ada data analisis.</td></tr>');
                return;
            }

            perSoal.forEach(s => {
                const tr = $('<tr></tr>');
                
                tr.append($('<td class="text-center fw-bold"></td>').text(s.soal_no));
                tr.append($('<td class="text-center"></td>').html('<span class="badge bg-secondary text-white text-uppercase" style="font-size:0.65rem;">' + s.jenis_soal + '</span>'));
                
                // Strip HTML tags for clean rendering
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = s.pertanyaan_html || s.pertanyaan;
                const cleanTeks = tempDiv.textContent || tempDiv.innerText || s.pertanyaan;
                const truncatedTeks = cleanTeks.length > 85 ? cleanTeks.substring(0, 85) + '...' : cleanTeks;
                
                tr.append($('<td></td>').text(truncatedTeks));
                tr.append($('<td class="text-center fw-bold"></td>').text(s.p.toFixed(2)));
                tr.append($('<td class="text-center"></td>').html('<span class="badge-modern ' + getPBadgeClass(s.klasifikasi_p) + '">' + s.klasifikasi_p + '</span>'));
                tr.append($('<td class="text-center fw-bold"></td>').text(s.d !== null ? s.d.toFixed(2) : 'N/A'));
                tr.append($('<td class="text-center"></td>').html('<span class="badge-modern ' + getDBadgeClass(s.klasifikasi_d) + '">' + s.klasifikasi_d + '</span>'));
                tr.append($('<td class="text-center"></td>').html('<span class="badge-modern ' + getStatusBadgeClass(s.status) + '">' + s.status + '</span>'));

                tbody.append(tr);
            });
        }
    });
    </script>
</body>
</html>
