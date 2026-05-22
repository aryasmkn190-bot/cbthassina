<!-- File: app/Views/Panel/kartu_peserta_config.php -->
<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three p-3 shadow-sm rounded bg-white">
                <form class="form-horizontal" id="form-kartu">

                    <!-- Header -->
                    <div class="widget-heading d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="mb-2 mb-sm-0">Konfigurasi Kartu Peserta</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="eye"></i> Preview
                            </button>
                            <button type="button" id="btnPrint" class="btn btn-success">
                                <i data-feather="printer"></i> Cetak
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="widget-content">
                        <div class="mb-3">
                            <label for="nama_ujian" class="form-label">Nama Ujian</label>
                            <input type="text" name="nama_ujian" id="nama_ujian"
                                class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select name="kelas_id" id="kelas_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k) : ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Preview -->
                <div class="widget-content mt-4">
                    <h6 class="fw-bold">Preview Kartu Peserta</h6>
                    <div id="preview-container">
                        <div class="alert alert-info mb-0">
                            Silakan isi form dan klik Preview untuk menampilkan contoh kartu peserta.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        $('#form-kartu').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "<?= site_url('panel/kartu/preview') ?>",
                type: "POST",
                data: formData,
                success: function(html) {
                    $('#preview-container').html(html);
                },
                error: function() {
                    alert("Gagal memuat preview kartu.");
                }
            });
        });

        $('#btnPrint').on('click', function() {
            const namaUjian = $('#nama_ujian').val();
            const kelasId = $('#kelas_id').val();

            if (!namaUjian || !kelasId) {
                alert('Nama ujian dan kelas wajib diisi.');
                return;
            }

            const url = `<?= site_url('panel/kartu/print') ?>?nama_ujian=${encodeURIComponent(namaUjian)}&kelas_id=${encodeURIComponent(kelasId)}`;
            window.open(url, '_blank');
        });
    });
</script>
<?= $this->endSection() ?>