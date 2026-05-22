<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Ujian</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>assets/img/favicon.ico" />
    <link rel="stylesheet" href="<?= base_url('src/bootstrap/css/bootstrap.min.css') ?>">

    <style>
        body {
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(-45deg, #e0f2ff, #d6e4ff, #f3f7ff, #ffffff);
            background-size: 400% 400%;
            animation: gradientMove 12s ease infinite;
            position: relative;
            margin: 0;
        }

        /* Background bubble */
        .bg-bubbles {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
        }

        .bg-bubbles::before,
        .bg-bubbles::after {
            content: "";
            position: absolute;
            top: -50px;
            border-radius: 50%;
            animation: floatBubble 20s infinite ease-in-out;
        }

        .bg-bubbles::before {
            left: -80px;
            width: 300px;
            height: 300px;
            background: rgba(92, 106, 196, 0.15);
        }

        .bg-bubbles::after {
            right: -80px;
            width: 200px;
            height: 200px;
            background: rgba(124, 137, 249, 0.18);
            animation-duration: 25s;
        }

        /* Gradient animation */
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes floatBubble {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(80px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .portal-card {
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
            padding: 1.5rem;
        }

        .portal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(92, 106, 196, 0.25);
        }

        .portal-icon {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #5C6AC4, #7C89F9);
            color: white;
            box-shadow: 0 5px 12px rgba(92, 106, 196, 0.35);
            transition: transform 0.3s ease;
        }

        .portal-card:hover .portal-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .portal-icon svg {
            width: 28px;
            height: 28px;
        }

        /* Modal */
        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: none;
            background: linear-gradient(135deg, #5C6AC4, #7C89F9);
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .form-control {
            border-radius: 0.6rem;
        }

        .form-control:focus {
            border-color: #5C6AC4;
            box-shadow: 0 0 0 0.2rem rgba(92, 106, 196, 0.25);
        }

        #btnSubmitToken {
            background: linear-gradient(135deg, #5C6AC4, #7C89F9);
            border: none;
            border-radius: 0.6rem;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }

        #btnSubmitToken:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(92, 106, 196, 0.35);
        }

        /* HP mode */
        @media (max-width: 767px) {
            .portal-card {
                padding: 12px;
            }

            .portal-card h6 {
                font-size: 14px;
            }

            .portal-card p {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- Background -->
    <div class="bg-bubbles"></div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">Portal Ujian</h2>
            <p class="text-muted">Pilih menu untuk memulai ujian atau melihat informasi.</p>
        </div>

        <div class="row justify-content-center">
            <?php if (empty($menus)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-warning small py-3">
                        <i data-feather="info" class="me-1"></i>
                        Ujian belum tersedia saat ini. Silakan cek kembali nanti.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card portal-card p-3 text-center menu-item"
                            data-link="<?= esc($menu['link']) ?>"
                            data-token="<?= esc($menu['token']) ?>"
                            data-istoken="<?= esc($menu['is_token']) ?>">

                            <div class="portal-icon mb-2">
                                <i data-feather="<?= esc($menu['icon']) ?>"></i>
                            </div>

                            <h6 class="fw-bold mb-1"><?= esc($menu['title']) ?></h6>

                            <?php if ($menu['tgl_dibuka'] && $menu['tgl_ditutup']): ?>
                                <p class="text-muted small mb-0">
                                    <?= date('d M Y H:i', strtotime($menu['tgl_dibuka'])) ?> -
                                    <?= date('d M Y H:i', strtotime($menu['tgl_ditutup'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Token -->
    <div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">🔑 Masukkan Token Ujian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="password" class="form-control" id="inputToken" placeholder="Token ujian">
                    <div class="invalid-feedback mt-2">Token salah, coba lagi.</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100" id="btnSubmitToken">Masuk Ujian</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('src/plugins/src/jquery/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('src/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url() ?>src/plugins/src/font-icons/feather/feather.min.js"></script>
    <script>
        $(function() {
            let selectedLink = '';
            let expectedToken = '';

            $('.menu-item').on('click', function() {
                const link = $(this).data('link');
                const isToken = $(this).data('istoken');
                const token = $(this).data('token');

                if (isToken == 1) {
                    selectedLink = link;
                    expectedToken = token;
                    $('#inputToken').val('').removeClass('is-invalid');
                    $('#tokenModal').modal('show');
                } else {
                    window.location.href = link;
                }
            });

            $('#btnSubmitToken').on('click', function() {
                const input = $('#inputToken').val().trim();
                if (input === String(expectedToken).trim()) {
                    window.location.href = selectedLink;
                } else {
                    $('#inputToken').addClass('is-invalid').focus();
                }
            });
        });

        feather.replace();
    </script>

</body>

</html>