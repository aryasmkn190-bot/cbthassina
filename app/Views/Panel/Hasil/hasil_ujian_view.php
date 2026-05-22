<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>
<style>
    /* Warna teks utama di dark mode */
    body.dark .card .fw-semibold,
    body.dark .card .text-body {
        color: #e6e6e6;
        /* lebih terang agar kontras */
    }

    /* Warna teks kecil (kelas, nilai) */
    body.dark .card .small.text-muted {
        color: #9ca3af;
        /* abu muda */
    }

    /* Tombol outline di dark mode biar lebih kontras */
    body.dark .btn-outline-secondary {
        color: #e6e6e6;
        border-color: #555;
    }

    body.dark .btn-outline-secondary:hover {
        background-color: #555;
        color: #fff;
    }

    /* Biar feather icon selalu jelas di dark */
    body.dark .btn i {
        stroke: #e6e6e6;
        /* feather pakai stroke */
    }

    /* Animasi hover pada card list peserta */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        /* naik sedikit */
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        /* bayangan lebih tebal */
    }

    /* Dark mode: biar tetap estetik */
    body.dark .card {
        background-color: #1e293b;
        /* sedikit lebih gelap */
        border: 1px solid #2d3b4d;
    }

    body.dark .card:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5);
    }
</style>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header Ujian -->
            <div class="mb-4 pb-3">
                <h4 class="fw-bold mb-1"><?= esc($ujian['nama_ujian']) ?></h4>
                <div class="text-muted small">
                    <span class="me-3"><i class="bi bi-clock"></i> <?= esc($ujian['durasi_ujian']) ?> menit</span>
                    <span class="me-3"><i class="bi bi-calendar-event"></i> <?= date('d M Y, H:i', strtotime($ujian['waktu_mulai'])) ?></span>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">

                <!-- Input pencarian -->
                <input type="text" id="searchBox"
                    class="form-control form-control-sm flex-grow-1"
                    placeholder="Cari peserta...">

                <!-- Filter & Sort -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <select id="filterStatus" class="form-select form-select-sm flex-fill flex-sm-grow-0 w-auto">
                        <option value="">Semua Status</option>
                        <option value="belum_mulai">Belum Mulai</option>
                        <option value="sedang_ujian">Sedang Ujian</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <select id="filterKelas" class="form-select form-select-sm flex-fill flex-sm-grow-0 w-auto">
                        <option value="">Semua Kelas</option>
                    </select>

                    <select id="sortBy" class="form-select form-select-sm flex-fill flex-sm-grow-0 w-auto">
                        <option value="">Urutkan...</option>
                        <option value="nilai_desc">Nilai Tertinggi</option>
                        <option value="nilai_asc">Nilai Terendah</option>
                        <option value="kelas_asc">Kelas A-Z</option>
                        <option value="status_asc">Status</option>
                    </select>

                    <select id="perPageSelect" class="form-select form-select-sm flex-fill flex-sm-grow-0 w-auto">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">Semua</option>
                    </select>
                </div>



                <!-- Tombol Aksi -->
                <div class="d-flex gap-1 ms-auto">
                    <button id="refreshList" class="btn btn-outline-secondary btn-sm" title="Refresh">
                        <i data-feather="refresh-cw"></i>
                    </button>
                    <button id="kirimjawaban" class="btn btn-outline-primary btn-sm" title="Upload Jawaban">
                        <i data-feather="upload"></i>
                    </button>
                    <button id="exportExcel" class="btn btn-outline-success btn-sm" title="Export Excel">
                        <i data-feather="file-text"></i>
                    </button>
                    <button id="selesaikanSemua" class="btn btn-outline-danger btn-sm" title="Selesaikan Semua">
                        <i data-feather="check-square"></i>
                    </button>
                </div>


            </div>



            <!-- List Peserta -->
            <div id="hasilList" class="list-group list-group-flush"></div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
        </div>
    </div>
</div>


<!-- Modal Detail Jawaban tetap sama -->
<div class="modal fade" id="modalDetailJawaban" tabindex="-1" aria-labelledby="modalDetailJawabanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailJawabanLabel">Detail Jawaban</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailJawabanKonten">
                <p class="text-center text-muted">Memuat jawaban...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" onclick="printJawaban()">Print</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('pagejs') ?>
<script>
    $(document).ready(function() {
        const ujianId = '<?= $ujianid ?>';
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        let perPage = 10;

        const $listEl = $('#hasilList');
        const $paginationEl = $('#pagination');
        const $searchBox = $('#searchBox');
        const $filterStatus = $('#filterStatus');
        const $filterKelas = $('#filterKelas');
        const $sortBy = $('#sortBy');

        // Fetch data pakai jQuery
        function loadData() {
            $.ajax({
                url: "<?= base_url('panel/hasil-ujian/get/') ?>" + ujianId,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    allData = res.data || [];

                    // ambil unique kelas
                    buildKelasList(allData);

                    applyFilters();
                },
                error: function(xhr, status, error) {
                    console.error("Error load data:", error);
                }
            });
        }

        function buildKelasList(data) {
            const kelasSet = new Set();

            data.forEach(item => {
                if (item.nama_kelas) {
                    kelasSet.add(item.nama_kelas);
                }
            });

            const kelasList = Array.from(kelasSet);

            $filterKelas.empty().append('<option value="">Semua Kelas</option>');
            kelasList.forEach(k => {
                $filterKelas.append(`<option value="${k}">${k}</option>`);
            });
        }

        function applyFilters() {
            const search = $searchBox.val().toLowerCase();
            const status = $filterStatus.val();
            const kelas = $filterKelas.val();
            let data = [...allData];

            if (search) {
                data = data.filter(d =>
                    d.nama_peserta.toLowerCase().includes(search) ||
                    d.nisn.toLowerCase().includes(search)
                );
            }
            if (status) data = data.filter(d => d.status === status);
            if (kelas) data = data.filter(d => d.nama_kelas === kelas);

            // Sorting
            switch ($sortBy.val()) {
                case 'nilai_desc':
                    data.sort((a, b) => b.nilai_total - a.nilai_total);
                    break;
                case 'nilai_asc':
                    data.sort((a, b) => a.nilai_total - b.nilai_total);
                    break;
                case 'kelas_asc':
                    data.sort((a, b) => a.nama_kelas.localeCompare(b.nama_kelas));
                    break;
                case 'status_asc':
                    data.sort((a, b) => a.status.localeCompare(b.status));
                    break;
            }

            filteredData = data;
            renderList();
        }

        function renderList() {
            $listEl.empty();
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = filteredData.slice(start, end);

            if (pageData.length === 0) {
                $listEl.html('<div class="text-center text-muted p-4">Tidak ada data</div>');
                $paginationEl.empty();
                return;
            }

            pageData.forEach((row, i) => {
                const nomor = start + i + 1; // nomor urut global
                let waktuText = (row.status === 'belum_mulai') ?
                    'Belum dikerjakan' :
                    formatDateTime(row.waktu_mulai);
                $listEl.append(`
                    <div class="card border-0 shadow-sm rounded-3 mb-2 hover-card">
                        <div class="card-body d-flex justify-content-between align-items-center py-2 px-3">
                            <div>
                                <div class="fw-semibold">${nomor}. ${row.nama_peserta}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-muted d-flex align-items-center">
                                        <i data-feather="users" width="14" height="14" class="me-1"></i> 
                                        ${row.nama_kelas}
                                    </div>
                                    <div class="small text-muted d-flex align-items-center ms-3">
                                        <i data-feather="clock" width="14" height="14" class="me-1"></i> 
                                        ${waktuText}
                                        ${row.platform ? `<span class="ms-2 d-flex align-items-center"><i data-feather="monitor" width="14" height="14" class="me-1"></i> ${row.platform}</span>` : ""}
                                    </div>
                                    </div>


                            <div class="small mt-1 fw-semibold d-flex align-items-center gap-2">
                                    ${row.status !== 'sedang_ujian' ? `Nilai: <strong>${row.nilai_total || 0}</strong>` : ''}
                                    ${renderStatus(row.status)}
                                    ${row.status === 'sedang_ujian' ? `
                                        <div class="spinner-border spinner-border-sm text-primary ms-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    ` : ''}
                                </div>

                            </div>
                            <div class="d-flex gap-1">
                                ${row.status !== 'belum_mulai' ? `
                                    <button class="btn btn-outline-secondary btn-sm p-1 lihat-jawaban-btn" 
                                        data-id="${row.id}" title="Lihat Jawaban">
                                        <i data-feather="eye" width="14" height="14"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm p-1 ulang-ujian-btn" 
                                        data-id="${row.id}" title="Ulang Ujian">
                                        <i data-feather="rotate-ccw" width="14" height="14"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm p-1 reset-device-btn" 
                                        data-id="${row.id}" title="Reset Device">
                                        <i data-feather="unlock" width="14" height="14"></i>
                                    </button>
                                    
                                ` : ''}
                                ${(row.status === 'sedang_ujian' && row.platform !== 'android') ? `
                                    <button class="btn btn-outline-success btn-sm p-1 selesai-ujian-btn" 
                                        data-id="${row.ujian_id}" data-idpeserta="${row.peserta_id}" title="Paksa Selesai">
                                        <i data-feather="check-circle" width="14" height="14"></i>
                                    </button>
                                ` : ''}
                                 ${(row.status === 'selesai') ? `
                                   <button class="btn btn-outline-info btn-sm p-1 koreksi-ulang-btn" 
                                            data-id="${row.id}" title="Koreksi Ulang">
                                            <i data-feather="refresh-cw" width="14" height="14"></i>
                                        </button>
                                ` : ''}

                            </div>
                        </div>
                    </div>
                `);
            });

            renderPagination();
            feather.replace();
        }


        function formatDateTime(datetimeStr) {
            // parsing string tanggal dari API (misalnya "2025-09-21 08:30:00")
            let date = new Date(datetimeStr);

            // opsi format (Indonesia)
            let optionsDate = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            let optionsTime = {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };

            let tanggal = date.toLocaleDateString('id-ID', optionsDate); // "21 Sep 2025"
            let waktu = date.toLocaleTimeString('id-ID', optionsTime); // "08.30"

            return `${tanggal} - ${waktu}`;
        }


        // versi badge minimalis
        function renderStatus(status) {
            if (status === 'belum_mulai') return '<span class="badge rounded-pill bg-light text-danger border">Belum</span>';
            if (status === 'sedang_ujian') return '<span class="badge rounded-pill bg-warning-subtle text-dark border">Mengerjakan</span>';
            if (status === 'selesai') return '<span class="badge rounded-pill bg-success-subtle text-success border">Selesai</span>';
            return '<span class="badge rounded-pill bg-secondary-subtle text-muted border">?</span>';
        }


        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / perPage);
            let html = '';

            if (totalPages > 1) {
                // tombol Prev
                html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                    <i data-feather="chevron-left" width="14" height="14"></i>
                </button>
            </li>`;

                if (totalPages <= 5) {
                    // kalau sedikit halaman, tampilkan semua
                    for (let i = 1; i <= totalPages; i++) {
                        html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <button class="page-link" data-page="${i}">${i}</button>
                    </li>`;
                    }
                } else {
                    // halaman pertama
                    html += `
                <li class="page-item ${1 === currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="1">1</button>
                </li>`;

                    // ellipsis kiri
                    if (currentPage > 3) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }

                    // halaman sekitar current
                    let start = Math.max(2, currentPage - 1);
                    let end = Math.min(totalPages - 1, currentPage + 1);

                    for (let i = start; i <= end; i++) {
                        html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <button class="page-link" data-page="${i}">${i}</button>
                    </li>`;
                    }

                    // ellipsis kanan
                    if (currentPage < totalPages - 2) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }

                    // halaman terakhir
                    html += `
                <li class="page-item ${totalPages === currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="${totalPages}">${totalPages}</button>
                </li>`;
                }

                // tombol Next
                html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i data-feather="chevron-right" width="14" height="14"></i>
                </button>
            </li>`;
            }

            $paginationEl.html(html);

            // render feather icons
            if (window.feather) {
                feather.replace();
            }
        }


        // Event listeners
        $searchBox.on('input', function() {
            currentPage = 1;
            applyFilters();
        });

        $filterStatus.on('change', function() {
            currentPage = 1;
            applyFilters();
        });

        $filterKelas.on('change', function() {
            currentPage = 1;
            applyFilters();
        });

        $sortBy.on('change', function() {
            applyFilters();
        });

        $paginationEl.on('click', '.page-link', function() {
            currentPage = parseInt($(this).data('page'));
            renderList();
        });

        $('#perPageSelect').on('change', function() {
            const val = $(this).val();
            if (val === 'all') {
                perPage = filteredData.length; // semua data
            } else {
                perPage = parseInt(val);
            }
            currentPage = 1; // reset ke halaman awal
            renderList();
        });
        loadData();
        $('#refreshList').on('click', function() {
            loadData();
        });

        $('#exportExcel').on('click', function() {
            const kelas = $filterKelas.val();
            const status = $filterStatus.val();
            const search = $searchBox.val();

            let url = "<?= base_url('panel/hasil-ujian/exportskoring/' . $ujianid) ?>";
            let params = [];
            if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
            if (status) params.push('status=' + encodeURIComponent(status));
            if (search) params.push('search=' + encodeURIComponent(search));

            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            window.location.href = url;
        });
        $('#selesaikanSemua').on('click', function() {
            Swal.fire({
                title: 'Selesaikan Semua Peserta?',
                text: "Semua peserta yang sedang ujian akan dipaksa selesai.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post("<?= base_url('panel/hasil-ujian/selesai-semua/' . $ujianid) ?>", function(res) {
                        if (res.status) {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#28a745'
                            });
                            loadData(); // refresh pakai renderList
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#e7515a'
                            });
                        }
                    }).fail(() => {
                        Snackbar.show({
                            text: 'Terjadi kesalahan server',
                            pos: 'top-center',
                            backgroundColor: '#e7515a'
                        });
                    });
                }
            });
        });

        $('#hasilList').on('click', '.ulang-ujian-btn', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Ulangi Ujian?',
                text: 'Semua jawaban peserta akan dihapus dan peserta bisa mengerjakan ulang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ulangi!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e3342f'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `<?= base_url('panel/hasil-ujian/ulang/') ?>${id}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            loadData(); // 🔄 refresh list
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#ffc107'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#dc3545'
                            });
                        }
                    },
                    error: function() {
                        Snackbar.show({
                            text: 'Terjadi kesalahan pada server.',
                            pos: 'top-center',
                            backgroundColor: '#dc3545'
                        });
                    }
                });
            });
        });
        $('#hasilList').on('click', '.koreksi-ulang-btn', function() {
            const id = $(this).data('id');
            console.log(id);
            Swal.fire({
                title: 'Koreksi Ulang?',
                text: 'Sistem akan menghitung ulang nilai dan koreksi jawaban peserta ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Koreksi Ulang!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0dcaf0' // biru muda (info)
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `<?= base_url('panel/hasil-ujian/koreksi-ulang/') ?>${id}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            loadData(); // 🔄 refresh list
                            Snackbar.show({
                                text: res.message || 'Koreksi ulang berhasil dilakukan.',
                                pos: 'top-center',
                                backgroundColor: '#0dcaf0'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message || 'Koreksi ulang gagal.',
                                pos: 'top-center',
                                backgroundColor: '#dc3545'
                            });
                        }
                    },
                    error: function() {
                        Snackbar.show({
                            text: 'Terjadi kesalahan pada server.',
                            pos: 'top-center',
                            backgroundColor: '#dc3545'
                        });
                    }
                });
            });
        });
        $('#hasilList').on('click', '.reset-device-btn', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Buka Kunci Perangkat?',
                text: "Device akan direset dan peserta bisa login ulang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e3342f'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '<?= base_url('panel/hasil-ujian/reset-device') ?>',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            loadData(); // 🔄 refresh list
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#28a745'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#e7515a'
                            });
                        }
                    },
                    error: function() {
                        Snackbar.show({
                            text: 'Terjadi kesalahan server',
                            pos: 'top-center',
                            backgroundColor: '#e7515a'
                        });
                    }
                });
            });
        });
        $('#hasilList').on('click', '.selesai-ujian-btn', function() {
            const id = $(this).data('id');
            const peserta_id = $(this).data('idpeserta');
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: 'Peserta tidak bisa mengerjakan lagi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#38c172'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/hasil-ujian/selesai/') ?>${id}`, {
                        peserta_id
                    }, function(res) {
                        if (res.status) {
                            loadData(); // 🔄 refresh list
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#28a745'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });
        $(document).on('click', '.lihat-jawaban-btn', function() {
            const hasilUjianId = $(this).data('id');
            $('#detailJawabanKonten').html('<p class="text-muted text-center">Memuat data...</p>');
            $.getJSON(`<?= base_url('panel/hasil-ujian/jawaban') ?>/${hasilUjianId}`, function(res) {
                if (res.status) {

                    renderDetailJawaban(res.data);

                } else {
                    $('#detailJawabanKonten').html(`<div class="alert alert-warning">${res.message}</div>`);
                }
            }).fail(() => {
                $('#detailJawabanKonten').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            });
        });
        $('#kirimjawaban').on('click', function() {
            const token = localStorage.getItem('syncToken');
            const url = localStorage.getItem('syncServerUrl');
            const ujianId = '<?= $ujianid ?>';

            if (!token || !url) {
                Swal.fire('Gagal', 'Token atau URL belum tersimpan. Silakan cek koneksi dulu.', 'error');
                return;
            }

            Swal.fire({
                title: 'Kirim Jawaban?',
                text: 'Data hasil ujian selesai akan dikirim ke server pusat.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd'
            }).then(result => {
                if (result.isConfirmed) {
                    $('#kirimjawaban').prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Mengirim...');

                    $.ajax({
                        url: `<?= base_url('panel/hasil-ujian/sinkronisasi') ?>`,
                        type: 'POST',
                        data: {
                            token,
                            url,
                            ujian_id: ujianId
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Sukses', res.message, 'success');
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
                        },
                        complete: function() {
                            $('#kirimjawaban').prop('disabled', false).html('<i data-feather="upload"></i>');
                            feather.replace();
                        }
                    });
                }
            });
        });

    });

    function renderDetailJawaban(data) {
        const {
            peserta,
            soalList,
            jawaban,
            opsiList
        } = data;

        let html = `
            <div class="mb-4 p-3 rounded bg-light border shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex justify-content-start flex-wrap gap-4 small text-secondary">
                        <div><span class="fw-semibold text-dark">Nama:</span> ${peserta?.nama ?? '-'}</div>
                        <div><span class="fw-semibold text-dark">NISN:</span> ${peserta?.nisn ?? '-'}</div>
                        <div><span class="fw-semibold text-dark">Kelas:</span> ${peserta?.nama_kelas ?? '-'}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary" style="font-size: 1.2rem;">
                            Poin: ${data.hasil.poin_benar} / ${data.hasil.poin_maksimal}
                        </div>
                        <div class="small text-muted">Nilai Akhir: ${data.hasil.nilai_total}</div>
                    </div>
                </div>
            </div>`;


        soalList.forEach((soal, i) => {
            const nomor = i + 1;
            const jwb = jawaban[soal.id] ?? null;
            const opsi = opsiList[soal.id] ?? [];
            const poin = jwb?.poin ?? 0;
            const benar = jwb?.is_benar ?? false;

            let status = `<span class="badge bg-danger">Belum Dijawab</span>`;
            if (jwb) {
                status = benar ?
                    `<span class="badge bg-success">Benar</span>` :
                    `<span class="badge bg-warning">Salah</span>`;
            }

            html += `
        <div class="mb-4 border-bottom pb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Soal ${nomor}</h6>
                <div class="d-flex gap-2 align-items-center">
                    ${status}
                    <span class="badge bg-secondary">+${poin} poin</span>
                </div>
            </div>
            <div class="mb-2">${soal.pertanyaan}</div>
           ${renderOpsi(soal, opsi, jwb, data)}
        </div>`;
        });

        $('#detailJawabanKonten').html(html);
        $('#modalDetailJawaban').modal('show');
    }

    function renderOpsi(soal, opsiList, jawaban, data) {
        const jenis = soal.jenis_soal;
        if (!jawaban) return `<em class="text-muted fst-italic">Belum menjawab</em>`;

        const highlight = (dipilih, benar) => {
            if (dipilih && benar) return 'border-success bg-success-subtle';
            if (dipilih && !benar) return 'border-danger bg-danger-subtle';
            if (!dipilih && benar) return 'border-success bg-success-subtle';
            return 'bg-light';
        };

        if (['pg', 'mpg'].includes(jenis)) {
            const selected = jenis === 'pg' ? [jawaban.value] : (jawaban.values ?? []);
            return `
        <div class="list-group shadow-sm rounded-2 overflow-hidden">
            ${opsiList.map(op => {
                const dipilih = selected.includes(op.label);
                const benar = op.is_true == '1';
                return `
                <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(dipilih, benar)}">
                    <div class="d-flex gap-2">
                        <strong>${op.label}.</strong><span>${op.teks}</span>
                    </div>
                    <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                </div>`;
            }).join('')}
        </div>`;
        }

        if (jenis === 'benar_salah') {
            return `
            <div class="list-group shadow-sm rounded-2 overflow-hidden">
                ${opsiList.map(op => {
                    const jwbPeserta = (jawaban[op.label] ?? '').toLowerCase();
                    const isTrue = op.is_true === '1'; // perbaikan tipe data
                    const kunci = isTrue ? 'benar' : 'salah';
                    const benar = jwbPeserta === kunci;

                    return `
                    <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(true, benar)}">
                        <div>
                            <strong>${op.label}.</strong> <span>${op.teks}</span>
                            <div class="small text-muted">
                                Jawaban: <strong>${jwbPeserta || '-'}</strong> | Kunci: <strong>${kunci}</strong>
                            </div>
                        </div>
                        <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                    </div>`;
                }).join('')}
            </div>`;
        }


        if (jenis === 'jodohkan') {
            return `
        <div class="list-group shadow-sm rounded-2 overflow-hidden">
            ${opsiList.map(op => {
                const jwbPeserta = jawaban[op.label] ?? '-';
                const benar = jwbPeserta === op.pasangan;
                return `
                <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(jwbPeserta !== '-', benar)}">
                    <div class="d-flex gap-2">
                        <strong>${op.label}.</strong><span>${op.teks}</span>
                    </div>
                    <div class="text-end small">
                        <div>Jawab: <strong>${jwbPeserta}</strong></div>
                        <div>⇄ Kunci: ${op.pasangan}</div>
                        <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                    </div>
                </div>`;
            }).join('')}
        </div>`;
        }

        if (jenis === 'isian') {
            return `<div class="form-control bg-light">${jawaban.value || '-'}</div>`;
        }

        if (jenis === 'esai') {
            const nilai = jawaban?.poin ?? 0;
            const max = soal?.bobot ?? 10;
            const pesertaId = jawaban?.peserta_id ?? data?.peserta?.id ?? '';
            const ujianId = jawaban?.ujian_id ?? data?.hasil?.ujian_id ?? '';

            return `
                <div class="mb-3">
                    <div class="form-control bg-light mb-3" style="min-height: 100px; white-space: pre-line;">
                        ${jawaban.value || '-'}
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Penilaian (0 - ${max})</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" min="0" max="${max}" step="0.5"
                                class="form-range nilai-slider-esai flex-grow-1"
                                id="slider_esai_${soal.id}" 
                                value="${nilai}"
                                data-soal-id="${soal.id}"
                                data-ujian-id="${ujianId}"
                                data-peserta-id="${pesertaId}">
                            <span class="badge bg-primary fs-6 nilai-display" id="nilai_display_${soal.id}">
                                ${nilai}
                            </span>
                            <button class="btn btn-success btn-sm btn-simpan-nilai-esai"
                                data-soal-id="${soal.id}" 
                                data-max="${max}"
                                data-ujian-id="${ujianId}"
                                data-peserta-id="${pesertaId}">
                                 Simpan
                            </button>
                        </div>
                    </div>
                </div>`;

        }


        return '';
    }
    $(document).on('click', '.btn-simpan-nilai-esai', function() {
        const soalId = $(this).data('soal-id');
        const ujianId = $(this).data('ujian-id');
        const pesertaId = $(this).data('peserta-id');
        const max = parseFloat($(this).data('max'));
        const nilai = parseFloat($(`#slider_esai_${soalId}`).val() || 0);

        if (nilai > max) {
            Swal.fire({
                icon: 'warning',
                title: 'Nilai terlalu tinggi!',
                text: `Maksimal bobot hanya ${max} poin.`,
            });
            return;
        }

        Swal.fire({
            title: 'Simpan Nilai Esai?',
            text: `Nilai yang diberikan: ${nilai} / ${max}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;

            // Kirim data sebagai JSON
            $.ajax({
                url: `<?= base_url() ?>panel/hasil-ujian/koreksi-esai`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    soal_id: soalId,
                    ujian_id: ujianId,
                    peserta_id: pesertaId,
                    poin: nilai
                }),
                success: function(res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: `Nilai esai ${nilai} poin disimpan.`,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // ubah tampilan tombol + slider setelah tersimpan
                        $(`#slider_esai_${soalId}`).prop('disabled', true);
                        $(`button[data-soal-id="${soalId}"]`)
                            .prop('disabled', true)
                            .removeClass('btn-success')
                            .addClass('btn-outline-success')
                            .html('<i data-feather="check"></i> Tersimpan');
                        feather.replace(); // refresh icon feather
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                }
            });
        });
    });




    $(document).on('input', '.nilai-slider-esai', function() {
        const soalId = $(this).data('soal-id');
        const nilai = $(this).val();
        $(`#nilai_display_${soalId}`).text(nilai);
    });
</script>
<script>
    function printJawaban() {
        const content = document.getElementById('detailJawabanKonten').innerHTML;

        const win = window.open('', '_blank');
        win.document.write(`
        <html>
        <head>
            <title>Print Jawaban</title>
            <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <link href="<?= base_url() ?>src/plugins/src/editors/quill/katex.min.css" rel="stylesheet" type="text/css" />
            <style>
                body { padding: 20px; font-size: 14px; }
                .badge { font-size: 0.75rem; }
                .list-group-item { page-break-inside: avoid; }
            </style>
        </head>
        <body>
            <h4 class="mb-3">Detail Jawaban Peserta</h4>
            ${content}
        </body>
        </html>
    `);
        win.document.close();

        // Tunggu seluruh halaman selesai dimuat, baru print
        win.onload = function() {
            win.focus();
            setTimeout(() => {
                win.print();

                // Fallback untuk menutup jika onafterprint tidak dipicu
                const closeFallback = setTimeout(() => {
                    win.close();
                }, 300);

                win.onafterprint = () => {
                    clearTimeout(closeFallback);
                    win.close();
                };
            }, 300);
        };

    }
</script>

<?= $this->endSection() ?>