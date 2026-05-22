<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <form class="form-horizontal" id="settings-form">
                    <div class="widget-heading">
                        <h5 class="">Setting Sekolah</h5>
                        <div class="task-action">
                            <button type="submit" class="btn btn-primary"><i data-feather="save"></i> <span class="btn-text-inner">Simpan</span></button>
                        </div>
                    </div>
                    <div class="widget-content">

                        <div class="form-group row align-items-center mb-3">
                            <label for="appname" class="form-control-label col-sm-3 text-md-right">Nama Aplikasi</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="appname" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="npsn" class="form-control-label col-sm-3 text-md-right">NPSN Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="npsn" id="npsn" class="form-control form-control-sm">
                                <small id="helpnpsn" class="form-text text-muted">Pengecekan data sekolah ..</small>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-3">
                            <label for="nss" class="form-control-label col-sm-3 text-md-right">NSS Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nss" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="appname" class="form-control-label col-sm-3 text-md-right">Nama Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nama_sekolah" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="jenjang" class="form-control-label col-sm-3 text-md-right">Jenjang</label>
                            <div class="col-sm-6 col-md-9">
                                <select class="form-control form-control-sm" name="jenjang" id="jenjang" required>
                                    <option value="">Pilih Jenjang</option>
                                    <option value="SD">SD</option>
                                    <option value="MI">MI</option>
                                    <option value="SMP">SMP</option>
                                    <option value="MTS">MTS</option>
                                    <option value="SMA">SMA</option>
                                    <option value="MA">MA</option>
                                    <option value="SMK">SMK</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="appname" class="form-control-label col-sm-3 text-md-right">Alamat Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="alamat_sekolah" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="kelurahan" class="form-control-label col-sm-3 text-md-right">Kelurahan</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="kelurahan" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="kecamatan" class="form-control-label col-sm-3 text-md-right">Kecamatan</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="kecamatan" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="kota" class="form-control-label col-sm-3 text-md-right">Kota / Kabupaten</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="kota" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="provinsi" class="form-control-label col-sm-3 text-md-right">Provinsi</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="provinsi" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="website" class="form-control-label col-sm-3 text-md-right">Website</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="website" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="email" class="form-control-label col-sm-3 text-md-right">Email</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="email" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="nama_kepsek" class="form-control-label col-sm-3 text-md-right">Nama Kepala Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nama_kepsek" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="nip_kepsek" class="form-control-label col-sm-3 text-md-right">NIP Kepala Sekolah</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="nip_kepsek" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="kementrian" class="form-control-label col-sm-3 text-md-right">Kementrian</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="kementrian" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="key_encrypt" class="form-control-label col-sm-3 text-md-right">Kunci Encrypt</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="key_encrypt" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label for="api_token" class="form-control-label col-sm-3 text-md-right">Token Sinkronisasi</label>
                            <div class="col-sm-6 col-md-9">
                                <input type="text" name="api_token" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Site Logo</label>
                            <div class="col-sm-6 col-md-9">
                                <div class="custom-file">
                                    <input type="file" name="logo" class="custom-file-input form-control" id="logo">

                                </div>
                                <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                <img id="logoPreview" src="" alt="Logo Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Logo Kementrian</label>
                            <div class="col-sm-6 col-md-9">
                                <div class="custom-file">
                                    <input type="file" name="logomentri" class="custom-file-input form-control" id="logomentri">

                                </div>
                                <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                <img id="logoPreviewMentri" src="" alt="Logo Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label class="form-control-label col-sm-3 text-md-right">Kop Surat</label>
                            <div class="col-sm-6 col-md-9">
                                <div class="custom-file">
                                    <input type="file" name="kopsurat" class="custom-file-input form-control" id="kopsurat">

                                </div>
                                <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                <img id="kopsuratPreview" src="" alt="kopsurat Preview" style="max-width: 400px; display: none;">
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#helpnpsn').hide();
        // Ketika pengguna menyelesaikan ketik nomor NPSN
        $('#npsn').keyup(function() {
            // Ambil nilai input NPSN
            var npsn = $(this).val();

            // Lakukan pengecekan hanya jika NPSN memiliki panjang yang memadai
            if (npsn.length >= 8) {
                // Lakukan permintaan Ajax ke API
                $('#helpnpsn').show();
                $.ajax({
                    url: 'https://api-sekolah-indonesia.vercel.app/sekolah',
                    type: 'GET',
                    data: {
                        npsn: npsn
                    },
                    success: function(response) {
                        $('#helpnpsn').hide();
                        // Tampilkan data sekolah jika ditemukan
                        if (response.total_data > 0) {
                            var data = response.dataSekolah[0];
                            $('[name="nama_sekolah"]').val(data.sekolah);
                            $('#jenjang').val(data.bentuk);
                            $('[name="alamat_sekolah"]').val(data.alamat_jalan);
                            $('[name="kecamatan"]').val(data.kecamatan);
                            $('[name="kota"]').val(data.kabupaten_kota);
                            $('[name="provinsi"]').val(data.propinsi);

                        } else {

                        }
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan pesan kesalahan jika ada
                        $('#helpnpsn').hide();
                        console.error('Terjadi kesalahan: ' + status);
                    }
                });
            }
        });
        $.ajax({
            url: "<?= site_url('panel/pengaturan/sekolah/getdata') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                console.log(data);
                $('[name="appname"]').val(data.appname);
                $('[name="jenjang"]').val(data.jenjang);
                $('[name="nama_sekolah"]').val(data.nama_sekolah);
                $('[name="nama_kepsek"]').val(data.nama_kepsek);
                $('[name="nip_kepsek"]').val(data.nip_kepsek);
                $('[name="alamat_sekolah"]').val(data.alamat_sekolah);
                $('[name="key_encrypt"]').val(data.key_encrypt);
                $('[name="npsn"]').val(data.npsn);
                $('[name="nss"]').val(data.nss);
                $('[name="kelurahan"]').val(data.kelurahan);
                $('[name="kecamatan"]').val(data.kecamatan);
                $('[name="kota"]').val(data.kota);
                $('[name="provinsi"]').val(data.provinsi);
                $('[name="website"]').val(data.website);
                $('[name="email"]').val(data.email);
                $('[name="kementrian"]').val(data.kementrian);
                $('[name="api_token"]').val(data.api_token);
                $('#logoPreviewMentri').attr('src', '<?= base_url('assets/img/') ?>' + data.logokementrian).show();
                $('#logoPreview').attr('src', '<?= base_url('assets/img/') ?>' + data.logo).show();
                $('#kopsuratPreview').attr('src', '<?= base_url('assets/img/') ?>' + data.kop_surat).show();

            },
            error: function(jqXHR, textStatus, errorThrown) {
                //console.log(jqXHR);
                alert('Error get data from ajax');
            }
        });
        $('#logo').change(function() {
            readURL(this, '#logoPreview');
            $(this).next('.custom-file-label').html(event.target.files[0].name);
        });
        $('#kopsurat').change(function() {
            readURL(this, '#kopsuratPreview');
            $(this).next('.custom-file-label').html(event.target.files[0].name);
        });
        $('#logomentri').change(function() {
            readURL(this, '#logoPreviewMentri');
            $(this).next('.custom-file-label').html(event.target.files[0].name);
        });

        function readURL(input, previewElement) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $(previewElement).attr('src', e.target.result).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#settings-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            // console.log(formData);
            $.ajax({
                url: '<?= base_url('panel/pengaturan/sekolah/update') ?>', // Sesuaikan dengan URL CI4 Anda
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    // $('#responseMessage').html('<div class="alert alert-success" role="alert">' + response.message + '</div>');
                    Snackbar.show({
                        text: response.message,
                        pos: 'top-center'
                    });
                },
                error: function() {
                    // $('#responseMessage').html('<div class="alert alert-danger" role="alert">Terjadi kesalahan saat mengirim data.</div>');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>