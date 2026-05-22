<?= $this->extend('Layout/main'); ?>
<?= $this->section('css'); ?>

<!--  BEGIN CUSTOM STYLE FILE  -->
<link href="<?= base_url() ?>/src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/src/plugins/css/light/editors/quill/quill.snow.css">

<link href="<?= base_url() ?>/src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/src/plugins/css/dark/editors/quill/quill.snow.css">
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <form class="form-horizontal" id="settings-form">
                    <div class="widget-heading mb-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Halaman Informasi</h5>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> <span class="btn-text-inner">Simpan</span>
                        </button>
                    </div>

                    <div class="widget-content">

                        <div id="basic" class="layout-spacing layout-top-spacing">
                            <div id="editor-container">

                            </div>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script src="<?= base_url() ?>/src/plugins/src/editors/quill/quill.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Basic

        var quill = new Quill('#editor-container', {
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
            placeholder: 'Compose an epic...',
            theme: 'snow' // or 'bubble'
        });
        // Ambil data dari server
        $.ajax({
            url: "<?= site_url('panel/exambro/setting/getdata') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                quill.root.innerHTML = data.informasi;
            },
            error: function() {
                alert('Gagal mengambil data dari server.');
            }
        });
        // Submit form
        $('#settings-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var html = quill.root.innerHTML;
            formData.append('informasi', html); // key 'content' harus cocok dengan server
            $.ajax({
                url: '<?= base_url('panel/exambro/setting/update') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    Snackbar.show({
                        text: response.message,
                        pos: 'top-center'
                    });
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan pengaturan.');
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>