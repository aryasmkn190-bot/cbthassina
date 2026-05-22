<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header Ujian -->
            <div class="mb-4 pb-3 border-bottom">
                <h4 class="fw-bold mb-1"><?= esc($ujian['nama_ujian']) ?></h4>
                <div class="text-muted small">
                    <span class="me-3"><i class="bi bi-clock"></i> <?= esc($ujian['durasi_ujian']) ?> menit</span>
                    <span class="me-3"><i class="bi bi-calendar-event"></i> <?= date('d M Y, H:i', strtotime($ujian['waktu_mulai'])) ?></span>
                </div>
            </div>

            <!-- Toolbar Filter -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                <!-- Input pencarian lebih lebar -->
                <input type="text" id="searchBox"
                    class="form-control form-control-sm flex-grow-1"
                    placeholder="Cari peserta...">

                <!-- Dropdown filter & sort otomatis rapat di kanan -->
                <div class="d-flex gap-2">
                    <select id="filterStatus" class="form-select form-select-sm w-auto">
                        <option value="">Semua Status</option>
                        <option value="belum_mulai">Belum Mulai</option>
                        <option value="sedang_ujian">Sedang Ujian</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <select id="filterKelas" class="form-select form-select-sm w-auto">
                        <option value="">Semua Kelas</option>
                    </select>

                    <select id="sortBy" class="form-select form-select-sm w-auto">
                        <option value="">Urutkan...</option>
                        <option value="nilai_desc">Nilai Tertinggi</option>
                        <option value="nilai_asc">Nilai Terendah</option>
                        <option value="kelas_asc">Kelas A-Z</option>
                        <option value="status_asc">Status</option>
                    </select>
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


<?= $this->endSection() ?>


<?= $this->section('pagejs') ?>
<script>
    $(document).ready(function() {
        const ujianId = '<?= $ujianid ?>';
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        const perPage = 5;

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

            pageData.forEach(row => {
                $listEl.append(`
            <div class="card border-0 shadow-sm rounded-3 mb-2">
                <div class="card-body d-flex justify-content-between align-items-center py-2 px-3">
                    <div>
                        <div class="fw-semibold">${row.nama_peserta}</div>
                        <div class="small text-muted">${row.nama_kelas}</div>
                        <div class="small mt-1">
                            Nilai: <strong>${row.nilai_total || 0}</strong>
                            ${renderStatus(row.status)}
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light btn-sm p-1 lihat-jawaban-btn" data-id="${row.id}" title="Lihat Jawaban">
                            <i data-feather="eye" width="14" height="14"></i>
                        </button>
                        <button class="btn btn-light btn-sm p-1 ulang-ujian-btn" data-id="${row.id}" title="Ulang Ujian">
                            <i data-feather="rotate-ccw" width="14" height="14"></i>
                        </button>
                        <button class="btn btn-light btn-sm p-1 reset-device-btn" data-id="${row.id}" title="Reset Device">
                            <i data-feather="unlock" width="14" height="14"></i>
                        </button>
                        ${row.status === 'sedang_ujian' ? `
                            <button class="btn btn-light btn-sm p-1 selesai-ujian-btn" 
                                data-id="${row.ujian_id}" data-idpeserta="${row.peserta_id}" title="Paksa Selesai">
                                <i data-feather="check-circle" width="14" height="14"></i>
                            </button>` : ''}
                    </div>
                </div>
            </div>
        `);
            });

            renderPagination();
            feather.replace();
        }

        // versi badge minimalis
        function renderStatus(status) {
            if (status === 'belum_mulai') return '<span class="badge rounded-pill bg-light text-danger border">Belum</span>';
            if (status === 'sedang_ujian') return '<span class="badge rounded-pill bg-warning-subtle text-dark border">Ujian</span>';
            if (status === 'selesai') return '<span class="badge rounded-pill bg-success-subtle text-success border">Selesai</span>';
            return '<span class="badge rounded-pill bg-secondary-subtle text-muted border">?</span>';
        }


        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / perPage);
            let html = '';

            if (totalPages > 1) {
                for (let i = 1; i <= totalPages; i++) {
                    html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <button class="page-link" data-page="${i}">${i}</button>
                    </li>`;
                }
            }

            $paginationEl.html(html);
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

        loadData();
    });
</script>

<?= $this->endSection() ?>