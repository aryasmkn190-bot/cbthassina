<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .profile-card {
        border-radius: 16px;
        overflow: hidden;
        border: none;
    }
    .profile-header-gradient {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        background-color: #f1f5f9;
    }
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #4f46e5;
    }
    .nav-tabs-custom .nav-link.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: transparent;
    }
    body.dark .nav-tabs-custom {
        border-bottom-color: #334155;
    }
    body.dark .nav-tabs-custom .nav-link {
        color: #94a3b8;
    }
    body.dark .nav-tabs-custom .nav-link.active {
        color: #818cf8;
        border-bottom-color: #818cf8;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container-xxl p-0 mt-4 mb-5">
    <div class="row g-4">
        
        <!-- Left Side: Mini profile summary -->
        <div class="col-12 col-lg-4">
            <div class="card profile-card shadow-sm bg-white mb-4">
                <div class="profile-header-gradient">
                    <img src="<?= base_url() ?>src/assets/img/profile-30.png" class="profile-avatar mb-3" alt="Avatar">
                    <h5 class="fw-bold mb-1 text-white"><?= esc($peserta['nama']) ?></h5>
                    <p class="mb-0 text-white-50 small">Kelas: <?= esc($peserta['nama_kelas'] ?? '-') ?></p>
                </div>
                <div class="card-body p-4 text-dark">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small fw-semibold">NISN</span>
                        <span class="fw-bold text-end"><?= esc($peserta['nisn'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small fw-semibold">NIK</span>
                        <span class="fw-semibold text-end"><?= esc($peserta['nik'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="text-muted small fw-semibold">Status Data Pokok</span>
                        <?php if ($pendingRequest): ?>
                            <span class="badge bg-warning rounded-pill px-2.5 py-1 text-dark">Sedang Diverifikasi</span>
                        <?php else: ?>
                            <span class="badge bg-success rounded-pill px-2.5 py-1 text-white">Terverifikasi</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Pending Request Alert -->
            <?php if ($pendingRequest): ?>
                <?php 
                    $dataBaru = json_decode($pendingRequest['data_baru'], true);
                ?>
                <div class="alert alert-warning shadow-sm rounded-4 p-3 border-0">
                    <div class="d-flex mb-2">
                        <i data-feather="clock" class="text-warning me-2" style="width: 20px; height: 20px;"></i>
                        <h6 class="alert-heading fw-bold mb-0 text-dark">Pengajuan Edit Data Penting</h6>
                    </div>
                    <p class="small text-muted mb-2">Anda telah mengajukan perubahan data penting berikut yang sedang menunggu verifikasi admin:</p>
                    <ul class="small mb-0 text-dark pl-3">
                        <?php foreach ($dataBaru as $key => $val): ?>
                            <li><strong><?= ucwords(str_replace('_', ' ', $key)) ?>:</strong> <?= esc($val) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Side: Edit Form tabs -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form id="form_profil" class="needs-validation" novalidate>
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h4 class="fw-bold mb-0 text-dark">Biodata Pokok Pendidikan</h4>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                            <i data-feather="save" class="me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                    <!-- Custom Tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi" type="button" role="tab">Data Pribadi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="domisili-tab" data-bs-toggle="tab" data-bs-target="#domisili" type="button" role="tab">Alamat & Kontak</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ortu-tab" data-bs-toggle="tab" data-bs-target="#ortu" type="button" role="tab">Orang Tua / Wali</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="akun-tab" data-bs-toggle="tab" data-bs-target="#akun" type="button" role="tab">Akun</button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content text-dark" id="profileTabsContent">
                        
                        <!-- 1. DATA PRIBADI (RESTRICTED FIELDS) -->
                        <div class="tab-pane fade show active" id="pribadi" role="tabpanel">
                            <?php if ($pendingRequest): ?>
                                <div class="alert alert-light border text-muted small p-2 rounded-3 mb-3">
                                    <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i> Kolom data pribadi dikunci sementara karena pengajuan perubahan sebelumnya sedang diproses.
                                </div>
                            <?php endif; ?>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" value="<?= esc($peserta['nama']) ?>" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">NISN (Nomor Induk Siswa Nasional) <span class="text-danger">*</span></label>
                                    <input type="text" name="nisn" class="form-control" value="<?= esc($peserta['nisn']) ?>" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">NIS / NIPD (Nomor Induk Sekolah)</label>
                                    <input type="text" name="nis" class="form-control" value="<?= esc($peserta['nis'] ?? '') ?>" <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">NIK Siswa (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control" value="<?= esc($peserta['nik'] ?? '') ?>" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="<?= esc($peserta['tempat_lahir'] ?? '') ?>" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= esc($peserta['tanggal_lahir'] ?? '') ?>" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-control" required <?= $pendingRequest ? 'disabled' : '' ?>>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L" <?= ($peserta['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= ($peserta['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-muted">Kelas Aktif (Dapodik)</label>
                                    <input type="text" class="form-control bg-light" value="<?= esc($peserta['nama_kelas'] ?? '-') ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ALAMAT & KONTAK (UNRESTRICTED) -->
                        <div class="tab-pane fade" id="domisili" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nomor HP / WhatsApp</label>
                                    <input type="text" name="telepon" class="form-control" value="<?= esc($peserta['telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Email Siswa</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($peserta['email'] ?? '') ?>" placeholder="nama@email.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat Jalan</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Nama Jalan, No. Rumah, dll"><?= esc($peserta['alamat'] ?? '') ?></textarea>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label fw-semibold">RT</label>
                                    <input type="text" name="rt" class="form-control" value="<?= esc($peserta['rt'] ?? '') ?>" placeholder="000">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label fw-semibold">RW</label>
                                    <input type="text" name="rw" class="form-control" value="<?= esc($peserta['rw'] ?? '') ?>" placeholder="000">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Dusun</label>
                                    <input type="text" name="dusun" class="form-control" value="<?= esc($peserta['dusun'] ?? '') ?>" placeholder="Nama Dusun/Kampung">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Kelurahan / Desa</label>
                                    <input type="text" name="kelurahan" class="form-control" value="<?= esc($peserta['kelurahan'] ?? '') ?>" placeholder="Nama Desa/Lurah">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Kecamatan</label>
                                    <input type="text" name="kecamatan" class="form-control" value="<?= esc($peserta['kecamatan'] ?? '') ?>" placeholder="Nama Kecamatan">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Kode Pos</label>
                                    <input type="text" name="kode_pos" class="form-control" value="<?= esc($peserta['kode_pos'] ?? '') ?>" placeholder="xxxxx">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Jenis Tempat Tinggal</label>
                                    <select name="jenis_tinggal" class="form-control">
                                        <option value="Bersama Orang Tua" <?= ($peserta['jenis_tinggal'] ?? '') === 'Bersama Orang Tua' ? 'selected' : '' ?>>Bersama Orang Tua</option>
                                        <option value="Kos" <?= ($peserta['jenis_tinggal'] ?? '') === 'Kos' ? 'selected' : '' ?>>Kos</option>
                                        <option value="Wali" <?= ($peserta['jenis_tinggal'] ?? '') === 'Wali' ? 'selected' : '' ?>>Wali / Saudara</option>
                                        <option value="Asrama" <?= ($peserta['jenis_tinggal'] ?? '') === 'Asrama' ? 'selected' : '' ?>>Asrama / Pondok</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Alat Transportasi</label>
                                    <input type="text" name="alat_transportasi" class="form-control" value="<?= esc($peserta['alat_transportasi'] ?? '') ?>" placeholder="Contoh: Sepeda Motor, Jalan Kaki, Bus Umum">
                                </div>
                            </div>
                        </div>

                        <!-- 3. ORANG TUA / WALI (UNRESTRICTED) -->
                        <div class="tab-pane fade" id="ortu" role="tabpanel">
                            <!-- AYAH -->
                            <div class="p-3 border rounded-3 mb-4">
                                <h6 class="fw-bold mb-3 text-primary"><i data-feather="user" class="me-1"></i> Data Ayah Kandung</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" class="form-control" value="<?= esc($peserta['nama_ayah'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">NIK Ayah</label>
                                        <input type="text" name="nik_ayah" class="form-control" value="<?= esc($peserta['nik_ayah'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Tahun Lahir Ayah</label>
                                        <input type="number" name="tahun_lahir_ayah" class="form-control" value="<?= esc($peserta['tahun_lahir_ayah'] ?? '') ?>" placeholder="19xx">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pendidikan Ayah</label>
                                        <input type="text" name="pendidikan_ayah" class="form-control" value="<?= esc($peserta['pendidikan_ayah'] ?? '') ?>" placeholder="Contoh: SMA, S1, S2">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pekerjaan Ayah</label>
                                        <input type="text" name="pekerjaan_ayah" class="form-control" value="<?= esc($peserta['pekerjaan_ayah'] ?? '') ?>" placeholder="Contoh: Karyawan Swasta, Petani">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Rentang Penghasilan Ayah</label>
                                        <input type="text" name="penghasilan_ayah" class="form-control" value="<?= esc($peserta['penghasilan_ayah'] ?? '') ?>" placeholder="Contoh: Rp 2.000.000 - Rp 5.000.000">
                                    </div>
                                </div>
                            </div>

                            <!-- IBU -->
                            <div class="p-3 border rounded-3 mb-4">
                                <h6 class="fw-bold mb-3 text-danger"><i data-feather="user" class="me-1"></i> Data Ibu Kandung</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" class="form-control" value="<?= esc($peserta['nama_ibu'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">NIK Ibu</label>
                                        <input type="text" name="nik_ibu" class="form-control" value="<?= esc($peserta['nik_ibu'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Tahun Lahir Ibu</label>
                                        <input type="number" name="tahun_lahir_ibu" class="form-control" value="<?= esc($peserta['tahun_lahir_ibu'] ?? '') ?>" placeholder="19xx">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pendidikan Ibu</label>
                                        <input type="text" name="pendidikan_ibu" class="form-control" value="<?= esc($peserta['pendidikan_ibu'] ?? '') ?>" placeholder="Contoh: SMA, S1, D3">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pekerjaan Ibu</label>
                                        <input type="text" name="pekerjaan_ibu" class="form-control" value="<?= esc($peserta['pekerjaan_ibu'] ?? '') ?>" placeholder="Contoh: Ibu Rumah Tangga, Guru">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Rentang Penghasilan Ibu</label>
                                        <input type="text" name="penghasilan_ibu" class="form-control" value="<?= esc($peserta['penghasilan_ibu'] ?? '') ?>" placeholder="Contoh: Kurang dari Rp 1.000.000">
                                    </div>
                                </div>
                            </div>

                            <!-- WALI -->
                            <div class="p-3 border rounded-3">
                                <h6 class="fw-bold mb-3 text-success"><i data-feather="user" class="me-1"></i> Data Wali (Opsional)</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Wali</label>
                                        <input type="text" name="nama_wali" class="form-control" value="<?= esc($peserta['nama_wali'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">NIK Wali</label>
                                        <input type="text" name="nik_wali" class="form-control" value="<?= esc($peserta['nik_wali'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Tahun Lahir Wali</label>
                                        <input type="number" name="tahun_lahir_wali" class="form-control" value="<?= esc($peserta['tahun_lahir_wali'] ?? '') ?>" placeholder="19xx">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pendidikan Wali</label>
                                        <input type="text" name="pendidikan_wali" class="form-control" value="<?= esc($peserta['pendidikan_wali'] ?? '') ?>">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Pekerjaan Wali</label>
                                        <input type="text" name="pekerjaan_wali" class="form-control" value="<?= esc($peserta['pekerjaan_wali'] ?? '') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Rentang Penghasilan Wali</label>
                                        <input type="text" name="penghasilan_wali" class="form-control" value="<?= esc($peserta['penghasilan_wali'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. AKUN (PASSWORD) -->
                        <div class="tab-pane fade" id="akun" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-muted">Username Login</label>
                                    <input type="text" class="form-control bg-light" value="<?= esc($peserta['username']) ?>" readonly>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Ubah Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <small class="text-muted">Masukkan minimal 5 karakter untuk mengubah password.</small>
                                </div>
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
<script>
    $('#form_profil').on('submit', function(e) {
        e.preventDefault();

        if (this.checkValidity() === false) {
            $(this).addClass('was-validated');
            return;
        }

        // Enable all inputs before serialize so that disabled fields (restricted ones) are sent to check for changes
        const disabledFields = $(this).find(':disabled');
        disabledFields.prop('disabled', false);
        const data = $(this).serialize();
        disabledFields.prop('disabled', true); // Re-disable them immediately

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Data profil Anda akan disimpan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('siswa/profil/update') ?>', data, function(res) {
                    if (res.status) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#3b82f6',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        let errHtml = '';
                        if (typeof res.message === 'object') {
                            $.each(res.message, (k, v) => errHtml += v + '\n');
                        } else {
                            errHtml = res.message;
                        }
                        Swal.fire('Error', errHtml, 'error');
                    }
                }, 'json');
            }
        });
    });
</script>
<?= $this->endSection(); ?>
