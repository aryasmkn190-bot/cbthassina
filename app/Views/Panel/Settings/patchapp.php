<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading mb-3">
                    <h5 class="">Patch Aplikasi</h5>
                    <div class="task-action">
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive mb-3">
                        <table class="table mb-4">
                            <tbody>
                                <tr>
                                    <td style="width: 150px;">Informasi Aplikasi</td>
                                    <td>Candy Exam Server</td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;">App Version</td>
                                    <td><?= $setting->appversion ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;">Keterangan</td>
                                    <td>
                                        Jangan diganti Copyrightnya abis itu dijual lagi dan diakui aplikasi sendiri <br>
                                        hargai pengembang yak kakak,,
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card style-4 col-sm-6">
                        <form class="form-horizontal" id="patchform">
                            <div class="card-body pt-3">
                                <div class="form-group mb-3">
                                    <input type="file" class="form-control-file" name="patch_zip" id="patch_zip" placeholder="" aria-describedby="fileHelpId" required>
                                    <small id="fileHelpId" class="form-text text-muted">Upload File Zip</small>
                                </div>

                            </div>
                            <div class="card-footer pt-0 border-0">
                                <button type="submit" class="btn btn-primary">Proses Patch</button>
                            </div>
                        </form>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#patchform').submit(function(event) {
            event.preventDefault();
            // Mendapatkan file ZIP yang diunggah
            var formData = new FormData($(this)[0]);

            // Mengirim file ZIP melalui AJAX
            $.ajax({
                url: '<?= base_url('panel/pengaturan/patch/update') ?>', // URL untuk upload
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Snackbar.show({
                        text: response.message,
                        pos: 'top-center'
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Gagal melakukan update aplikasi: ' + xhr.responseText);
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>