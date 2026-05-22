<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Wizard Instalasi Candy Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(120deg, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.7s ease-in-out;
        }

        .step {
            display: none;
            animation: slideIn 0.4s ease;
        }

        .step.active {
            display: block;
        }

        .badge-step {
            width: 40px;
            height: 40px;
            font-size: 16px;
            line-height: 40px;
            border-radius: 50%;
            margin: 0 8px;
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-control:invalid {
            border-color: #dc3545;
        }

        .form-control:valid {
            border-color: #198754;
        }

        .btn-next {
            transition: 0.3s;
        }

        .btn-next:disabled {
            opacity: 0.6;
        }
    </style>
</head>

<body>
    <div class="card">
        <h3 class="text-center mb-4">🛠️ Wizard Instalasi Candy Exam</h3>

        <!-- Step Indicator -->
        <div class="text-center mb-4">
            <span class="badge bg-primary badge-step" id="badge1">1</span>
            <span class="badge bg-secondary badge-step" id="badge2">2</span>
            <span class="badge bg-secondary badge-step" id="badge3">3</span>
        </div>

        <!-- Step 1 -->
        <div class="step active" id="step1">
            <h5 class="mb-3">1️⃣ Konfigurasi Database</h5>
            <form id="formDb" class="needs-validation" novalidate>
                <input type="text" name="hostname" class="form-control mb-3" placeholder="Host (contoh: localhost)" value="localhost" required>
                <input type="text" name="username" class="form-control mb-3" placeholder="Username DB" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password DB">
                <input type="text" name="database" class="form-control mb-3" placeholder="Nama Database" required>
            </form>
        </div>

        <!-- Step 2 -->
        <div class="step" id="step2">
            <h5 class="mb-3">2️⃣ Akun Admin</h5>
            <form id="formAdmin" class="needs-validation" novalidate>
                <input type="text" name="nama" class="form-control mb-3" placeholder="Nama Lengkap" required>
                <input type="email" name="email" class="form-control mb-3" placeholder="Email Admin" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password Admin" required>
            </form>
        </div>

        <!-- Step 3 -->
        <div class="step" id="step3">
            <h5 class="mb-3">3️⃣ Migrasi Database</h5>
            <p>Klik tombol di bawah untuk menjalankan migrasi database:</p>
            <button id="btnMigrasi" class="btn btn-success w-100">🚀 Jalankan Migrasi</button>
        </div>

        <!-- Navigasi -->
        <div class="d-grid mt-4">
            <button id="btnNext" class="btn btn-primary btn-next">➡️ Lanjutkan</button>
        </div>

        <!-- Notifikasi -->
        <div id="notif" class="mt-3"></div>

        <div class="text-center text-muted mt-3 small">
            Candy Exam Installer • <?= date('Y') ?>
        </div>
    </div>

    <script>
        let step = 1;

        function showStep(stepNumber) {
            $('.step').removeClass('active');
            $('#step' + stepNumber).addClass('active');

            for (let i = 1; i <= 3; i++) {
                $('#badge' + i).removeClass('bg-primary').addClass('bg-secondary');
            }
            $('#badge' + stepNumber).removeClass('bg-secondary').addClass('bg-primary');

            $('#btnNext').toggle(stepNumber < 3);
        }

        function showNotif(message, type = 'success') {
            $('#notif').html(`<div class="alert alert-${type} mt-2">${message}</div>`);
        }

        function validateAndMark(formId) {
            const form = document.getElementById(formId);
            if (!form.checkValidity()) {
                form.classList.add('was-validated'); // Hanya ditambahkan saat tombol diklik
                return false;
            }
            return true;
        }

        $('#btnNext').click(function() {
            if (step === 1) {
                if (validateAndMark('formDb')) {
                    $.post('<?= base_url('install/dbsave') ?>', $('#formDb').serialize(), function(res) {
                        if (res.success) {
                            showNotif('✅ Database berhasil dikonfigurasi');
                            step++;
                            showStep(step);
                        } else {
                            showNotif(res.message || '❌ Gagal menyimpan konfigurasi DB', 'danger');
                        }
                    });
                }
            } else if (step === 2) {
                if (validateAndMark('formAdmin')) {
                    $.post('<?= base_url('install/save-admin') ?>', $('#formAdmin').serialize(), function(res) {
                        if (res.success) {
                            showNotif('✅ Admin berhasil disimpan');
                            step++;
                            showStep(step);
                        } else {
                            showNotif(res.message || '❌ Gagal menyimpan admin', 'danger');
                        }
                    });
                }
            }
        });

        $('#btnMigrasi').click(function() {
            $('#btnMigrasi').prop('disabled', true).text('⏳ Menjalankan migrasi...');
            $.get('<?= base_url('install/migrate') ?>', function(res) {
                if (res.success) {
                    showNotif('🎉 Migrasi berhasil! Anda bisa login sekarang.');
                    $('#btnMigrasi').text('✅ Migrasi Selesai');
                } else {
                    $('#btnMigrasi').prop('disabled', false).text('🚀 Jalankan Migrasi');
                    showNotif('❌ ' + res.message, 'danger');
                }
            });
        });
    </script>

</body>

</html>