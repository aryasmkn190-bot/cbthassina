<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Akun</title>
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />

    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
        }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f4f8, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-watermark {
            position: fixed;
            width: 180px;
            opacity: 0.05;
            z-index: 1;
            pointer-events: none;
            transition: all 3s ease-in-out;
        }

        .card.login-card {
            border-radius: 1.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border: none;
            background: #fff;
            padding: 2rem;
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
        }

        h2.fw-bold {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.3rem;
            background: linear-gradient(90deg, #5C6AC4, #9C6ADE);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }



        .form-control {
            border-radius: 50px;
            padding: 0.75rem 1.2rem;
            border: 1.5px solid #ddd;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .form-control:focus {
            border-color: #5C6AC4;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(92, 106, 196, 0.15);
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            transition: 0.3s ease;
            color: #aaa;
            pointer-events: none;
        }

        .form-control:focus+label,
        .form-control:not(:placeholder-shown)+label {
            top: -10px;
            left: 15px;
            font-size: 0.8rem;
            background: #fff;
            padding: 0 5px;
            color: #5C6AC4;
        }

        /* Form group relative sudah ada */
        .form-group {
            position: relative;
            margin-bottom: 2rem;
            /* beri ruang lebih untuk error */
        }

        /* Input validasi */
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: 2.5rem;
            /* ruang untuk icon eye tetap */
        }

        /* Invalid feedback rapih */
        .invalid-feedback {
            display: block;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Toggle password tetap di kanan */
        .input-password .toggle-password {
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            position: absolute;
            cursor: pointer;
            color: #888;
            z-index: 2;
        }

        /* Jangan biarkan icon terhalang error */
        .input-password input.is-invalid+label+.toggle-password {
            /* tetap di atas input */
        }

        /* Jika mau, bisa buat icon eye ikut merah saat error */
        .input-password input.is-invalid~.toggle-password {
            color: #dc3545;
        }


        .btn-gradient {
            background: linear-gradient(90deg, #5C6AC4, #9C6ADE);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
            border-radius: 50px;
            padding: 0.7rem 1.2rem;
            font-size: 1rem;
        }

        .btn-gradient:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #4a57b3, #845ec2);
        }

        /* Bubble minimalis */
        .bubbles span {
            position: absolute;
            bottom: -80px;
            background: rgba(92, 106, 196, 0.1);
            border-radius: 50%;
            animation: bubbleUp 12s linear infinite;
        }

        @keyframes bubbleUp {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.5;
            }

            100% {
                transform: translateY(-120vh) scale(1.2);
                opacity: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Watermark -->
    <img src="<?= base_url('assets/img/logo_exam_1.png') ?>" class="bg-watermark" id="watermark" alt="Watermark">

    <!-- Bubble Background -->
    <div class="bubbles">
        <span style="left:10%;width:30px;height:30px;"></span>
        <span style="left:25%;width:20px;height:20px;"></span>
        <span style="left:40%;width:40px;height:40px;"></span>
        <span style="left:60%;width:25px;height:25px;"></span>
        <span style="left:75%;width:35px;height:35px;"></span>
    </div>

    <div class="card login-card">
        <div class="text-center mb-4">
            <img src="<?= base_url('assets/img/') . $setting->logo ?>" alt="Logo" style="width: 70px; height:70px;" class="mb-2">
            <h2 class="fw-bold">Masuk Akun</h2>
            <p class="text-muted mb-0">Masukkan username dan password</p>
        </div>

        <form id="loginForm" class="needs-validation" novalidate autocomplete="off">
            <div class="form-group">
                <input type="text" name="identity" class="form-control" placeholder=" " required autocomplete="off">
                <label>Username</label>
                <div class="invalid-feedback">Wajib diisi.</div>
            </div>

            <div class="form-group input-password">
                <input type="password" name="password" class="form-control" placeholder=" " required autocomplete="off">
                <label>Password</label>
                <span class="toggle-password">&#128065;</span>
                <div class="invalid-feedback">Wajib diisi.</div>
            </div>

            <button type="submit" class="btn btn-gradient w-100">Masuk</button>
        </form>

    </div>

    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.js"></script>

    <script>
        $(document).ready(function() {
            // ------------------------------
            // Watermark animasi
            // ------------------------------
            const watermark = $('#watermark')[0];
            const positions = [{
                    top: "5%",
                    left: "5%",
                    transform: "rotate(-15deg)"
                },
                {
                    top: "10%",
                    right: "8%",
                    transform: "rotate(10deg)"
                },
                {
                    bottom: "8%",
                    left: "10%",
                    transform: "rotate(20deg)"
                },
                {
                    bottom: "5%",
                    right: "5%",
                    transform: "rotate(-10deg)"
                }
            ];

            function moveWatermark() {
                const pos = positions[Math.floor(Math.random() * positions.length)];
                watermark.style.top = watermark.style.bottom = "";
                watermark.style.left = watermark.style.right = "";
                Object.assign(watermark.style, pos);
            }
            moveWatermark();
            setInterval(moveWatermark, 3000);

            // ------------------------------
            // Toggle password
            // ------------------------------
            $(document).on('click', '.toggle-password', function() {
                let input = $(this).siblings('input');
                input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            });

            // ------------------------------
            // Reset form dan sembunyikan error saat load
            // ------------------------------
            const $form = $('#loginForm');
            $form[0].reset();
            $form.find('input').removeClass('is-invalid');
            $form.find('.invalid-feedback').hide();

            // ------------------------------
            // Hapus error saat user mengetik
            // ------------------------------
            $form.find('input').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').hide();
            });

            // ------------------------------
            // Submit form dengan validasi & AJAX
            // ------------------------------
            $form.on('submit', function(e) {
                e.preventDefault();
                let valid = true;

                // Validasi input secara manual
                $form.find('input').each(function() {
                    if (!this.checkValidity()) {
                        $(this).addClass('is-invalid');
                        $(this).siblings('.invalid-feedback').show();
                        valid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').hide();
                    }
                });

                if (!valid) return;

                // Ambil data
                let identity = $('[name="identity"]').val();
                let password = $('[name="password"]').val();
                let role = $('[name="role"]').val();

                // AJAX login
                $.ajax({
                    url: "<?= base_url('auth/login') ?>",
                    method: "POST",
                    data: {
                        identity,
                        password,
                        role
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            window.location.href = res.redirect;
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center'
                            });
                        }
                    },
                    error: function() {
                        Snackbar.show({
                            text: 'Terjadi kesalahan koneksi.',
                            pos: 'top-center'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>