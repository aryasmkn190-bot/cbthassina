<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Denah Tempat Duduk - <?= esc($ruang['nama']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #1e2a38;
        }

        p.info {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .denah-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 12px;
            /* jarak horisontal & vertikal */
            margin-top: 20px;
            font-size: 14px;
        }

        .denah-table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            font-weight: 500;
        }

        .denah-table td:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .denah-table td.kosong {
            background-color: #eaeaea;
            border: 1px dashed #bbb;
        }

        small {
            display: block;
            color: #666;
            margin-top: 4px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header-content .logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-content .text {
            text-align: center;
        }

        .header-content h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 20px;
            margin: 0;
            color: #1e2a38;
            letter-spacing: 1px;
        }

        .header-content h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 16px;
            margin: 4px 0 6px 0;
            color: #444;
        }

        .header-content .sub-info {
            font-size: 13px;
            color: #555;
            margin: 0;
        }

        .divider {
            border: 0;
            border-top: 2px solid #1e2a38;
            margin-top: 10px;
            width: 100%;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .divider {
                border-color: #000;
            }

            .denah-table td {
                box-shadow: none;
                transform: none;
                border: 1px solid #000;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="header">
        <div class="header-content">
            <div class="logo">
                <?php if (!empty($setting->logo)): ?>
                    <img src="<?= base_url('assets/img/' . $setting->logo) ?>" alt="Logo Sekolah">
                <?php endif; ?>
            </div>
            <div class="text">
                <h1><?= strtoupper(esc($setting->nama_sekolah)) ?></h1>
                <h2>DENAH TEMPAT DUDUK PESERTA UJIAN</h2>
                <p class="sub-info">
                    Ruang: <?= esc($ruang['nama']) ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                    Sesi: <?= esc($sesi['nama']) ?>
                </p>
            </div>
        </div>
        <hr class="divider">
    </div>

    <?php
    $cols = $cols;
    $total = count($peserta);
    $rows = ceil($total / $cols);
    $index = 0;
    ?>

    <table class="denah-table">
        <?php for ($r = 0; $r < $rows; $r++): ?>
            <tr>
                <?php for ($c = 0; $c < $cols; $c++): ?>
                    <?php if ($index < $total):
                        $p = $peserta[$index]; ?>
                        <td>
                            <?= esc($p['nama']) ?><br>
                            <small><?= esc($p['nisn'] ?? '-') ?></small>
                        </td>
                        <?php $index++; ?>
                    <?php else: ?>
                        <td class="kosong">&nbsp;</td>
                    <?php endif; ?>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
    <script>
        // Tutup tab setelah print atau cancel
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>