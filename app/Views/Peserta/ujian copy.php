<?= $this->extend('Layout/main_peserta'); ?>
<?= $this->section('css'); ?>
<style>
    .card .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.6em;
    }

    .card .btn {
        border-radius: 8px;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">


        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h4 class="fw-bold mb-0">Daftar Ujian</h4>
            <div class="d-flex align-items-center gap-1 text-muted small" id="datetime">
                <i data-feather="clock"></i>
                <span id="datetime-text"></span>
            </div>
        </div>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <div class="row mt-3" id="ujian-list">
            <!-- Card Ujian akan dimuat di sini lewat AJAX -->
        </div>



    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>

<script>
    function generateUUID() {
        if (window.crypto && crypto.randomUUID) {
            return crypto.randomUUID();
        }

        // Fallback UUIDv4 generator
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    if (!document.cookie.includes('device_id')) {
        document.cookie = "device_id=" + generateUUID() +
            "; path=/; max-age=86400; SameSite=Lax";
    }


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
    feather.replace(); // render feather icons
</script>


<script>
    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
    }
    $(document).ready(function() {
        $.ajax({
            url: "<?= base_url('peserta/ujian/getall') ?>",
            method: "GET",
            dataType: "json",
            success: function(res) {
                const container = $("#ujian-list").empty();
                if (!res.status || res.data.length === 0) {
                    container.html('<div class="col-12 text-center"><p>Tidak ada ujian.</p></div>');
                    return;
                }

                res.data.forEach((ujian) => {
                    const mulai = new Date(ujian.waktu_mulai);
                    const selesai = new Date(ujian.waktu_selesai);
                    let status = '';
                    let button = '';

                    if (ujian.status_peserta === 'selesai') {
                        status = '<span class="badge bg-success mb-2">Sudah Selesai</span>';

                        if (ujian.tampil_nilai == 1) {
                            button = `<button class="btn btn-success lihat-hasil-btn w-100" data-id="${ujian.id}">
                    Lihat Hasil
                  </button>`;
                        } else {
                            button = `<button class="btn btn-outline-success w-100" disabled>
                    Selesai
                  </button>`;
                        }
                    } else if (ujian.status_peserta === 'sedang_mengerjakan') {
                        status = '<span class="badge bg-primary mb-2">Sedang Dikerjakan</span>';

                        const deviceCookie = getCookie('device_id'); // fungsi ambil cookie
                        if (ujian.single_login) {
                            if (ujian.device_id && deviceCookie !== ujian.device_id) {
                                button = '<button class="btn btn-danger w-100" disabled>Perangkat Berbeda</button>';
                            } else {
                                button = `<button class="btn btn-warning w-100 lanjutkan-ujian-btn" data-id="${ujian.id}">
                                            Lanjutkan Ujian
                                        </button>`;
                            }
                        } else {
                            button = `<button class="btn btn-warning w-100 lanjutkan-ujian-btn" data-id="${ujian.id}">
                                            Lanjutkan Ujian
                                        </button>`;

                        }
                    } else if (ujian.status_waktu === 'belum_mulai') {
                        status = '<span class="badge bg-warning mb-2">Belum Dimulai</span>';
                        button = '<button class="btn btn-outline-secondary w-100" disabled>Belum Mulai</button>';

                    } else if (ujian.status_waktu === 'dibuka') {
                        status = '<span class="badge bg-success mb-2">Sedang Dibuka</span>';

                        // Cek jika device_id sudah tersimpan (misalnya sebagai penanda sudah pernah mulai ujian)
                        if (ujian.device_id) {
                            button = '<button class="btn btn-outline-dark w-100" disabled>Perangkat Sudah Dipakai</button>';
                        } else {
                            button = `<button class="btn btn-primary w-100 mulai-ujian-btn" data-id="${ujian.id}" data-pakai-token="${ujian.pakai_token}">
                        Mulai Ujian
                    </button>`;
                        }

                    } else if (ujian.status_waktu === 'terlambat') {
                        status = '<span class="badge bg-danger mb-2">Terlambat</span>';
                        button = '<button class="btn btn-outline-danger w-100" disabled>Terlambat</button>';
                    }





                    const widget = `
                                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 layout-spacing">
                                        <div class="widget widget-t-sales-widget widget-m-sales">
                                            <div class="media">
                                                <div class="icon ml-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-book">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5V4.5z"></path>
                                                    </svg>
                                                </div>
                                                <div class="media-body">
                                                    <p class="widget-text fw-bold text-primary mb-1">${ujian.nama_bank_soal}</p>
                                                    <p class="widget-numeric-value"><strong>${ujian.kode_ujian}</strong></p>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column w-bottom mt-2 text-center">

                                               

                                                <div class="d-flex justify-content-between text-muted small px-2 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="calendar" class="me-1"></i>
                                                        ${mulai.toLocaleDateString()} - ${mulai.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="clock" class="me-1"></i>
                                                        ${selesai.toLocaleDateString()} - ${selesai.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-info">
                                                        Durasi: ${ujian.durasi_ujian} Menit
                                                    </span>
                                                    ${status}
                                                </div>

                                                    ${button}
                                              
                                            </div>


                                        </div>
                                    </div>`;


                    container.append(widget);
                });
                feather.replace(); // panggil setelah elemen baru dimasukkan
            },
            error: function() {
                $("#ujian-list").html('<div class="col-12 text-center"><p class="text-danger">Gagal memuat data ujian.</p></div>');
            }
        });
        $(document).on('click', '.lanjutkan-ujian-btn', function() {
            const ujianId = $(this).data('id');
            window.location.href = `<?= base_url('peserta/ujian/lanjut/') ?>${ujianId}`;
        });
        $(document).on('click', '.lihat-hasil-btn', function() {
            const ujianId = $(this).data('id');
            window.location.href = `<?= base_url('peserta/ujian/hasil/') ?>${ujianId}`;
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
                    preConfirm: (inputToken) => {
                        if (!inputToken || inputToken.trim() === '') {
                            Swal.showValidationMessage('Token tidak boleh kosong.');
                            return false;
                        }

                        return $.ajax({
                            url: `<?= base_url('peserta/ujian/cektoken') ?>`,
                            method: 'POST',
                            data: {
                                ujian_id: id,
                                token: inputToken
                            },
                            dataType: 'json'
                        }).then(res => {
                            if (!res.status) {
                                throw new Error(res.message || 'Token salah.');
                            }
                            return true; // penting!
                        }).catch(error => {
                            Swal.showValidationMessage(error.message);
                        });
                    }

                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `<?= base_url('peserta/ujian/mulai/') ?>${id}`;
                    }
                });
            } else {
                window.location.href = `<?= base_url('peserta/ujian/mulai/') ?>${id}`;
            }
        });

    });
</script>


<?= $this->endSection(); ?>