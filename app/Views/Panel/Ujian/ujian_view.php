<?php $this->extend('Layout/main'); ?>
<?php $this->section('content'); ?>
<style>
    /* Custom minimalis toggle */
    .custom-switch .form-check-input {
        width: 2.5em;
        height: 1.3em;
        cursor: pointer;
        border-radius: 1.5em;
        background-color: #ddd;
        border: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .custom-switch .form-check-input:checked {
        background-color: #0d6efd;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.6);
    }

    .custom-switch .form-check-input::before {
        content: "";
        position: absolute;
        top: 0.15em;
        left: 0.2em;
        width: 0.9em;
        height: 0.9em;
        background: #fff;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .custom-switch .form-check-input:checked::before {
        transform: translateX(1.2em);
    }


    /* Efek hover kode ujian */
    #copyKodeWrapper:hover {
        background: #e9f3ff;
        transform: scale(1.03);
        box-shadow: 0 0 12px rgba(0, 123, 255, 0.4);
        transition: all 0.3s ease;
    }

    /* Efek QR Code */
    #qrCode img {
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    #qrCode img:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
    }

    /* Efek input group link */
    .input-group:hover {
        transform: translateY(-3px);
        transition: transform 0.3s ease;
    }

    /* Tombol salin link */
    #copyLink:hover {
        background-color: #0d6efd;
        color: white;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    /* Tombol masuk */
    #masukLink {
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #masukLink:hover {
        transform: scale(1.05);
        box-shadow: 0 0 18px rgba(0, 123, 255, 0.6);
    }
</style>

<style>
    .jadwal-badge {
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s ease;
        cursor: default;
    }

    .jadwal-badge:hover {
        background-color: rgba(0, 123, 255, 0.1);
        /* highlight biru lembut */
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .aksi-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .aksi-icons a:hover {
        transform: scale(1.2);
        filter: brightness(1.2);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Card interaktif dengan glow biru */
    .card.interactive-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .card.interactive-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0, 123, 255, 0.4), 0 0 12px rgba(0, 123, 255, 0.3);
    }

    .card.interactive-card:active {
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 8px 16px rgba(0, 123, 255, 0.2), 0 0 8px rgba(0, 123, 255, 0.2);
    }
</style>
<style>
    .selected-card {
        border: 2px solid var(--bs-primary) !important;
        background: #f8faff;
    }

    .selected-overlay {
        font-weight: 600;
    }
</style>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
                <h5 class="fw-semibold mb-0">Manajemen Ujian</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah
                    </button>
                    <button type="button" id="btnAturMassal" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPengaturanUjian">
                        <i data-feather="settings"></i> Pengaturan
                    </button>

                    <button type="button" id="refreshList" class="btn btn-outline-secondary">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group shadow-sm rounded overflow-hidden">
                    <!-- Icon Search -->
                    <span class="input-group-text bg-white border-0">
                        <i data-feather="search" class="text-muted"></i>
                    </span>

                    <!-- Input Search -->
                    <input type="text" id="searchBox" class="form-control border-0" placeholder="Cari ujian...">

                    <!-- Select Per Page dengan ikon -->
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <span class="input-group-text bg-white border-0">
                            <i data-feather="layers" class="text-muted"></i>
                        </span>
                        <select id="perPageSelect" class="form-select form-select-sm border-0">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>


                    <!-- Filter Hari Ini -->
                    <span class="input-group-text bg-white border-0">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="filterToday">
                            <label class="form-check-label ms-2 small" for="filterToday">Hari ini</label>
                        </div>
                    </span>
                </div>
            </div>



            <!-- Tempat render list ujian -->
            <div id="ujianList" class="d-flex flex-column gap-3"></div>

            <!-- Pagination -->
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-3"></ul>
            </nav>

        </div>
    </div>
</div>
<!-- Modal Pengaturan Massal -->
<div class="modal fade" id="modalPengaturanUjian" tabindex="-1" aria-labelledby="modalPengaturanUjianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white rounded-top-4">
                <h5 class="modal-title fw-semibold" id="modalPengaturanUjianLabel">
                    <i data-feather="settings" class="me-2"></i> Pengaturan Massal Ujian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="modal-body p-4">

                <!-- Form Pengaturan -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="waktuMulai" class="form-label fw-medium">Waktu Mulai</label>
                        <input type="datetime-local" id="waktuMulai" class="form-control" placeholder="Pilih waktu mulai">
                    </div>
                    <div class="col-md-6">
                        <label for="waktuSelesai" class="form-label fw-medium">Waktu Selesai</label>
                        <input type="datetime-local" id="waktuSelesai" class="form-control" placeholder="Pilih waktu selesai">
                    </div>

                    <div class="col-12 mt-2">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tokenAktif">
                                    <label class="form-check-label fw-medium" for="tokenAktif">
                                        Token Aktif
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ubahTokenMassal">
                                    <label class="form-check-label fw-medium" for="ubahTokenMassal">
                                        Ubah Token Sekaligus
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2" id="inputTokenMassalContainer" style="display: none;">
                        <label for="tokenMassal" class="form-label fw-medium">Token Baru</label>
                        <div class="input-group">
                            <input type="text" id="tokenMassal" class="form-control" placeholder="Masukkan atau generate token baru" maxlength="10">
                            <button class="btn btn-outline-secondary" type="button" id="btnGenerateTokenMassal">
                                <i data-feather="refresh-ccw"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Daftar Ujian Terpilih -->
                <h6 class="fw-semibold mb-2">Daftar Ujian Terpilih</h6>
                <div id="listUjianSimple" class="list-group border rounded small" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-center text-muted py-3 small">Belum ada ujian dipilih</div>
                </div>

            </div>

            <div class="modal-footer bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-success" id="btnExportBelumUjian">
                    <i data-feather="download"></i> Export Belum Ujian
                </button>
                <div>

                    <button type="button" class="btn btn-outline-primary" id="btnSimpanPengaturan">
                        <i data-feather="save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="tambahPesertaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formTambahPeserta" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ujian_id">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_tingkat" class="form-label">Tingkat</label>
                        <select id="filter_tingkat" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($tingkat as $t): ?>
                                <option value="<?= esc($t['id']) ?>"><?= esc($t['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label for="filter_kelas" class="form-label">Kelas</label>
                        <select id="filter_kelas" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= esc($k['id']) ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_jurusan" class="form-label">Jurusan</label>
                        <select id="filter_jurusan" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($jurusan as $j): ?>
                                <option value="<?= esc($j['id']) ?>"><?= esc($j['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_agama" class="form-label">Agama</label>
                        <select id="filter_agama" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($agama as $a): ?>
                                <option value="<?= esc($a['id']) ?>"><?= esc($a['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-5">
                        <label>Belum Ditambahkan (<span id="count-belum">0</span>)</label>
                        <select id="list-belum" class="form-select" multiple size="10">
                            <!-- Diisi via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-2 d-flex flex-column justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-tambah">
                            <i data-feather="arrow-right-circle"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btn-hapus">
                            <i data-feather="arrow-left-circle"></i>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label>Sudah Ditambahkan (<span id="count-sudah">0</span>)</label>
                        <select name="peserta_id[]" id="list-sudah" class="form-select" multiple size="10">
                            <!-- Akan dipindahkan dari kiri ke kanan -->
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Ujian (Ex : STS_GANJIL_X_BINDO)</label>
                        <input type="text" name="nama_ujian" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kode Ujian</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="kode_ujian" id="kodeUjian"
                                class="form-control" required readonly>
                            <button type="button" class="btn btn-outline-primary" id="btnGenerateKode">
                                <i data-feather="refresh-ccw"></i> Generate
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Token</label>
                        <input type="text" name="token" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>Bank Soal</label>
                        <select name="bank_soal_id" id="bank_soal_id" class="form-control form-control-sm">
                            <option value="">Pilih Bank Soal</option>
                            <?php foreach ($banks as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= esc($b['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label>Jenis Ujian</label>
                        <select name="jenis_ujian_id" class="form-control form-control-sm" required>
                            <option value="">Pilih Jenis Ujian</option>
                            <?php foreach ($jenisUjian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Durasi Ujian (menit)</label>
                        <input type="number" name="durasi_ujian" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Minimal Durasi (menit)</label>
                        <input type="number" name="minimal_durasi" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>Dibuat Oleh</label>
                        <select name="created_by" class="form-control form-control-sm">
                            <?php foreach ($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>Status</label>
                        <select name="is_active" class="form-control form-control-sm" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Pengaturan</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="list-group shadow-sm rounded-3">
                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="shuffle" class="me-2 text-primary"></i> Acak Soal</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="acak_soal" value="1" id="acak_soal">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="repeat" class="me-2 text-info"></i> Acak Opsi</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="acak_opsi" value="1" id="acak_opsi">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="key" class="me-2 text-warning"></i> Pakai Token</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="pakai_token" value="1" id="pakai_token">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="bar-chart-2" class="me-2 text-success"></i> Tampilkan Nilai</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="tampil_nilai" value="1" id="tampil_nilai">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="list-group shadow-sm rounded-3">
                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="book-open" class="me-2 text-secondary"></i> Tampilkan Pembahasan</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="tampil_pembahasan" value="1" id="tampil_pembahasan">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="video" class="me-2 text-danger"></i> Pakai Webcam</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="pakai_webcam" value="1" id="pakai_webcam">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="log-in" class="me-2 text-dark"></i> Single Login Device</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="single_login" value="1" id="single_login">
                                        </div>
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i data-feather="lock" class="me-2 text-dark"></i> Perangkat Terkunci</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="perangkat_terkunci" value="1" id="perangkat_terkunci">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="save()" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header">
                <h5 class="modal-title" id="ujianNama"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Nama Ujian
                <h6 id="ujianNama" class="fw-bold mb-3"></h6> -->

                <!-- Kode Ujian -->
                <div class="bg-light rounded p-2 mb-3 position-relative" style="cursor: pointer;" id="copyKodeWrapper">
                    <small class="text-muted">Kode Masuk</small>
                    <div id="ujianKode" class="fs-3 fw-bold text-primary">123456</div>
                    <small id="copyNotif" class="text-success position-absolute top-0 end-0 m-2" style="display:none;">✅ Disalin</small>
                </div>

                <!-- QR Code -->
                <div id="qrCode" class="d-flex justify-content-center mb-3"></div>

                <!-- Link & Salin -->
                <div class="input-group mb-3">
                    <input type="text" id="ujianLink" class="form-control" readonly>
                    <button class="btn btn-outline-primary" id="copyLink">Salin</button>
                </div>

                <!-- Opsi Login -->
                <div class="mb-3 text-start">
                    <label class="form-label d-block mb-1 fw-semibold">Opsi Login</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" id="butuh_login">
                        </div>
                        <span id="opsiLoginLabel" class="small text-muted">Peserta tidak perlu login</span>
                    </div>
                </div>

                <!-- Opsi Dibagikan -->
                <div class="mb-3 text-start">
                    <label class="form-label d-block mb-1 fw-semibold">Status Ujian</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" id="dibagikan">
                        </div>
                        <span id="opsiBagikanLabel" class="small text-muted">Belum dibagikan</span>
                    </div>
                </div>

                <!-- Tombol Masuk -->
                <a href="#" id="masukLink" class="btn btn-primary w-100">Masuk</a>
            </div>
        </div>
    </div>
</div>


<?php $this->endSection(); ?>
<?php $this->section('pagejs'); ?>
<script src="<?= base_url() ?>src/plugins/src/editors/quill/qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        const $shareModal = $("#shareModal");
        const $butuhLogin = $("#butuh_login");
        const $dibagikan = $("#dibagikan");
        const $opsiLoginLabel = $("#opsiLoginLabel");
        const $opsiBagikanLabel = $("#opsiBagikanLabel");
        // Inisialisasi Tom Select dengan jQuery
        window.bankSelectInstance = new TomSelect('#bank_soal_id', {
            searchField: ['text'],
            placeholder: 'Pilih Bank Soal...',
            allowClear: true
        });
        // --- SIMPAN OPSI ---
        function saveOption(type, value) {
            let ujianId = $shareModal.data("id"); // ambil dari data-id modal
            $.ajax({
                url: "<?= base_url('panel/ujian/update-share') ?>/" + ujianId,
                type: "POST",
                data: {
                    option: type,
                    value: value ? 1 : 0,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(res) {
                    if (!res.status) {
                        alert("❌ Gagal menyimpan opsi!");
                    }
                },
                error: function() {
                    alert("⚠️ Terjadi kesalahan koneksi!");
                }
            });
        }

        // --- TOGGLE butuh_login ---
        $butuhLogin.on("change", function() {
            let checked = $(this).is(":checked");
            $opsiLoginLabel.text(checked ? "Peserta harus login" : "Peserta tidak perlu login");
            saveOption("butuh_login", checked);
        });

        // --- TOGGLE dibagikan ---
        $dibagikan.on("change", function() {
            let checked = $(this).is(":checked");
            $opsiBagikanLabel.text(checked ? "Sudah dibagikan" : "Belum dibagikan");
            saveOption("dibagikan", checked);
        });

        // --- SHARE BUTTON CLICK ---
        $(document).on("click", ".share-btn", function() {
            const nama = $(this).data("nama");
            const kode = $(this).data("kode");
            const link = $(this).data("link");
            const id = $(this).data("id");

            // Set konten awal modal
            $shareModal.data("id", id);
            $("#ujianNama").text(nama);
            $("#ujianKode").text(kode);
            $("#ujianLink").val(link);
            $("#masukLink").attr("href", link);

            // Generate QR baru
            $("#qrCode").html("");
            new QRCode(document.getElementById("qrCode"), {
                text: link,
                width: 150,
                height: 150
            });

            // 🔥 Ambil data dari list yang sama dengan edit-btn
            $.get("<?= base_url('panel/ujian/list') ?>", function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    // Set opsi sesuai database
                    $butuhLogin.prop("checked", item.butuh_login == "1");
                    $opsiLoginLabel.text(
                        item.butuh_login == "1" ? "Peserta harus login" : "Peserta tidak perlu login"
                    );

                    $dibagikan.prop("checked", item.dibagikan == "1");
                    $opsiBagikanLabel.text(
                        item.dibagikan == "1" ? "Sudah dibagikan" : "Belum dibagikan"
                    );
                }
            });

            // Tampilkan modal
            $shareModal.modal("show");
        });


        // --- SALIN LINK ---
        $("#copyLink").on("click", function() {
            const linkInput = $("#ujianLink")[0];
            const btn = $(this);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(linkInput.value).then(() => {
                    btn.text("✅ Disalin");
                    setTimeout(() => btn.text("Salin"), 2000);
                }).catch(() => fallbackCopy(linkInput, btn));
            } else {
                fallbackCopy(linkInput, btn);
            }

            function fallbackCopy(input, button) {
                input.select();
                input.setSelectionRange(0, 99999); // untuk mobile
                try {
                    const success = document.execCommand("copy");
                    if (success) {
                        button.text("✅ Disalin");
                        setTimeout(() => button.text("Salin"), 2000);
                    } else {
                        alert("Gagal menyalin. Silakan salin manual.");
                    }
                } catch (err) {
                    alert("Gagal menyalin. Silakan salin manual.");
                }
            }
        });

        // --- SALIN KODE ---
        $("#copyKodeWrapper").on("click", function() {
            const kode = $("#ujianKode").text().trim();
            const copyNotif = $("#copyNotif");
            navigator.clipboard.writeText(kode).then(() => {
                copyNotif.show();
                setTimeout(() => copyNotif.hide(), 1500);
            });
        });
    });
</script>


<script>
    const base_url = '<?= base_url() ?>';
    let save_method = 'add';
    let allData = [];
    let currentPage = 1;
    let perPage = 5;
    let selectedUjianIds = [];


    $(document).ready(function() {
        loadData();


        $('#refreshList').on('click', function() {
            loadData();
        });

        $('#searchBox').on('input', function() {
            currentPage = 1;
            renderList();
        });
        // Event ketika checkbox/toggle "Hari ini" berubah
        $('#filterToday').on('change', function() {
            currentPage = 1;
            renderList();

        });
        $('#perPageSelect').on('change', function() {
            const val = $(this).val();
            if (val === 'all') {
                perPage = allData.length; // tampilkan semua
            } else {
                perPage = parseInt(val);
            }
            currentPage = 1;
            renderList();
        });
    });

    function loadData() {
        $.getJSON(`${base_url}panel/ujian/list`, function(res) {
            allData = res.data || [];
            currentPage = 1;
            renderList();
        });
    }

    function renderList() {
        let search = $('#searchBox').val().toLowerCase();
        const filterToday = $('#filterToday').is(':checked');
        const today = new Date();
        const todayStr = today.toLocaleDateString('sv-SE'); // ✅ lokal yyyy-mm-dd

        let filtered = allData.filter(item => {
            let namaUjian = item.nama_ujian.toLowerCase();
            let bankSoal = item.nama_bank_soal.toLowerCase();

            // Konversi tanggal ke Date object
            let mulai = item.waktu_mulai ? new Date(item.waktu_mulai) : null;
            let selesai = item.waktu_selesai ? new Date(item.waktu_selesai) : null;

            // Filter Hari Ini
            if (filterToday && (mulai && selesai)) {
                let mulaiStr = item.waktu_mulai.slice(0, 10); // yyyy-mm-dd
                let selesaiStr = item.waktu_selesai.slice(0, 10);
                let matchDate = (todayStr >= mulaiStr && todayStr <= selesaiStr);

                if (!matchDate) return false;
            }

            // Format tanggal ke bentuk user-friendly
            let formatterFull = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            let formatterShort = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            let waktuMulaiStr = mulai ? (
                item.waktu_mulai.toLowerCase() + " " +
                formatterFull.format(mulai).toLowerCase() + " " +
                formatterShort.format(mulai).toLowerCase()
            ) : "";

            let waktuSelesaiStr = selesai ? (
                item.waktu_selesai.toLowerCase() + " " +
                formatterFull.format(selesai).toLowerCase() + " " +
                formatterShort.format(selesai).toLowerCase()
            ) : "";

            // Filter berdasarkan search
            return namaUjian.includes(search) ||
                bankSoal.includes(search) ||
                waktuMulaiStr.includes(search) ||
                waktuSelesaiStr.includes(search);
        });
        let start = (currentPage - 1) * perPage;
        let paginated = filtered.slice(start, start + perPage);

        let html = '';
        if (paginated.length === 0) {
            html = `<div class="alert alert-light text-center">Tidak ada data ujian</div>`;
        } else {
            paginated.forEach(row => {
                let collapseId = `collapseUjian${row.id}`;

                html += `
      <div class="card interactive-card shadow-sm border-0 rounded-3 p-3 mb-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
          <div>
            <h5 class="mb-1 fw-semibold">${row.nama_ujian}</h5>
            <p class="mb-1 text-muted small">
              Bank Soal: <span class="fw-medium">${row.nama_bank_soal}</span>
              Jenis Ujian: <span class="fw-medium">${row.nama_jenis_ujian}</span>
            </p>
            
           <!-- Status & Waktu (clean minimalis dengan animasi hover) -->
            <div class="d-flex flex-wrap gap-3 mb-2 text-muted small align-items-center">
            <span class="jadwal-badge ${row.is_active == 1 ? 'text-success fw-semibold' : 'text-secondary'}">
                <i data-feather="circle"></i> ${row.is_active == 1 ? 'Aktif' : 'Nonaktif'}
            </span>

            <span class="jadwal-badge">
                <i data-feather="clock"></i> ${row.durasi_ujian ?? 0} menit
            </span>

            <span class="jadwal-badge text-muted small">
                <i data-feather="calendar" class="me-1"></i>
                ${formatDateClean(row.waktu_mulai)} – ${formatDateClean(row.waktu_selesai)}
            </span>

            <span class="jadwal-badge text-muted small">
                <i data-feather="user" class="me-1"></i> ${row.jumlah_peserta ?? 0} Peserta
            </span>
            </div>



          <!-- Link collapse detail opsi -->
            <a class="text-muted small fw-medium d-inline-flex align-items-center gap-1 toggle-opsi" 
                data-bs-toggle="collapse" 
                href="#${collapseId}" 
                role="button" 
                aria-expanded="false">
                <span>Opsi lainnya</span>
                <i data-feather="chevron-down" class="icon-toggle"></i>
            </a>

                </div>
                
                
           <!-- Card body ... -->
            <div class="d-flex flex-column justify-content-between">

            <!-- Konten utama card -->
            <div>
                <!-- Judul, bank soal, status, dll -->
            </div>

            <!-- Aksi minimalis di bawah -->
            <div class="aksi-icons d-flex align-items-center gap-2 mt-3 justify-content-end">
                <!-- Tambah Peserta -->
                <a href="javascript:void(0)" class="text-success tambah-peserta-btn" 
                    data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#tambahPesertaModal"
                    title="Tambah Peserta">
                    <i data-feather="user-plus"></i>
                </a>

                <!-- Edit -->
                <a href="javascript:void(0)" class="text-primary edit-btn" data-id="${row.id}" title="Edit">
                    <i data-feather="edit"></i>
                </a>

                <!-- Hapus -->
                <a href="javascript:void(0)" class="text-danger delete-btn" data-id="${row.id}" title="Hapus">
                    <i data-feather="trash-2"></i>
                </a>

                <!-- Detail -->
                <a href="${base_url}panel/ujian/detail/${row.id}" class="text-secondary" title="Detail">
                    <i data-feather="info"></i>
                </a>
                <!-- Bagikan -->
                <a href="javascript:void(0)" 
                    class="text-warning share-btn" 
                    data-id="${row.id}" 
                    data-nama="${row.nama_ujian}" 
                    data-kode="${row.kode_ujian}" 
                    data-link="${base_url}share/ujian/play/${row.token}" 
                    title="Bagikan">
                    <i data-feather="share-2"></i>
                </a>

            </div>

            </div>



        </div>

        <!-- Collapse Detail Opsi (ikon minimalis aktif = warna, nonaktif = abu2) -->
            <div class="collapse mt-3" id="${collapseId}">
            <div class="d-flex flex-wrap gap-2">

                ${row.single_login == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Single Login">
                    <i data-feather="log-in"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Single Login">
                    <i data-feather="log-in"></i> <small>Nonaktif</small>
                </span>`}

                ${row.pakai_token == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Pakai Token">
                    <i data-feather="key"></i> <small>Aktif (${row.token})</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Pakai Token">
                    <i data-feather="key"></i> <small>Nonaktif</small>
                </span>`}

                ${row.acak_soal == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Acak Soal">
                    <i data-feather="shuffle"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Acak Soal">
                    <i data-feather="shuffle"></i> <small>Nonaktif</small>
                </span>`}

                ${row.acak_opsi == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Acak Opsi">
                    <i data-feather="repeat"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Acak Opsi">
                    <i data-feather="repeat"></i> <small>Nonaktif</small>
                </span>`}

                ${row.tampil_nilai == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Tampilkan Nilai">
                    <i data-feather="bar-chart-2"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Tampilkan Nilai">
                    <i data-feather="bar-chart-2"></i> <small>Nonaktif</small>
                </span>`}

                ${row.tampil_pembahasan == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Tampilkan Pembahasan">
                    <i data-feather="book-open"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Tampilkan Pembahasan">
                    <i data-feather="book-open"></i> <small>Nonaktif</small>
                </span>`}

                ${row.pakai_webcam == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Pakai Webcam">
                    <i data-feather="video"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Pakai Webcam">
                    <i data-feather="video"></i> <small>Nonaktif</small>
                </span>`}

                ${row.perangkat_terkunci == '1' 
                ? `<span class="badge rounded-pill bg-success px-3 py-2 d-flex align-items-center gap-1" title="Perangkat Terkunci">
                    <i data-feather="lock"></i> <small>Aktif</small>
                </span>`
                : `<span class="badge rounded-pill bg-secondary px-3 py-2 d-flex align-items-center gap-1" title="Perangkat Terkunci">
                    <i data-feather="lock"></i> <small>Nonaktif</small>
                </span>`}

            </div>
            </div>

      </div>`;
            });
        }

        $('#ujianList').html(html);

        // Tambah interaksi klik pada card
        $('.card.interactive-card').on('click', function(e) {
            // Hindari klik pada tombol aksi
            if ($(e.target).closest('.aksi-icons, a, button, .collapse').length > 0) return;

            const id = $(this).find('.edit-btn').data('id');

            // Toggle pilih / batal
            if (selectedUjianIds.includes(id)) {
                // 🔹 Batalkan pilihan
                selectedUjianIds = selectedUjianIds.filter(x => x !== id);
                $(this).removeClass('selected-card border-primary shadow-lg');
                $(this).find('.selected-overlay').remove();
            } else {
                // 🔹 Tambahkan pilihan
                selectedUjianIds.push(id);
                $(this).addClass('selected-card border-primary shadow-lg');
                if ($(this).find('.selected-overlay').length === 0) {
                    $(this).append(`
                <div class="selected-overlay position-absolute top-0 end-0 bg-primary text-white small px-2 py-1 rounded-start">
                    Dipilih
                </div>
            `);
                }
            }

            // 🔹 Update badge jumlah terpilih di tombol pengaturan (jika ada)
            updateSelectedCount();

            // 🔹 Aktifkan / nonaktifkan tombol pengaturan
            if (selectedUjianIds.length > 0) {
                $('#btnAturMassal').prop('disabled', false);
            } else {
                $('#btnAturMassal').prop('disabled', true);
            }
        });



        feather.replace();
        renderPagination(filtered.length);

    }
    $('#modalPengaturanUjian').on('show.bs.modal', function() {
        const container = $('#listUjianSimple');
        container.empty();

        // Reset input fields
        $('#waktuMulai').val('');
        $('#waktuSelesai').val('');
        $('#tokenAktif').prop('checked', false);
        $('#ubahTokenMassal').prop('checked', false);
        $('#tokenMassal').val('');
        $('#inputTokenMassalContainer').hide();

        if (selectedUjianIds.length === 0) {
            container.html(`<div class="alert alert-light text-center small">Belum ada ujian dipilih</div>`);
            return;
        }

        selectedUjianIds.forEach(id => {
            const ujian = allData.find(u => u.id == id);
            if (ujian) {
                container.append(`
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong>${ujian.nama_ujian}</strong><br>
                        <small class="text-muted">${ujian.nama_bank_soal}</small>
                    </span>
                   
                </div>
            `);
            }
        });
    });

    // Toggle input token massal
    $('#ubahTokenMassal').on('change', function() {
        if ($(this).is(':checked')) {
            $('#inputTokenMassalContainer').slideDown();
            if (!$('#tokenMassal').val()) {
                $('#tokenMassal').val(generateToken());
            }
        } else {
            $('#inputTokenMassalContainer').slideUp();
        }
    });

    // Generate token massal
    $('#btnGenerateTokenMassal').on('click', function() {
        $('#tokenMassal').val(generateToken());
    });

    $('#btnSimpanPengaturan').on('click', function() {
        if (selectedUjianIds.length === 0) {
            Swal.fire('Belum ada ujian dipilih', '', 'warning');
            return;
        }

        const payload = {
            ujian_ids: selectedUjianIds,
            waktu_mulai: $('#waktuMulai').val(),
            waktu_selesai: $('#waktuSelesai').val(),
            token_aktif: $('#tokenAktif').is(':checked') ? 1 : 0,
            ubah_token: $('#ubahTokenMassal').is(':checked') ? 1 : 0,
            token: $('#tokenMassal').val()
        };

        Swal.fire({
            title: 'Simpan Pengaturan?',
            text: `Akan diterapkan ke ${selectedUjianIds.length} ujian.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
        }).then(res => {
            if (res.isConfirmed) {
                $.ajax({
                    url: `${base_url}panel/ujian/updateMassal`,
                    method: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: (res) => {
                        Swal.fire('Berhasil!', 'Pengaturan berhasil disimpan.', 'success');
                        $('#modalPengaturanUjian').modal('hide');

                        // 🔹 Hapus semua tanda pilihan di UI
                        selectedUjianIds = []; // kosongkan array ujian terpilih
                        $('.interactive-card')
                            .removeClass('selected-card border-primary shadow-lg')
                            .find('.selected-overlay').remove();
                        updateSelectedCount();
                        // 🔹 Refresh data ujian
                        loadData();
                    },

                    error: (xhr) => {
                        let message = 'Terjadi kesalahan server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal', message, 'error');
                    }
                });
            }
        });
    });

    $('#btnExportBelumUjian').on('click', function() {
        if (selectedUjianIds.length === 0) {
            Swal.fire('Belum ada ujian dipilih', '', 'warning');
            return;
        }

        Swal.fire({
            title: 'Export Data?',
            text: `Data peserta belum ujian untuk ${selectedUjianIds.length} ujian akan diekspor.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Export',
        }).then(res => {
            if (res.isConfirmed) {
                const url = `${base_url}panel/ujian/exportBelumUjian?ids=${selectedUjianIds.join(',')}`;
                window.open(url, '_blank');
            }
        });
    });


    function updateSelectedCount() {
        const count = selectedUjianIds.length;
        const btn = $('[data-bs-target="#modalPengaturanUjian"]');

        if (count > 0) {
            // 🔹 Aktifkan tombol dan tampilkan badge jumlah terpilih
            btn.prop('disabled', false)
                .html(`<i data-feather="settings"></i> Pengaturan Massal <span class="badge bg-primary ms-1">${count}</span>`);
        } else {
            // 🔹 Nonaktifkan tombol dan hapus badge
            btn.prop('disabled', true)
                .html(`<i data-feather="settings"></i> Pengaturan Massal`);
        }

        // 🔹 Refresh ikon feather
        feather.replace();
    }


    function formatDateClean(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);

        const day = String(date.getDate()).padStart(2, '0');
        const month = date.toLocaleString('id-ID', {
            month: 'short'
        });
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const mins = String(date.getMinutes()).padStart(2, '0');

        return `${day} ${month} ${year}, ${hours}:${mins}`;
    }



    function renderPagination(total) {
        let totalPages = Math.ceil(total / perPage);
        let html = '';

        if (totalPages > 1) {
            // tombol Prev
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="gotoPage(${currentPage - 1})" ${currentPage === 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                        <i data-feather="chevron-left"></i>
                    </a>
                 </li>`;

            if (totalPages <= 5) {
                // kalau total halaman sedikit, tampilkan semua
                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="gotoPage(${i})">${i}</a>
                         </li>`;
                }
            } else {
                // halaman pertama
                html += `<li class="page-item ${1 === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="gotoPage(1)">1</a>
                     </li>`;

                // ellipsis kiri
                if (currentPage > 3) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                // halaman sekitar current
                let start = Math.max(2, currentPage - 1);
                let end = Math.min(totalPages - 1, currentPage + 1);

                for (let i = start; i <= end; i++) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="gotoPage(${i})">${i}</a>
                         </li>`;
                }

                // ellipsis kanan
                if (currentPage < totalPages - 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                // halaman terakhir
                html += `<li class="page-item ${totalPages === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="gotoPage(${totalPages})">${totalPages}</a>
                     </li>`;
            }

            // tombol Next
            html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="gotoPage(${currentPage + 1})" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                        <i data-feather="chevron-right"></i>
                    </a>
                 </li>`;
        }

        $('#pagination').html(html);

        // render feather icons
        if (window.feather) {
            feather.replace();
        }
    }


    function gotoPage(page) {
        currentPage = page;
        renderList();
    }
    // TOMBOL EDIT
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');

        $.get('<?= base_url('panel/ujian/list') ?>', function(res) {
            const item = res.data.find(i => i.id == id);
            if (item) {
                $('[name="id"]').val(item.id);
                $('[name="nama_ujian"]').val(item.nama_ujian);
                $('[name="deskripsi"]').val(item.deskripsi);
                $('[name="kode_ujian"]').val(item.kode_ujian);
                $('[name="token"]').val(item.token);
                $('[name="durasi_ujian"]').val(item.durasi_ujian);
                $('[name="minimal_durasi"]').val(item.minimal_durasi);
                $('[name="waktu_mulai"]').val(item.waktu_mulai);
                $('[name="waktu_selesai"]').val(item.waktu_selesai);
                $('[name="is_active"]').val(item.is_active);
                $('[name="jenis_ujian_id"]').val(item.jenis_ujian_id);
                $('[name="acak_soal"]').prop('checked', item.acak_soal == '1');
                $('[name="acak_opsi"]').prop('checked', item.acak_opsi == '1');
                $('[name="pakai_token"]').prop('checked', item.pakai_token == '1');
                $('[name="tampil_nilai"]').prop('checked', item.tampil_nilai == '1');
                $('[name="tampil_pembahasan"]').prop('checked', item.tampil_pembahasan == '1');
                $('[name="pakai_webcam"]').prop('checked', item.pakai_webcam == '1');
                $('[name="perangkat_terkunci"]').prop('checked', item.perangkat_terkunci == '1');
                $('[name="single_login"]').prop('checked', item.single_login == '1');
                $('[name="created_by"]').val(item.created_by);

                // ======== Bagian Bank Soal untuk Tom Select ========
                if (window.bankSelectInstance) {
                    window.bankSelectInstance.setValue(item.bank_soal_id);
                }

                $('.modal-title').text('Edit Ujian');
                $('#modal_form').modal('show');
                save_method = 'edit';
            }
        });
    });


    // TOMBOL DELETE
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin hapus ujian ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/ujian/delete/') ?>${id}`, function(res) {
                    if (res.status) {
                        loadData(); // refresh list card
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    } else {
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }
                }, 'json');
            }
        });
    });

    // TOMBOL TAMBAH PESERTA
    // $(document).on('click', '.tambah-peserta-btn', function() {
    //     const ujianId = $(this).data('id');
    //     $('#tambahPesertaModal [name="ujian_id"]').val(ujianId);
    //     $('#tambahPesertaModal').modal('show');
    // });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();

        $('[name="token"]').val(generateToken());
        $('[name="kode_ujian"]').val(generateKodeUjian());

        // ===== Kosongkan Bank Soal =====
        if (window.bankSelectInstance) {
            window.bankSelectInstance.clear();
        }

        $('.modal-title').text('Tambah Ujian');
        $('#modal_form').modal('show');
    }

    const btnGenerate = document.getElementById('btnGenerateKode');
    // klik tombol generate
    btnGenerate.addEventListener('click', function() {
        $('[name="kode_ujian"]').val(generateKodeUjian());
    });

    function generateKodeUjian(length = 6) {
        let code = '';
        for (let i = 0; i < length; i++) {
            code += Math.floor(Math.random() * 10); // angka 0-9
        }
        return code;
    }

    function generateToken(length = 6) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return Array.from({
            length
        }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    }


    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/ujian/create') ?>' :
            '<?= base_url('panel/ujian/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        let formData = $('#form').serializeArray();

        const checkboxNames = [
            'acak_soal',
            'acak_opsi',
            'pakai_token',
            'tampil_nilai',
            'tampil_pembahasan',
            'pakai_webcam',
            'perangkat_terkunci',
            'single_login'
        ];

        checkboxNames.forEach(name => {
            formData = formData.filter(f => f.name !== name);
            formData.push({
                name: name,
                value: $(`[name="${name}"]`).is(':checked') ? '1' : '0'
            });
        });

        $.post(url, $.param(formData), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');

                // Refresh ulang data & render list
                loadData();

                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => {
                    errors += `<li>${v}</li>`;
                });
                errors += '</ul></div>';
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }
</script>


<script>
    $(document).ready(function() {
        // Trigger awal dan saat filter berubah
        $('#filter_tingkat, #filter_kelas, #filter_jurusan, #filter_agama').on('change', loadPesertaBelumDitambahkan);

        // Saat modal dibuka, inisialisasi ID ujian dan load peserta
        $('#tambahPesertaModal').on('show.bs.modal', function(e) {
            const ujianId = $(e.relatedTarget).data('id');
            $('[name="ujian_id"]').val(ujianId);
            loadPesertaBelumDitambahkan();
            loadPesertaSudahDitambahkan(ujianId);
        });

        $('#btn-tambah').click(function() {
            $('#list-belum option:selected').each(function() {
                const cloned = $(this).clone().prop('selected', true); // ✅ auto selected
                $('#list-sudah').append(cloned);
                $(this).remove();
            });
            updateCounts(); // ✅ di sini
        });


        $('#btn-hapus').click(function() {
            const selected = $('#list-sudah option:selected');
            const ujianId = $('[name="ujian_id"]').val();

            if (selected.length === 0) return;

            const pesertaIds = [];
            selected.each(function() {
                pesertaIds.push($(this).val());
            });

            // Kirim ke server untuk cek apakah peserta-peserta itu sudah tercatat di hasil_ujian
            $.post('<?= base_url('panel/ujian/cek-peserta-diuji') ?>', {
                ujian_id: ujianId,
                peserta_id: pesertaIds
            }, function(res) {
                if (!res.status) {
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                    return;
                }

                const {
                    sudahAda,
                    belumAda
                } = res.data;

                // Kalau belum ada, langsung pindah
                belumAda.forEach(id => {
                    const opt = $('#list-sudah option[value="' + id + '"]');
                    $('#list-belum').append(opt.clone());
                    opt.remove();
                    updateCounts(); // ✅ Update jumlah
                });

                // Kalau sudah ada, konfirmasi dulu
                if (sudahAda.length > 0) {
                    if (confirm('Beberapa peserta sudah memiliki data hasil ujian. Menghapus mereka akan menghapus seluruh nilai. Lanjutkan?')) {
                        $.post('<?= base_url('panel/ujian/remove-peserta') ?>', {
                            ujian_id: ujianId,
                            peserta_id: sudahAda
                        }, function(resRemove) {
                            if (resRemove.status) {
                                sudahAda.forEach(id => {
                                    const opt = $('#list-sudah option[value="' + id + '"]');
                                    $('#list-belum').append(opt.clone());
                                    opt.remove();
                                    updateCounts(); // ✅ Update jumlah
                                });
                                Snackbar.show({
                                    text: resRemove.message,
                                    pos: 'top-center'
                                });
                            } else {
                                Snackbar.show({
                                    text: resRemove.message,
                                    pos: 'top-center'
                                });
                            }
                        }, 'json');
                    }
                }
            }, 'json');
        });

        $("#form input[name='waktu_selesai']").on("change", function() {
            let mulaiVal = $("#form input[name='waktu_mulai']").val();
            let selesaiVal = $(this).val();

            if (mulaiVal && selesaiVal) {
                let start = new Date(mulaiVal);
                let end = new Date(selesaiVal);

                if (end <= start) {
                    $("#errorMessages").html(`
                    <div class="alert alert-danger py-2 px-3 mb-2 small">
                        <i data-feather="alert-triangle"></i>
                        Tanggal & waktu <b>Selesai</b> harus lebih besar dari <b>Mulai</b>.
                    </div>
                `);
                    feather.replace();
                    $(this).val(""); // reset kalau salah
                } else {
                    $("#errorMessages").html(""); // hapus pesan error
                }
            }
        });
        // Submit form peserta ujian
        $('#formTambahPeserta').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post('<?= base_url('panel/ujian/add-peserta') ?>', formData, function(res) {
                if (res.status) {
                    $('#tambahPesertaModal').modal('hide');
                    loadData();
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                } else {
                    Snackbar.show({
                        text: 'Gagal menyimpan data peserta.',
                        pos: 'top-center'
                    });
                }
            }, 'json');
        });
    });

    function loadPesertaBelumDitambahkan() {
        const ujianId = $('[name="ujian_id"]').val();
        const data = {
            tingkat_id: $('#filter_tingkat').val(),
            kelas_id: $('#filter_kelas').val(),
            jurusan_id: $('#filter_jurusan').val(),
            agama_id: $('#filter_agama').val()
        };

        if (!ujianId) return;

        $.get(`<?= base_url('panel/ujian/filter-peserta') ?>/${ujianId}`, data, function(res) {
            $('#list-belum').empty();
            if (res.status) {
                $.each(res.data, function(i, peserta) {
                    $('#list-belum').append(`<option value="${peserta.id}">${peserta.nama} (${peserta.nisn})</option>`);
                });
            }
            updateCounts(); // ✅ di sini
        }, 'json');
    }


    // Ambil peserta yang sudah ditambahkan ke ujian
    function loadPesertaSudahDitambahkan(ujianId) {
        $.get(`<?= base_url('panel/ujian/peserta-ujian') ?>/${ujianId}`, function(res) {
            $('#list-sudah').empty();
            if (res.status) {
                $.each(res.data, function(i, peserta) {
                    $('#list-sudah').append(
                        `<option value="${peserta.id}" selected>${peserta.nama} (${peserta.nisn})</option>`
                    );
                });
            }
            updateCounts(); // ✅ di sini
        }, 'json');
    }

    function updateCounts() {
        $('#count-belum').text($('#list-belum option').length);
        $('#count-sudah').text($('#list-sudah option').length);
    }
</script>

<?php $this->endSection(); ?>