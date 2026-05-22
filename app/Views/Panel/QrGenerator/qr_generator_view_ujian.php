<?php
// File: app/Views/Panel/qr_generator_view.php
?>
<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <form class="form-horizontal" id="settings-form">
                    <div class="widget-heading">
                        <h5 class="">Qr Code Link Ujian</h5>
                        <div class="task-action">
                            <button type="submit" class="btn btn-primary"><i data-feather="refresh-cw"></i> <span class="btn-text-inner">Generate</span></button>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="alert alert-primary alert-dismissible fade show border-0 mb-4" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg> ... </svg></button>
                            <strong>Info!</strong> Qr Code yang dihasilkan sudah Ter-Enkripsi, hanya bisa di Scan di Candy Exam.</button>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="url_server" class="form-control-label col-sm-3 text-md-right">Link Ujian</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="url_server" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="nama_ujian" class="form-control-label col-sm-3 text-md-right">Nama Ujian</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nama_ujian" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="jumlah_ruang" class="form-control-label col-sm-3 text-md-right">Jumlah Ruang</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="number" name="jumlah_ruang" id="jumlah_ruang" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <img id="qrPreview" src="" alt="QR Code Server" width="300" style="display: none;">

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
        const $linkInput = $('[name="url_server"]');

        $linkInput.on('input', function() {
            const link = $(this).val().trim();
            if (link.length > 0) {
                $.ajax({
                    url: "<?= site_url('panel/qrgenerator/qrujian') ?>",
                    type: "GET",
                    data: {
                        link: link
                    },
                    success: function(response) {
                        if (response.qr) {
                            $('#qrPreview').attr('src', response.qr).show();
                        } else {
                            $('#qrPreview').hide().attr('src', '');
                        }
                    },
                    error: function() {
                        $('#qrPreview').hide().attr('src', '');
                        console.warn('QR gagal diperbarui.');
                    }
                });
            } else {
                $('#qrPreview').hide().attr('src', '');
            }
        });
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: "<?= site_url('panel/qrgenerator/generateprintlink') ?>",
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