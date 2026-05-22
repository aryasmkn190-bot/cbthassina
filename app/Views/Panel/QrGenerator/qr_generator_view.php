<?= $this->extend('Layout/main') ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">

                <form class="form-horizontal" id="settings-form">
                    <div class="widget-heading d-flex justify-content-between align-items-center">
                        <h5>QR Code URL Server</h5>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="refresh-cw"></i>
                            <span class="btn-text-inner">Generate</span>
                        </button>
                    </div>

                    <div class="widget-content">
                        <div class="alert alert-primary alert-dismissible fade show border-0 mb-4" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Info!</strong> QR Code yang dihasilkan sudah terenkripsi dan hanya bisa di-scan di aplikasi Candy Exam.
                        </div>

                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">URL Server</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="url_server" id="url_server" class="form-control form-control-sm" value="<?= base_url() ?>" placeholder="https://example.com/" required>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-3">
                            <label for="nama_ujian" class="form-control-label col-sm-3 text-md-right">Nama Ujian</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nama_ujian" id="nama_ujian" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-3">
                            <label for="jumlah_ruang" class="form-control-label col-sm-3 text-md-right">Jumlah Ruang</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="number" name="jumlah_ruang" id="jumlah_ruang" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="text-center mt-4" id="qr-preview">
                            <img id="qrImage" src="<?= $qrImageUri ?? '' ?>" alt="QR Code Server" width="300">
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
    $(document).ready(function() {

        // 🔹 Fungsi generate QR otomatis via AJAX
        function generateQR() {
            const formData = $('#settings-form').serialize();

            $.ajax({
                url: "<?= site_url('panel/qrgenerator/generateserver') ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    // Jika hasil QR berupa Data URI (base64)
                    if (response.startsWith('data:image')) {
                        $('#qrImage').attr('src', response);
                    } else {
                        console.error('Respons tidak valid:', response);
                    }
                },
                error: function() {
                    console.error("Gagal membuat QR Code");
                }
            });
        }

        // 🔹 Generate otomatis saat URL server berubah
        $('#url_server').on('input', function() {
            const val = $(this).val();
            if (val.length > 5 && val.startsWith('http')) {
                generateQR();
            }
        });

        // 🔹 Submit manual untuk buka QR di tab baru
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "<?= site_url('panel/qrgenerator/generateprint') ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.open();
                    printWindow.document.write(response);
                    printWindow.document.close();
                },
                error: function() {
                    alert('Gagal menghasilkan QR Code. Periksa input atau koneksi.');
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>