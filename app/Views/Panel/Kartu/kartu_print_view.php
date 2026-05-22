<!-- File: app/Views/Panel/KartuPeserta/print_kartu_view.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Kartu Peserta</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .kartu-container {
                page-break-inside: avoid;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            padding: 20px;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .kartu-container {
            width: 350px;
            height: 190px;
            border-radius: 10px;
            border: 1px solid #000;
            padding: 12px 16px;
            box-sizing: border-box;
            page-break-inside: avoid;
            position: relative;
        }

        .judul {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subjudul {
            text-align: center;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .kartu-content {
            display: flex;
            justify-content: space-between;
        }

        .info-table {
            font-size: 11.5px;
            width: 65%;
        }

        .info-table td {
            vertical-align: top;
            padding-bottom: 2px;
        }

        .qr-code {
            width: 32%;
            text-align: center;
        }

        .qr-code img {
            max-width: 90px;
            max-height: 90px;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            right: 16px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="grid">
        <?php foreach ($pesertaList as $peserta): ?>
            <div class="kartu-container">
                <div class="judul">Kartu Peserta Ujian</div>
                <div class="subjudul"><strong><?= esc($nama_ujian) ?></strong></div>
                <div class="kartu-content">
                    <table class="info-table">
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>: <?= esc($peserta['nama']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>: <?= esc($peserta['nama_kelas']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jurusan</strong></td>
                            <td>: <?= esc($peserta['nama_jurusan']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Username</strong></td>
                            <td>: <?= esc($peserta['username']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Password</strong></td>
                            <td>: <?= customDecrypt($peserta['password'], $setting->key_encrypt) ?></td>
                        </tr>
                    </table>
                    <div class="qr-code">
                        <img src="<?= $qrImageUri ?>" alt="QR Code">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="no-print" style="text-align:center; margin-top: 20px;">
        <button onclick="window.print()">Cetak Kartu</button>
    </div>

    <script>
        window.addEventListener('load', function() {
            const printTimeout = setTimeout(() => {
                window.print();
            }, 300);

            window.onafterprint = function() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.close();
                }
            };
        });
    </script>
</body>

</html>