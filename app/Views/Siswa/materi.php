<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .materi-card {
        border-radius: 20px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid rgba(0, 0, 0, 0.04);
        background-color: #fff;
    }
    .materi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.08);
    }
    .materi-icon-bg {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
        font-weight: 700;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.02);
    }
    .color-blue { background: rgba(59, 130, 246, 0.08) !important; color: #2563eb !important; }
    .color-purple { background: rgba(139, 92, 246, 0.08) !important; color: #7c3aed !important; }
    .color-green { background: rgba(16, 185, 129, 0.08) !important; color: #059669 !important; }
    .color-amber { background: rgba(245, 158, 11, 0.08) !important; color: #d97706 !important; }
    
    body.dark .materi-card {
        background-color: #0e1726;
        border-color: rgba(255, 255, 255, 0.05);
    }
    body.dark .color-blue { background: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    body.dark .color-purple { background: rgba(139, 92, 246, 0.15) !important; color: #a78bfa !important; }
    body.dark .color-green { background: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; }
    body.dark .color-amber { background: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">

        <!-- Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Materi Belajar & E-Book</h4>
                <p class="mb-0 text-muted small">Akses dan unduh buku pelajaran, modul ajar, dan handout rangkuman materi.</p>
            </div>
            <div class="text-success">
                <i data-feather="book-open" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card mb-4 p-3 rounded-4 shadow-sm border-0 bg-white">
            <div class="input-group rounded-pill overflow-hidden border">
                <span class="input-group-text bg-white border-0 py-2.5 px-3">
                    <i data-feather="search" class="text-muted" style="width: 18px; height: 18px;"></i>
                </span>
                <input type="text" id="materiSearch" class="form-control border-0 px-2" placeholder="Cari buku cetak, bab pembahasan, mata pelajaran..." style="font-size: 0.9rem; height: 46px;">
            </div>
        </div>

        <!-- Materials Grid -->
        <div class="row g-4" id="materiGrid">
            
            <!-- Buku 1 -->
            <div class="col-xl-3 col-lg-4 col-sm-6 col-12 materi-item" data-title="matematika wajib kalkulus integral">
                <div class="card materi-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="materi-icon-bg color-blue">
                            <i data-feather="book" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Matematika XI</h6>
                        <p class="text-primary small fw-semibold mb-2">Kalkulus & Integral</p>
                        <p class="text-muted small mb-3">Modul latihan soal, teori limit, turunan fungsi trigonometri, dan pembahasan integral tentu.</p>
                    </div>
                    <div>
                        <div class="text-muted small mb-3 border-top pt-2.5" style="font-size: 0.72rem;">Ukuran: <strong>4.2 MB</strong> | PDF</div>
                        <button class="btn btn-primary rounded-pill w-100 py-2 btn-unduh fw-bold"><i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>Unduh File</button>
                    </div>
                </div>
            </div>

            <!-- Buku 2 -->
            <div class="col-xl-3 col-lg-4 col-sm-6 col-12 materi-item" data-title="fisika eksperimen optik cahaya cermin lensa">
                <div class="card materi-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="materi-icon-bg color-purple">
                            <i data-feather="sun" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Fisika Optik</h6>
                        <p class="text-purple small fw-semibold mb-2">Cahaya & Alat Optik</p>
                        <p class="text-muted small mb-3">Materi pembahasan pemantulan dan pembiasan cahaya, perhitungan cermin cekung/cembung, serta lensa mata.</p>
                    </div>
                    <div>
                        <div class="text-muted small mb-3 border-top pt-2.5" style="font-size: 0.72rem;">Ukuran: <strong>3.8 MB</strong> | PDF</div>
                        <button class="btn btn-primary rounded-pill w-100 py-2 btn-unduh fw-bold"><i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>Unduh File</button>
                    </div>
                </div>
            </div>

            <!-- Buku 3 -->
            <div class="col-xl-3 col-lg-4 col-sm-6 col-12 materi-item" data-title="bahasa inggris analytical exposition text writing reading">
                <div class="card materi-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="materi-icon-bg color-green">
                            <i data-feather="message-circle" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Bahasa Inggris</h6>
                        <p class="text-success small fw-semibold mb-2">Analytical Text</p>
                        <p class="text-muted small mb-3">Modul ajar structure of Analytical Exposition, arguments composition, reading comprehension exercises.</p>
                    </div>
                    <div>
                        <div class="text-muted small mb-3 border-top pt-2.5" style="font-size: 0.72rem;">Ukuran: <strong>1.5 MB</strong> | PDF</div>
                        <button class="btn btn-primary rounded-pill w-100 py-2 btn-unduh fw-bold"><i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>Unduh File</button>
                    </div>
                </div>
            </div>

            <!-- Buku 4 -->
            <div class="col-xl-3 col-lg-4 col-sm-6 col-12 materi-item" data-title="sejarah indonesia kuno candi hindu budha kerajaan nusantara">
                <div class="card materi-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="materi-icon-bg color-amber">
                            <i data-feather="shield" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Sejarah Kita</h6>
                        <p class="text-warning small fw-semibold mb-2">Candi & Kerajaan Hindu</p>
                        <p class="text-muted small mb-3">E-Book sejarah kerajaan-kerajaan bercorak Hindu-Budha di Nusantara, asal-usul candi, prasasti, kebudayaan kuno.</p>
                    </div>
                    <div>
                        <div class="text-muted small mb-3 border-top pt-2.5" style="font-size: 0.72rem;">Ukuran: <strong>8.7 MB</strong> | PDF</div>
                        <button class="btn btn-primary rounded-pill w-100 py-2 btn-unduh fw-bold"><i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>Unduh File</button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        // Search Filter
        $("#materiSearch").on("keyup input", function() {
            const query = $(this).val().toLowerCase().trim();
            $(".materi-item").each(function() {
                const searchStr = $(this).data("title");
                if (searchStr.includes(query)) {
                    $(this).show(200);
                } else {
                    $(this).hide(200);
                }
            });
        });

        // Download Action
        $(".btn-unduh").on("click", function() {
            Swal.fire({
                icon: 'success',
                title: 'Materi Diunduh',
                text: 'Modul PDF materi pelajaran berhasil diunduh ke perangkat Anda.',
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-4' }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
