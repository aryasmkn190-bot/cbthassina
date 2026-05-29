<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
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
    
    .ujian-card-header {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .ujian-header-circle {
        position: absolute;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        bottom: -50px;
        right: -30px;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- 🖥️ Top Header Panel -->
        <div class="card mb-4 p-4 text-white ujian-card-header border-0 shadow-sm">
            <div class="ujian-header-circle"></div>
            <div class="z-1">
                <span class="badge mb-2 py-1 px-3 rounded-pill small" style="background: rgba(255,255,255,0.22); font-weight: 700;">ACADEMIC PORTAL</span>
                <h4 class="fw-bold mb-1 text-white">Ujian Online & Penilaian</h4>
                <p class="mb-0 text-white-80 small">Siswa: <?= esc($peserta['nama'] ?? 'Nama Siswa') ?> | Kelas: <?= esc($peserta['kelas'] ?? '-') ?> | NISN: <?= esc($peserta['nisn'] ?? '-') ?></p>
            </div>
        </div>

        <!-- 📝 Exam List Section -->
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i data-feather="edit-3" class="text-primary me-2"></i>Daftar Ujian Aktif</h5>
                <div class="text-muted small fw-bold" id="datetime-display"></div>
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

            <nav class="d-flex justify-content-center mt-3">
                <ul class="pagination" id="pagination"></ul>
            </nav>
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
        const clockDisplay = document.getElementById('datetime-display');
        if (clockDisplay) clockDisplay.innerText = localeString;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);

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

        const totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const paginated = filtered.slice(start, end);

        if (paginated.length === 0) {
            container.html('<div class="col-12 text-center py-5"><p class="text-muted small">Tidak ada ujian aktif saat ini.</p></div>');
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
                    `<button class="btn btn-success btn-sm w-100 lihat-hasil-btn shadow-sm" data-id="${u.id}"><i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>Lihat Hasil Ujian</button>` :
                    `<button class="btn btn-outline-success btn-sm w-100" disabled>Ujian Selesai</button>`;
            } else if (u.status_peserta === 'sedang_mengerjakan') {
                statusBadge = '<span class="badge bg-primary-subtle text-primary rounded-pill fw-bold" style="background-color: #eff6ff; color: #3b82f6 !important;">Dikerjakan</span>';
                buttonHtml = `<button class="btn btn-warning text-white btn-sm w-100 lanjutkan-ujian-btn shadow-sm" data-id="${u.id}" data-pakai-token="${u.pakai_token}"><i data-feather="play-circle" class="me-1" style="width: 14px; height: 14px;"></i>Lanjutkan Ujian</button>`;
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
