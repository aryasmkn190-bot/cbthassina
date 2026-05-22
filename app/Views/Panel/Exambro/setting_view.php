<?= $this->extend('Layout/main'); ?>
<?= $this->section('css'); ?>
<link href="<?= base_url() ?>/src/plugins/css/light/bootstrap-range-Slider/bootstrap-slider.css" rel="stylesheet" type="text/css">
<link href="<?= base_url() ?>/src/plugins/src/pickr/monolith.min.css" rel="stylesheet" type="text/css">
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <form class="form-horizontal" id="settings-form">
                    <div class="widget-heading mb-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pengaturan Exambro</h5>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> <span class="btn-text-inner">Simpan</span>
                        </button>
                        <a href="<?= base_url('panel/exambro/setting/download-config') ?>" class="btn btn-primary">
                            <i data-feather="download"></i> <span class="btn-text-inner">Download</span>
                        </a>
                    </div>

                    <div class="widget-content">

                        <!-- Nama Aplikasi -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Aplikasi</label>
                            <div class="col-sm-9">
                                <input type="text" name="app_name" class="form-control form-control-sm" readonly>
                            </div>
                        </div>

                        <!-- Nama Sekolah -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Sekolah</label>
                            <div class="col-sm-9">
                                <input type="text" name="school_name" class="form-control form-control-sm">
                            </div>
                        </div>



                        <!-- Kecerahan Layar -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Kecerahan Layar (%)</label>
                            <div class="col-sm-9">
                                <!-- <input type="number" name="default_brightness" class="form-control form-control-sm" min="0" max="100"> -->
                                <div class="custom-progress progress-up" style="width: 100%">
                                    <!-- Angka awal diubah jadi 80 -->
                                    <div class="range-count">
                                        <span class="range-count-number" id="brightnessValue">80</span>
                                        <span class="range-count-unit">%</span>
                                    </div>

                                    <!-- Slider default diatur ke 80 -->
                                    <input
                                        type="range"
                                        name="default_brightness"
                                        class="form-range progress-range-counter"
                                        id="customRange1"
                                        min="0"
                                        max="100"
                                        value="80">
                                </div>
                            </div>
                        </div>

                        <!-- Volume Aplikasi -->
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Volume Aplikasi (%)</label>
                            <div class="col-sm-6 col-md-9">
                                <div class="custom-progress progress-up" style="width: 100%">
                                    <div class="range-count">
                                        <span class="range-count-number" id="volumeValue">50</span>
                                        <span class="range-count-unit">%</span>
                                    </div>
                                    <input
                                        type="range"
                                        name="app_volume"
                                        class="form-range progress-range-counter"
                                        id="appVolumeRange"
                                        min="0"
                                        max="100"
                                        value="50">
                                </div>
                            </div>
                        </div>
                        <!-- Menu URL -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tampilkan Menu URL</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="menu_url" id="menu_url">
                                    <label class="form-check-label" for="menu_url">Aktifkan</label>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Scan QR -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tampilkan Menu Scan QR</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="menu_scanqr" id="menu_scanqr">
                                    <label class="form-check-label" for="menu_scanqr">Aktifkan</label>
                                </div>
                            </div>
                        </div>

                        <!-- Bluetooth -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Blokir Bluetooth</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="bluetooth" id="bluetooth">
                                    <label class="form-check-label" for="bluetooth">Aktifkan</label>
                                </div>
                            </div>
                        </div>

                        <!-- Headset -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Blokir Headset</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="headset" id="headset">
                                    <label class="form-check-label" for="headset">Aktifkan</label>
                                </div>
                            </div>
                        </div>

                        <!-- Password Exit -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Password Keluar</label>
                            <div class="col-sm-9">
                                <input type="text" name="password_exit" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Secret Code -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Secret Code</label>
                            <div class="col-sm-9 d-flex">
                                <input type="text" name="secret_code" id="secret_code" class="form-control form-control-sm me-2" readonly>
                                <button type="button" id="btnGenerateCode" class="btn btn-sm btn-primary">
                                    <i data-feather="refresh-cw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Hanya Buka Di Exambro</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="restrict_user_agent" id="restrict_user_agent">
                                    <label class="form-check-label" for="restrict_user_agent">Aktifkan</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Portal Login</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="portal_ujian" id="portal_ujian">
                                    <label class="form-check-label" for="portal_ujian">Aktifkan</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Login No Password</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="login_nopassword" id="login_nopassword">
                                    <label class="form-check-label" for="login_nopassword">Aktifkan</label>
                                </div>
                            </div>
                        </div>
                        <!-- Secret Code -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">User Agent Browser</label>
                            <div class="col-sm-9">
                                <input type="text" name="user_agent" class="form-control form-control-sm">
                            </div>
                        </div>
                        <!-- Secret Code -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Color Warna</label>
                            <div class="col-sm-9">
                                <input type="text" name="theme_color" class="form-control form-control-sm color-picker">
                            </div>
                        </div>

                        <!-- Bell Sound -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Bell Sound</label>
                            <div class="col-sm-9">
                                <input type="file" name="bell_sound" class="form-control form-control-sm">
                                <audio id="bellSoundPreview" controls style="margin-top: 5px; display: none;"></audio>
                                <small class="text-muted">File .mp3/.wav max 1MB</small>
                            </div>
                        </div>

                        <!-- Exit Sound -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Exit Sound</label>
                            <div class="col-sm-9">
                                <input type="file" name="exit_sound" class="form-control form-control-sm">
                                <audio id="exitSoundPreview" controls style="margin-top: 5px; display: none;"></audio>
                                <small class="text-muted">File .mp3/.wav max 1MB</small>
                            </div>
                        </div>


                        <!-- Banner Image -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Banner Ujian</label>
                            <div class="col-sm-9">
                                <input type="file" name="banner_img" class="form-control form-control-sm">
                                <small class="text-muted">Format gambar JPG/PNG max 1MB</small><br>
                                <img id="bannerPreview" src="" alt="Banner Preview" style="max-width: 70%; display: none;">
                            </div>
                        </div>

                        <!-- Logo Aplikasi -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Logo Aplikasi</label>
                            <div class="col-sm-9">
                                <input type="file" name="logo_resource" class="form-control form-control-sm">
                                <small class="text-muted">Maksimal ukuran 1MB</small><br>
                                <img id="logoPreview" src="" alt="Logo Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script src="<?= base_url() ?>/src/plugins/src/bootstrap-range-Slider/bootstrap-rangeSlider.js"></script>
<script src="<?= base_url() ?>/src/plugins/src/pickr/pickr.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        const pickr = Pickr.create({
            el: '.color-picker',
            theme: 'monolith', // or 'monolith', or 'nano'

            swatches: [
                'rgba(244, 67, 54, 1)',
                'rgba(233, 30, 99, 0.95)',
                'rgba(156, 39, 176, 0.9)',
                'rgba(103, 58, 183, 0.85)',
                'rgba(63, 81, 181, 0.8)',
                'rgba(33, 150, 243, 0.75)',
                'rgba(3, 169, 244, 0.7)',
                'rgba(0, 188, 212, 0.7)',
                'rgba(0, 150, 136, 0.75)',
                'rgba(76, 175, 80, 0.8)',
                'rgba(139, 195, 74, 0.85)',
                'rgba(205, 220, 57, 0.9)',
                'rgba(255, 235, 59, 0.95)',
                'rgba(255, 193, 7, 1)'
            ],

            components: {

                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    // rgba: true,
                    // hsla: true,
                    // hsva: true,
                    // cmyk: true,
                    input: true,
                    // clear: true,
                    save: true
                }
            }
        });
        pickr.on('save', (color) => {
            const hexColor = color.toHEXA().toString();
            $('.color-picker').val(hexColor);
            pickr.hide();
        });

        $('#customRange1').on('input change', function() {
            $('#brightnessValue').text($(this).val());
        });
        $('#appVolumeRange').on('input change', function() {
            $('#volumeValue').text($(this).val());
        });
        // Ambil data dari server
        $.ajax({
            url: "<?= site_url('panel/exambro/setting/getdata') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                console.log(data);
                $('[name="app_name"]').val(data.app_name);
                $('[name="school_name"]').val(data.school_name);
                $('#menu_url').prop('checked', data.menu_url == 1);
                $('#menu_scanqr').prop('checked', data.menu_scanqr == 1);
                $('#bluetooth').prop('checked', data.bluetooth == 1);
                $('#headset').prop('checked', data.headset == 1);
                $('#restrict_user_agent').prop('checked', data.restrict_user_agent == 1);
                $('#portal_ujian').prop('checked', data.portal_ujian == 1);
                $('#login_nopassword').prop('checked', data.login_nopassword == 1);
                $('#brightnessValue').html(data.default_brightness);
                $('[name="default_brightness"]').val(data.default_brightness);
                $('#volumeValue').html(data.app_volume);
                $('[name="app_volume"]').val(data.app_volume);
                $('[name="password_exit"]').val(data.password_exit);
                $('[name="secret_code"]').val(data.secret_code);
                $('[name="theme_color"]').val(data.theme_color);
                $('[name="user_agent"]').val(data.user_agent);
                pickr.setColor(data.theme_color); // set nilai pickr jika sudah ada
                if (data.banner_img) {
                    $('#bannerPreview').attr('src', '<?= base_url('assets/img/') ?>' + data.banner_img).show();
                }
                if (data.logo_resource) {
                    $('#logoPreview').attr('src', '<?= base_url('assets/img/') ?>' + data.logo_resource).show();
                }

                if (data.bell_sound) {
                    $('#bellSoundPreview').attr('src', '<?= base_url('assets/sound/') ?>' + data.bell_sound).show();
                }
                if (data.exit_sound) {
                    $('#exitSoundPreview').attr('src', '<?= base_url('assets/sound/') ?>' + data.exit_sound).show();
                }

            },
            error: function() {
                alert('Gagal mengambil data dari server.');
            }
        });

        function generateSecretCode(length = 16) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let code = '';
            for (let i = 0; i < length; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return code;
        }

        // Klik tombol generate
        $('#btnGenerateCode').on('click', function() {
            Swal.fire({
                title: 'Ganti Secret Code?',
                text: 'Mengganti secret code akan membuat pengguna harus scan QR Code kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ganti Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    const newCode = generateSecretCode(16);
                    $('#secret_code').val(newCode);

                    // Animasi kecil (opsional)
                    $('#secret_code').addClass('bg-success text-white');
                    setTimeout(() => {
                        $('#secret_code').removeClass('bg-success text-white');
                    }, 600);

                    Swal.fire({
                        icon: 'success',
                        title: 'Kode Baru Dibuat!',
                        text: 'Jangan lupa klik tombol simpan untuk menyimpan pengaturan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
        // Preview file
        $('#logo').change(function() {
            previewFile(this, '#logoPreview');
        });
        $('#banner_img').change(function() {
            previewFile(this, '#bannerPreview');
        });

        function previewFile(input, targetImg) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $(targetImg).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Submit form
        $('#settings-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.set('menu_url', $('#menu_url').is(':checked') ? 1 : 0);
            formData.set('menu_scanqr', $('#menu_scanqr').is(':checked') ? 1 : 0);
            formData.set('bluetooth', $('#bluetooth').is(':checked') ? 1 : 0);
            formData.set('headset', $('#headset').is(':checked') ? 1 : 0);
            formData.set('restrict_user_agent', $('#restrict_user_agent').is(':checked') ? 1 : 0);
            formData.set('portal_ujian', $('#portal_ujian').is(':checked') ? 1 : 0);
            formData.set('login_nopassword', $('#login_nopassword').is(':checked') ? 1 : 0);
            formData.set('theme_color', pickr.getColor().toHEXA().toString());

            $.ajax({
                url: '<?= base_url('panel/exambro/setting/update') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    Snackbar.show({
                        text: response.message,
                        pos: 'top-center'
                    });
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat menyimpan pengaturan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Snackbar.show({
                        text: msg,
                        pos: 'top-center',
                        backgroundColor: '#e74c3c' // merah biar beda dari success
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>