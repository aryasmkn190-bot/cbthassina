<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 15px;
            color: #000;
            margin: 35px;
            line-height: 1.5;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .header img {
            width: 85px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .header h2,
        .header h3 {
            margin: 0;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 20px;
            font-weight: bold;
        }

        .header h3 {
            font-size: 18px;
            margin-top: 5px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        td {
            padding: 4px 2px;
            vertical-align: top;
            font-size: 15px;
        }

        /* CATATAN */
        .note-box {
            border: 1px solid #333;
            border-radius: 6px;
            min-height: 120px;
            padding: 12px;
            font-size: 15px;
            margin-top: 5px;
            background: #fafafa;
        }

        /* TTD */
        .ttd-table td {
            padding-top: 5px;
            height: 130px;
        }

        .ttd-table td u {
            font-weight: bold;
            font-size: 15px;
        }

        /* FOOTER CATATAN */
        .footer-box {
            border: 1px solid #555;
            background: #f9f9f9;
            padding: 10px 12px;
            margin-top: 25px;
            font-size: 13px;
            border-radius: 6px;
        }

        /* PRINT */
        @media print {
            body {
                margin: 10mm 13mm;
            }
        }
    </style>
</head>
<?php
// Nama + NIP otomatis garis kosong jika null
$proktorNama = $data['proktor_nama'] ?: '_______________________';
$proktorNip  = $data['proktor_nip'];

$pengawasNama = $data['pengawas_nama'] ?: '_______________________';
$pengawasNip  = $data['pengawas_nip'];

$kepsekNama = $data['kepala_sekolah_nama'] ?: '_______________________';
$kepsekNip  = $data['kepala_sekolah_nip'];
?>

<body onload="window.print()">

    <!-- HEADER -->
    <div class="header">
        <img src="<?= base_url('assets/img/' . $setting->logo) ?>">
        <h2>BERITA ACARA PELAKSANAAN</h2>
        <h3><?= strtoupper($data['nama_jenis_ujian']) ?></h3>
        <h3>TAHUN <?= $tahun ?></h3>
    </div>

    <!-- PARAGRAF PEMBUKA -->
    <p style="text-align: justify;">
        Pada hari ini <b><?= $hari ?></b> tanggal <b><?= $tgl ?></b> bulan
        <b><?= $bulan ?></b> tahun <b><?= $tahun ?></b>, di
        <b><?= $setting->nama_sekolah ?></b> telah diselenggarakan ujian
        <b><?= strtoupper($data['nama_ujian']) ?></b>, mulai pukul
        <b><?= $data['jam_mulai'] ?></b> sampai dengan pukul
        <b><?= $data['jam_selesai'] ?></b>.
    </p>
    <!-- DATA KEGIATAN -->
    <table>
        <tr>
            <td width="230">Sekolah/Madrasah</td>
            <td>: <?= $setting->nama_sekolah ?></td>
        </tr>
        <tr>
            <td>Ruang</td>
            <td>: <?= $data['nama_ruang'] ?></td>
        </tr>
        <tr>
            <td>Sesi</td>
            <td>: <?= $data['nama_sesi'] ?></td>
        </tr>
        <tr>
            <td>Jumlah Peserta Seharusnya</td>
            <td>: <?= $data['jumlah_peserta_seharusnya'] ?></td>
        </tr>
        <tr>
            <td>Jumlah Hadir (Ikut Ujian)</td>
            <td>: <?= $data['jumlah_hadir'] ?></td>
        </tr>
        <tr>
            <td>Jumlah Tidak Mengerjakan</td>
            <td>: <?= $data['jumlah_tidak_hadir'] ?></td>
        </tr>
        <tr>
            <td>Username Tidak Mengerjakan</td>
            <td>: <?= $data['peserta_tidak_hadir'] ?: '—' ?></td>
        </tr>
    </table>

    <!-- CATATAN -->
    <p><b>Catatan selama Tes :</b></p>
    <div class="note-box">
        <?= $data['catatan'] ? nl2br($data['catatan']) : '<i>Tidak ada catatan.</i>' ?>
    </div>



    <p><b>Yang membuat berita acara:</b></p>

    <!-- TTD -->
    <table class="ttd-table">
        <tr>
            <td width="33%">
                1. Proktor<br><br><br>
                <u><?= $proktorNama ?></u><br>
                <?= "NIP." . $proktorNip ?: " - " ?>
            </td>

            <td width="33%">
                2. Pengawas<br><br><br>
                <u><?= $pengawasNama ?></u><br>
                <?= "NIP." . $pengawasNip ?: " - " ?>
            </td>

            <td width="33%">
                3. Penanggung Jawab<br><br><br>
                <u><?= $kepsekNama ?></u><br>
                <?= "NIP." . $kepsekNip ?: " - " ?>
            </td>
        </tr>
    </table>

    <!-- FOOTER NOTES -->
    <div class="footer-box">
        • Berita acara dibuat rangkap 3 (tiga).<br>

    </div>
    <script>
        // Tunggu proses print selesai atau dibatalkan
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>