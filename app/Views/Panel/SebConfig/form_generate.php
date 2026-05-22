<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <form class="form-horizontal" id="seb-form">
                    <div class="widget-heading d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">SEB Config Generator</h5>
                        <div class="task-action">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="refresh-cw"></i>
                                <span class="btn-text-inner">Generate Windows SEB</span>
                            </button>
                        </div>
                    </div>

                    <div class="widget-content">
                        <div class="alert alert-info alert-dismissible fade show border-0 mb-4" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Info!</strong> File konfigurasi akan dihasilkan dalam format
                            <code>.seb</code> untuk Windows.
                            Untuk iOS, gunakan **upload SEB signed**.
                        </div>

                        <!-- Start URL -->
                        <div class="form-group row align-items-center mb-3">
                            <label for="start_url" class="form-control-label col-sm-3 text-md-right">Start URL</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="url" name="start_url" id="start_url"
                                    class="form-control form-control-sm"
                                    required placeholder="https://domainujian.com/start"
                                    value="<?= esc($start_url ?? '') ?>">
                            </div>
                        </div>

                        <!-- Quit Password -->
                        <div class="form-group row align-items-center mb-3">
                            <label for="quit_password" class="form-control-label col-sm-3 text-md-right">Quit Password</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="quit_password" id="quit_password"
                                    class="form-control form-control-sm" required
                                    value="<?= esc($quit_password ?? '') ?>">
                            </div>
                        </div>

                        <!-- User Agent -->
                        <div class="form-group row align-items-center mb-3">
                            <label for="user_agent" class="form-control-label col-sm-3 text-md-right">User Agent</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="user_agent" id="user_agent"
                                    class="form-control form-control-sm"
                                    value="<?= esc($user_agent ?? 'SEB') ?>">
                            </div>
                        </div>

                        <!-- Enable Exit Button -->
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Enable Exit Button</label>
                            <div class="col-sm-6 col-md-9 d-flex align-items-center">
                                <input type="checkbox" name="allow_quit" id="allow_quit" class="form-check-input me-2">
                                <label for="allow_quit" class="mb-0">Tampilkan tombol exit (memerlukan Quit Password)</label>
                            </div>
                        </div>

                        <hr>

                        <!-- Upload SEB Signed (iOS) -->
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Upload SEB Config (iOS Signed)</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="file" name="seb_file" id="seb_file" accept=".seb" class="form-control form-control-sm">

                                <!-- Keterangan khusus iOS -->
                                <div class="alert alert-warning mt-2 p-2 mb-0" style="font-size: 12px;">
                                    <strong>Penting untuk iOS:</strong>
                                    Perangkat <b>iOS (iPad/iPhone)</b> hanya menerima file SEB yang
                                    <b>di-sign / ditandatangani</b> menggunakan
                                    <b>SEB Configuration Tool</b> resmi.
                                    File SEB hasil <b>Generate Windows</b> tidak dapat digunakan di iOS.
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <div class="row justify-content-center">
                                <!-- ========================= -->
                                <!--        SEB WINDOWS        -->
                                <!-- ========================= -->
                                <div class="col-md-5 text-center mb-4">
                                    <?php if (!empty($fileUrl) && !empty($qrCode)): ?>
                                        <a id="sebDownload" href="<?= esc($fileUrl) ?>" class="btn btn-success mb-3" target="_blank">
                                            Download Windows SEB
                                        </a>
                                        <button id="printQr" type="button" class="btn btn-secondary mb-3">
                                            <i data-feather="printer"></i> Print QR
                                        </button>
                                        <div id="qrWrapper">
                                            <img id="qrPreview" src="<?= esc($qrCode) ?>" alt="QR Code Config" width="300">
                                        </div>
                                    <?php else: ?>
                                        <a id="sebDownload" href="#" class="btn btn-success mb-3" target="_blank" style="display:none;">
                                            Download Windows SEB
                                        </a>
                                        <button id="printQr" type="button" class="btn btn-secondary mb-3" style="display:none;">
                                            <i data-feather="printer"></i> Print QR
                                        </button>
                                        <div id="qrWrapper">
                                            <img id="qrPreview" src="" alt="QR Code Config" width="300" style="display:none;">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- ========================= -->
                                <!--        SEB iOS UPLOAD     -->
                                <!-- ========================= -->
                                <div class="col-md-5 text-center mb-4">
                                    <?php if (!empty($fileUrlUpload) && !empty($qrCodeUpload)): ?>
                                        <a id="uploadedSebDownload" href="<?= esc($fileUrlUpload) ?>" class="btn btn-primary mb-3" target="_blank">
                                            Download Uploaded iOS SEB
                                        </a>
                                        <button id="printQrUpload" type="button" class="btn btn-secondary mb-3">
                                            <i data-feather="printer"></i> Print QR
                                        </button>
                                        <div id="qrWrapperUpload">
                                            <img id="uploadedQrPreview" src="<?= esc($qrCodeUpload) ?>"
                                                alt="QR Code Uploaded SEB" width="300">
                                        </div>
                                    <?php else: ?>
                                        <a id="uploadedSebDownload" href="#" class="btn btn-primary mb-3" target="_blank" style="display:none;">
                                            Download Uploaded iOS SEB
                                        </a>
                                        <div id="qrWrapperUpload">
                                            <img id="uploadedQrPreview" src="" alt="QR Code Uploaded SEB" width="300" style="display:none;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>



                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pagejs'); ?>
<script>
    $(function() {
        // Generate Windows SEB
        $('#seb-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= site_url('panel/qrgenerator/sebconfig/generate') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('#sebDownload').hide();
                    $('#qrPreview').hide().attr('src', '');
                    $('#printQr').hide();
                },
                success: function(res) {
                    if (res.success) {
                        if (res.fileUrl) $('#sebDownload').attr('href', res.fileUrl).show();
                        if (res.qrCode) {
                            $('#qrPreview').attr('src', res.qrCode).show();
                            $('#printQr').show();
                        }
                    } else {
                        alert(res.message ?? 'Gagal generate file SEB.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan server saat generate file SEB.');
                }
            });
        });

        // Upload iOS SEB Signed
        $('#seb_file').on('change', function() {
            var fileInput = this;
            if (fileInput.files.length === 0) return;

            var formData = new FormData();
            formData.append('seb_file', fileInput.files[0]);

            $.ajax({
                url: "<?= site_url('panel/qrgenerator/sebconfig/upload') ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#uploadedSebDownload').hide();
                    $('#uploadedQrPreview').hide().attr('src', '');
                },
                success: function(res) {
                    if (res.success) {
                        if (res.fileUrl) $('#uploadedSebDownload').attr('href', res.fileUrl).show();
                        if (res.qrCode) {
                            $('#uploadedQrPreview').attr('src', res.qrCode).show();
                            $('#printQrUpload').show(); // <--- tampilkan tombol print
                        }
                    } else {
                        alert(res.message ?? 'Gagal upload file SEB.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan server saat upload SEB.');
                }
            });
        });

        // Print QR
        $('#printQr').on('click', function() {
            var qrContent = document.getElementById('qrWrapper').innerHTML;
            var fileUrl = $('#sebDownload').attr('href') || '';

            var printWin = window.open('', '_blank');
            printWin.document.write(`
                                <html>
                                <head>
                                <title>Print QR Config</title>
                                <style>
                                @page { margin: 20mm; }
                                body { text-align: center; font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #fdfdfd; }
                                .print-container { border: 3px solid #333; padding: 30px; margin: auto; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.2); border-radius: 10px; background: #fff; }
                                h2 { margin-bottom: 20px; font-size: 20px; color: #222; }
                                img { max-width: 80%; margin-bottom: 20px; }
                                .link { font-size: 14px; color: #555; word-break: break-all; }
                                </style>
                                </head>
                                <body>
                                <div class="print-container">
                                <h2>SEB Config QR Code</h2>
                                ${qrContent}
                                ${fileUrl ? `<div class="link">Link Download: ${fileUrl}</div>` : ''}
                                </div>
                                <script>
                                window.onload = function(){
                                    window.print();
                                    window.close();
                                }
                                <\/script>
                                </body>
                                </html>
            `);
            printWin.document.close();
        });

        $('#printQrUpload').on('click', function() {
            var qrContent = document.getElementById('qrWrapperUpload').innerHTML;
            var fileUrl = $('#uploadedSebDownload').attr('href') || '';

            var printWin = window.open('', '_blank');
            printWin.document.write(`
                                <html>
                                <head>
                                <title>Print QR Uploaded</title>
                                <style>
                                @page { margin: 20mm; }
                                body { text-align: center; font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #fdfdfd; }
                                .print-container { border: 3px solid #333; padding: 30px; margin: auto; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.2); border-radius: 10px; background: #fff; }
                                h2 { margin-bottom: 20px; font-size: 20px; color: #222; }
                                img { max-width: 80%; margin-bottom: 20px; }
                                .link { font-size: 14px; color: #555; word-break: break-all; }
                                </style>
                                </head>
                                <body>
                                <div class="print-container">
                                <h2>SEB Config QR Code</h2>
                                ${qrContent}
                                ${fileUrl ? `<div class="link">Link Download: ${fileUrl}</div>` : ''}
                                </div>
                                <script>
                                window.onload = function(){
                                    window.print();
                                    window.close();
                                }
                                <\/script>
                                </body>
                                </html>
            `);
            printWin.document.close();
        });
    });
</script>
<?= $this->endSection() ?>