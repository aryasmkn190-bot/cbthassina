<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?= $title ?> </title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>assets/img/favicon.ico" />
    <link href="<?= base_url() ?>layouts/collapsible-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/collapsible-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url() ?>layouts/collapsible-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            /* range weight yang tersedia */
            font-style: normal;
        }
    </style>
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>layouts/collapsible-menu/css/light/plugins.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/light/authentication/auth-cover.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>layouts/collapsible-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/dark/authentication/auth-cover.css" rel="stylesheet" type="text/css" />

    <!-- toastr -->
    <link href="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/light/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/dark/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <style>
        .login-card {
            animation: fadeInUp 0.8s ease;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .login-card h2 {
            font-weight: bold;
            color: #6c63ff;
        }

        .btn-secondary {
            background-color: #6c63ff;
            border-color: #6c63ff;
            transition: 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #554ae0;
            border-color: #554ae0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #e4e9f0);
        }

        body.form {
            background: linear-gradient(135deg, #dff7ff, #ffffff);
            overflow: hidden;
            position: relative;
        }

        /* Geometri lembut sebagai dekorasi */
        .circle-1,
        .circle-2 {
            position: absolute;
            border-radius: 50%;
            z-index: 0;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: rgba(0, 149, 255, 0.1);
            top: -100px;
            left: -100px;
        }

        .circle-2 {
            width: 600px;
            height: 600px;
            background: rgba(255, 193, 7, 0.08);
            bottom: -200px;
            right: -150px;
        }

        /* Tambahkan efek semi transparan ke sisi kiri background */
        .auth-cover-bg-image {
            background: url('<?= base_url() ?>src/assets/img/app_logo.png');
            background-size: cover;
            opacity: 0.1;
        }

        .auth-cover img {
            opacity: 0.6;
        }

        .auth-cover h2 {
            font-size: 1.8rem;
        }

        .auth-cover p {
            font-size: 1rem;
        }

        /* Tambahkan bayangan lembut ke card login */
        .card {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.07);
            border-radius: 16px;
        }
    </style>


</head>

<body class="form">

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->
    <!-- Dekorasi global -->
    <div class="circle-1"></div>
    <div class="circle-2"></div>

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">

            <div class="row">

                <div class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                    <div class="auth-cover-bg-image"></div>
                    <div class="auth-overlay"></div>

                    <div class="auth-cover">

                        <div class="position-relative">

                            <img src="<?= base_url('assets/img/') . $setting->logo ?>" alt="auth-img">

                            <h2 class="mt-5 text-white font-weight-bolder px-2">Siap Ujian, Siap Masa Depan</h2>
                            <p class="text-white px-2">Exam Browser bantu kamu ujian tanpa distraksi. Gak bisa buka aplikasi lain, jadi fokus ngerjain soal aja. Simpel, aman, dan anti-curang.</p>
                        </div>

                    </div>

                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                    <div class="card login-card">
                        <div class="card-body p-4">

                            <form id="loginForm" class="needs-validation" novalidate>
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold text-primary">Masuk ke Akun</h2>
                                    <p class="text-muted mb-0">Akses soal dan mulai belajar lebih fokus.</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Username atau Email</label>
                                    <input type="text" name="identity" class="form-control rounded-pill" required>
                                    <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control rounded-pill" required>
                                    <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Ingat saya</label>
                                </div>

                                <button type="submit" class="btn btn-secondary w-100 rounded-pill mb-3">Masuk</button>

                                <!-- <div class="text-center">
                                    <p class="mb-0">Belum punya akun? <a href="#" class="text-decoration-none text-warning">Daftar</a></p>
                                </div> -->
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- toastr -->
    <script src="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.js"></script>


    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <script>
        // Validasi Bootstrap
        (function() {
            'use strict'
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();

        // jQuery AJAX login
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) return;

                const identity = $('[name="identity"]').val();
                const password = $('[name="password"]').val();

                $.ajax({
                    url: '<?= base_url('auth/panel/login') ?>',
                    type: 'POST',
                    data: {
                        identity,
                        password
                    },
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            window.location.href = res.redirect;
                        } else {
                            // Top Center
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center'
                            });
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan koneksi.');
                    }
                });
            });
        });
    </script>

</body>

</html>