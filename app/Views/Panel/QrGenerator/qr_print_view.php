<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code Server</title>

    <style>
        @media print {
            .page-break {
                page-break-after: always;
            }

            body {
                margin: 0;
            }
        }

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .qr-page {
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            padding: 40px;
            border: 6px solid #2F539B;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            page-break-inside: avoid;
        }

        .qr-page img.logo {
            width: 120px;
            margin-bottom: 15px;
        }

        .qr-page h1,
        .qr-page h2 {
            margin: 3px 0;
        }

        .qr-page img.qr {
            margin-top: 20px;
            width: 380px;
            height: 380px;
        }
    </style>
</head>

<body>

    <?php for ($i = 1; $i <= $jumlah_ruang; $i++): ?>

        <div class="qr-page">

            <img class="logo" src="<?= base_url('assets/img/' . $setting->logo) ?>" alt="Logo Sekolah">

            <h1><?= esc($setting->nama_sekolah) ?></h1>
            <h2><?= esc($nama_ujian) ?></h2>

            <h1>Ruang <?= $i ?></h1>

            <img class="qr"
                src="<?= $qr_codes[$i - 1]->getDataUri() ?>"
                alt="QR Code Ruang <?= $i ?>">
        </div>

        <?php if ($i < $jumlah_ruang): ?>
            <div class="page-break"></div>
        <?php endif; ?>

    <?php endfor; ?>

    <script>
        window.onload = () => {
            window.print();
        };
        window.onafterprint = () => {
            window.close();
        };
    </script>

</body>

</html>