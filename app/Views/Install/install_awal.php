<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Instalasi Awal - Administrator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4e9f0);
        }

        .setup-card {
            margin-top: 60px;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease;
        }

        .btn-main {
            background-color: #6c63ff;
            border-color: #6c63ff;
            transition: all 0.3s ease;
        }

        .btn-main:hover {
            background-color: #554ae0;
            border-color: #554ae0;
        }

        .fade {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .fade.show {
            opacity: 1;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="d-flex align-items-center" style="min-height: 100vh">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card setup-card p-4">
                    <h3 class="text-center mb-4">Konfigurasi Administrator</h3>
                    <form id="adminForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button id="mainButton" type="submit" class="btn btn-main w-100">Simpan Administrator</button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted" id="stepInfo">Langkah 1 dari 3</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentStep = 1;

            const $mainButton = $('#mainButton');
            const $form = $('#adminForm');
            const $stepInfo = $('#stepInfo');

            function updateButton(label, type = 'button') {
                $mainButton.text(label).attr('type', type);
            }

            function showSuccess(msg) {
                alert(msg);
            }

            // Step 1: Simpan Admin
            $form.on('submit', function(e) {
                e.preventDefault();
                if (currentStep !== 1) return;
                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }

                $.ajax({
                    url: '<?= base_url('install/simpan_admin') ?>',
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            showSuccess("Administrator berhasil disimpan.");
                            currentStep = 2;
                            updateButton("Cek & Buat Database");
                            $stepInfo.text("Langkah 2 dari 3");
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menyimpan administrator.');
                    }
                });
            });

            // Step 2 & 3
            $mainButton.on('click', function(e) {
                if (currentStep === 2) {
                    // Migrasi Database
                    $.get('<?= base_url('install/migrate') ?>', function(res) {
                        if (res.success) {
                            showSuccess("Migrasi database berhasil.");
                            // Delay sedikit untuk memberi waktu tampilkan pesan sukses
                            setTimeout(function() {
                                // Redirect ke halaman utama (atau login/admin/dashboard)
                                window.location.href = '<?= base_url('panel') ?>';
                            }, 1500); // 1.5 detik
                            currentStep = 3;
                            updateButton("Lanjutkan ke Aplikasi");
                            $stepInfo.text("Langkah 3 dari 3");
                        } else {
                            alert(res.message);
                        }
                    }).fail(function() {
                        alert('Gagal menjalankan migrasi.');
                    });
                } else if (currentStep === 3) {
                    window.location.href = '<?= base_url() ?>';
                }
            });
        });
    </script>
</body>

</html>