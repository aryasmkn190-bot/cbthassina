<!DOCTYPE html>
<html>

<head>
    <title>Koneksi Database Gagal</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
        }

        .box {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }

        .error {
            color: #a94442;
            margin-top: 20px;
            font-family: monospace;
            background: #f2dede;
            padding: 10px;
            border-radius: 5px;
        }

        .btn-install {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-install:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>⚠️ Koneksi ke Database Gagal</h2>
        <p>Mohon periksa konfigurasi database Anda.</p>
        <div class="error"><?= esc($error) ?></div>

        <p>Jika Anda baru pertama kali menginstal aplikasi ini:</p>
        <a href="<?= base_url('install') ?>" class="btn-install">Mulai Instalasi</a>
    </div>
</body>

</html>