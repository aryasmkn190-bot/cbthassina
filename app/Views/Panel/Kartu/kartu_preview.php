<div style="
    width: 320px;
    height: auto;
    border-radius: 10px;
    border: 1px solid #444;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    padding: 12px 16px;
    margin: 20px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f9f9f9;
    position: relative;
    box-sizing: border-box;
">

    <div style="text-align: center; margin-bottom: 6px;">
        <h4 style="margin: 0; font-size: 16px; color: #222;">Kartu Peserta Ujian</h4>
        <p style="margin: 4px 0; font-size: 13px; color: #333;"><strong><?= esc($nama_ujian) ?></strong></p>
    </div>

    <hr style="margin: 8px 0; border-top: 1px dashed #ccc;">

    <div style="display: flex; flex-direction: row; justify-content: space-between;">
        <!-- Kolom Kiri: Tabel data peserta -->
        <div style="width: 65%;">
            <table style="width: 100%; font-size: 13px; color: #333;">
                <tr>
                    <td style="font-weight: bold;">Nama</td>
                    <td>: <?= esc($peserta['nama']) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Kelas</td>
                    <td>: <?= esc($peserta['nama_kelas']) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Jurusan</td>
                    <td>: <?= esc($peserta['nama_jurusan']) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Username</td>
                    <td>: <?= esc($peserta['username']) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Password</td>
                    <td>: <?= customDecrypt($peserta['password'], $setting->key_encrypt) ?></td>
                </tr>
            </table>
        </div>

        <!-- Kolom Kanan: QR Code -->
        <div style="width: 32%; text-align: center;">
            <img src="<?= $qrImageUri ?>" alt="QR Code" style="max-width: 90px; max-height: 90px;">
        </div>
    </div>

    <div style="
        position: absolute;
        bottom: 6px;
        right: 16px;
        font-size: 10px;
        color: #999;
    ">
        Cetakan preview
    </div>
</div>