<?= $this->extend('Layout/main_peserta'); ?>
<?= $this->section('css'); ?>
<style>
    /* ===== Umum ===== */


    .card-ujian {
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        background-color: #fff;
        padding: 1rem;
    }

    .card-ujian:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .card-ujian .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }

    .card-ujian .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    /* Pagination */
    .pagination {
        justify-content: center;
    }

    .pagination .page-link {
        cursor: pointer;
    }

    /* Search & Filter */
    #searchUjian {
        border-radius: 8px;
    }

    .filter-select {
        border-radius: 8px;
    }

    /* Info Peserta */
    .info-peserta {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .info-peserta h5 {
        margin: 0;
        font-weight: 600;
    }

    .info-peserta p {
        margin: 0;
        font-size: 0.9rem;
        color: #6c757d;
    }

    /* Waktu real-time */
    #datetime {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<style>
    .hover-shadow {
        transition: all 0.2s ease-in-out;
    }

    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .card .btn i {
        vertical-align: middle;
    }
</style>
<style>
    /* Fokus effect untuk semua input & select */
    #searchUjian:focus,
    #sortTanggal:focus,
    #filterHariIni:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.3);
        outline: none;
    }

    /* Samakan tinggi input dan select */
    #searchUjian,
    #sortTanggal,
    #filterHariIni {
        height: 56px;
        /* sama dengan form-control-lg */
    }

    /* Padding kanan untuk icon di select */
    #sortTanggal,
    #filterHariIni {
        padding-right: 2rem !important;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">

        <!-- Info Peserta -->
        <div class="card mb-3 p-3 rounded shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div>
                <h5 class="mb-1 fw-bold"><?= esc($peserta['nama'] ?? 'Nama Peserta') ?></h5>
                <p class="mb-0 text-muted">NISN: <?= esc($peserta['nisn'] ?? '-') ?> | Kelas: <?= esc($peserta['kelas'] ?? '-') ?></p>
            </div>
            <div class="d-flex align-items-center gap-1 text-muted small" id="datetime">
                <i data-feather="clock"></i>
                <span id="datetime-text"></span>
            </div>
        </div>


        <h4 class="fw-bold mb-3">Daftar Ujian</h4>

        <!-- 🔍 Search & Filter -->
        <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">

            <!-- Search modern -->
            <div class="flex-grow-1 position-relative">
                <input type="text" id="searchUjian" class="form-control form-control-lg rounded-pill ps-5 shadow-sm" placeholder="Cari ujian...">
                <i data-feather="search" class="position-absolute" style="top:50%; left:18px; transform:translateY(-50%); width:18px; height:18px; color:#6c757d;"></i>
            </div>

            <!-- Sort & Filter kecil tapi sama tinggi -->
            <div class="d-flex gap-2">
                <div class="position-relative">
                    <select id="sortTanggal" class="form-select form-select-lg pe-5 rounded-pill shadow-sm">
                        <option value="desc">Terbaru</option>
                        <option value="asc">Terlama</option>
                    </select>
                    <i data-feather="calendar" class="position-absolute" style="top:50%; right:12px; transform:translateY(-50%); width:18px; height:18px; pointer-events:none;"></i>
                </div>

                <div class="position-relative">
                    <select id="filterHariIni" class="form-select form-select-lg pe-5 rounded-pill shadow-sm">
                        <option value="all">Semua</option>
                        <option value="today">Hari Ini</option>
                    </select>
                    <i data-feather="filter" class="position-absolute" style="top:50%; right:12px; transform:translateY(-50%); width:18px; height:18px; pointer-events:none;"></i>
                </div>
            </div>
        </div>






        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mt-3" id="ujian-list"></div>

        <nav>
            <ul class="pagination mt-3" id="pagination"></ul>
        </nav>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    // Waktu real-time
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
        document.getElementById('datetime-text').innerText = localeString;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    let ujianData = [];
    let currentPage = 1;
    const perPage = 6;

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

        const totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const paginated = filtered.slice(start, end);

        if (paginated.length === 0) {
            container.html('<div class="col-12 text-center"><p>Tidak ada ujian.</p></div>');
            $("#pagination").empty();
            return;
        }

        paginated.forEach(u => {
            const mulai = new Date(u.waktu_mulai);
            const selesai = new Date(u.waktu_selesai);

            // Status & button
            let status = '',
                button = '';
            if (u.status_peserta === 'selesai') {
                status = '<span class="badge bg-success rounded-pill">Selesai</span>';
                button = u.tampil_nilai == 1 ?
                    `<button class="btn btn-success btn-sm w-100 lihat-hasil-btn" data-id="${u.id}"><i data-feather="eye" class="me-1"></i>Lihat Hasil</button>` :
                    `<button class="btn btn-outline-success btn-sm w-100" disabled>Selesai</button>`;
            } else if (u.status_peserta === 'sedang_mengerjakan') {
                status = '<span class="badge bg-primary rounded-pill">Sedang Dikerjakan</span>';
                button = `<button class="btn btn-warning btn-sm w-100 lanjutkan-ujian-btn" data-id="${u.id}" data-pakai-token="${u.pakai_token}"><i data-feather="play" class="me-1"></i>Lanjutkan</button>`;
            } else if (u.status_waktu === 'belum_mulai') {
                status = '<span class="badge bg-warning rounded-pill">Belum Dimulai</span>';
                button = '<button class="btn btn-outline-secondary btn-sm w-100" disabled>Belum Mulai</button>';
            } else if (u.status_waktu === 'dibuka') {
                status = '<span class="badge bg-success rounded-pill">Sedang Dibuka</span>';
                button = `<button class="btn btn-primary btn-sm w-100 mulai-ujian-btn" data-id="${u.id}" data-pakai-token="${u.pakai_token}"><i data-feather="play-circle" class="me-1"></i>Mulai</button>`;
            } else if (u.status_waktu === 'terlambat') {
                status = '<span class="badge bg-danger rounded-pill">Terlambat</span>';
                button = '<button class="btn btn-outline-danger btn-sm w-100" disabled>Terlambat</button>';
            }

            container.append(`
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mb-3">
            <div class="card shadow-sm rounded p-3 h-100 hover-shadow">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold text-primary mb-0"><i data-feather="book-open" class="me-1"></i>${u.nama_ujian}</h6>
                    <span class="text-muted small">(${u.kode_ujian})</span>
                </div>

                <div class="d-flex justify-content-between text-muted small mb-2">
                    <div><i data-feather="calendar" class="me-1"></i>${mulai.toLocaleDateString()} ${mulai.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
                    <div><i data-feather="clock" class="me-1"></i>${selesai.toLocaleDateString()} ${selesai.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-info rounded-pill">Durasi: ${u.durasi_ujian} Menit</span>
                    ${status}
                </div>

                ${button}
            </div>
        </div>
    `);
        });




        // Pagination
        let pagHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            pagHTML += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        $("#pagination").html(pagHTML);
        feather.replace();
    }

    $(document).ready(function() {
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

        $("#searchUjian, #sortTanggal, #filterHariIni").on("input change", function() {
            currentPage = 1;
            renderUjian();
        });
        $(document).on("click", "#pagination .page-link", function() {
            currentPage = parseInt($(this).data("page"));
            renderUjian();
        });


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
            const pakaiToken = $(this).data('pakai-token') == 1; // pastikan data-pakai-token dikirim dari server

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