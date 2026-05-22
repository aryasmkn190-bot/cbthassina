<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - <?= esc($ruang['nama']) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        h2 {
            margin: 0;
            text-align: center;
            color: #1e2a38;
            font-weight: 600;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px 10px;
        }

        th {
            background-color: #e3f2fd;
            color: #1e2a38;
            font-weight: 600;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        td.tanda-tangan {
            background-color: #fff3e0;
        }

        .info-table td {
            border: none;
            padding: 3px 5px;
        }

        .info-table td:first-child {
            width: 150px;
            font-weight: 600;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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
            margin: 4px 0 0 0;
            color: #444;
            letter-spacing: 0.5px;
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

            th,
            td {
                border: 1px solid #000;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div class="header-content">
            <div class="logo">
                <?php if (!empty($setting->logo)): ?>
                    <img src="<?= base_url('assets/img/' . $setting->logo) ?>" alt="Logo Sekolah">
                <?php endif; ?>
            </div>
            <div class="text">
                <h1><?= strtoupper(esc($setting->nama_sekolah)) ?></h1>
                <h2>DAFTAR HADIR PESERTA UJIAN</h2>
            </div>
        </div>
        <hr class="divider">
    </div>

    <table class="info-table">
        <tr>
            <td>Nama Ujian</td>
            <td>: __________________________</td>
            <td>Waktu Ujian</td>
            <td>: __________________________</td>
        </tr>
        <tr>
            <td>Tanggal Ujian</td>
            <td>: __________________________</td>
            <td>Ruang</td>
            <td>: <?= esc($ruang['nama']) ?></td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td>: __________________________</td>
            <td>Sesi</td>
            <td>: <?= esc($sesi['nama']) ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Peserta</th>
                <th>NIS/NISN</th>
                <th>Kelas</th>
                <th style="width: 150px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($peserta as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($p['nama']) ?></td>
                    <td><?= esc($p['nisn'] ?? '-') ?></td>
                    <td><?= esc($p['nama_kelas'] ?? '-') ?></td>
                    <td class="tanda-tangan"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Tutup tab setelah print atau cancel
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>