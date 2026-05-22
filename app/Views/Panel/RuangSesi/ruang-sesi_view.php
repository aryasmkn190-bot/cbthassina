<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three p-3 shadow-sm rounded bg-white">

                <!-- Header -->
                <div class="widget-heading d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="mb-2 mb-sm-0">Manajemen Ruang & Sesi Peserta</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="btnSave" class="btn btn-success">
                            <i data-feather="save"></i> Simpan Penempatan
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSetSemua">
                            <i data-feather="layers"></i> Set Semua Ruang & Sesi
                        </button>
                    </div>
                </div>
                <div id="card-ruang-container" class="row g-3 mb-3">
                    <?php foreach ($ruang as $r): ?>
                        <div class="col-md-2">
                            <div class="card shadow-sm border-0 rounded-4 ruang-card h-100 text-center" data-id="<?= $r['id'] ?>" data-nama="<?= esc($r['nama']) ?>">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="icon-wrapper bg-light p-3 rounded-3 me-3 d-flex align-items-center justify-content-center">
                                        <i data-feather="printer" class="text-primary" width="28" height="28"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h6 class="fw-bold mb-1"><?= esc($r['nama']) ?></h6>
                                        <h3 class="fw-bolder text-dark mb-0" id="count-ruang-<?= $r['id'] ?>">
                                            <?= $jumlahPesertaPerRuang[$r['id']] ?? 0 ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>




                <!-- Filter -->
                <form id="form-filter" class="mb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select name="kelas_id" id="kelas_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabel Peserta -->
                <div id="peserta-container">
                    <div class="alert alert-info mb-0">
                        Pilih kelas terlebih dahulu untuk menampilkan daftar peserta.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal Set Semua -->
<div class="modal fade" id="modalSetSemua" tabindex="-1" aria-labelledby="modalSetSemuaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Semua Ruang & Sesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="setRuang" class="form-label">Ruang</label>
                    <select id="setRuang" class="form-select">
                        <option value="">-- Pilih Ruang --</option>
                        <?php foreach ($ruang as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="setSesi" class="form-label">Sesi</label>
                    <select id="setSesi" class="form-select">
                        <option value="">-- Pilih Sesi --</option>
                        <?php foreach ($sesi as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSetSemua" class="btn btn-primary">Terapkan ke Semua</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Pilih Sesi -->
<div class="modal fade" id="modalPilihSesi" tabindex="-1" aria-labelledby="modalPilihSesiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalPilihSesiLabel">Pilih Sesi untuk Daftar Hadir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ruang_id">

                <div class="mb-3">
                    <label for="sesi_id" class="form-label">Pilih Sesi</label>
                    <select id="sesi_id" class="form-select">
                        <option value="">-- Pilih Sesi --</option>
                        <?php foreach ($sesi as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlah_kolom" class="form-label">Jumlah Kolom Denah</label>
                    <input type="number" id="jumlah_kolom" class="form-control" min="1" value="5">
                    <small class="text-muted">Tentukan jumlah kolom per baris pada denah.</small>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-secondary" id="btnPrintDenah">
                    <i data-feather="map-pin"></i> Denah Tempat Duduk
                </button>
                <button class="btn btn-primary" id="btnPrintDaftarHadir">
                    <i data-feather="printer"></i> Daftar Hadir
                </button>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        // Tombol print denah
        $('#btnPrintDenah').on('click', function() {
            const ruangId = $('#ruang_id').val();
            const sesiId = $('#sesi_id').val();
            const jumlahKolom = $('#jumlah_kolom').val() || 5; // default 5 jika kosong

            if (!sesiId) {
                Swal.fire('Peringatan', 'Silakan pilih sesi terlebih dahulu.', 'warning');
                return;
            }

            // Buka tab baru dengan URL print denah, kirim parameter jumlah kolom
            const url = `<?= site_url('panel/ruangsesi/printDenah') ?>?ruang_id=${ruangId}&sesi_id=${sesiId}&cols=${jumlahKolom}`;
            window.open(url, '_blank');

            // Tutup modal
            $('#modalPilihSesi').modal('hide');
        });

        // Saat klik card ruang
        $('.ruang-card').on('click', function() {
            const ruangId = $(this).data('id');
            const ruangNama = $(this).data('nama');
            $('#ruang_id').val(ruangId);
            $('#modalPilihSesiLabel').text('Pilih Sesi untuk ' + ruangNama);
            $('#modalPilihSesi').modal('show');
        });

        // Tombol print
        $('#btnPrintDaftarHadir').on('click', function() {
            const ruangId = $('#ruang_id').val();
            const sesiId = $('#sesi_id').val();

            if (!sesiId) {
                Swal.fire('Peringatan', 'Silakan pilih sesi terlebih dahulu.', 'warning');
                return;
            }

            const url = "<?= site_url('panel/ruangsesi/print-daftarhadir') ?>?ruang_id=" + ruangId + "&sesi_id=" + sesiId;
            window.open(url, '_blank');
            $('#modalPilihSesi').modal('hide');
        });
        // Saat klik tombol tampilkan
        $('#form-filter').on('submit', function(e) {
            e.preventDefault();
            const kelasId = $('#kelas_id').val();

            if (!kelasId) {
                Swal.fire('Peringatan', 'Silakan pilih kelas terlebih dahulu.', 'warning');
                return;
            }

            $.ajax({
                url: "<?= site_url('panel/ruangsesi/get-peserta') ?>",
                type: "POST",
                data: {
                    kelas_id: kelasId
                },
                beforeSend: function() {
                    $('#peserta-container').html('<div class="text-center p-3">Memuat data peserta...</div>');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const peserta = response.data.peserta;
                        const ruang = response.data.ruang;
                        const sesi = response.data.sesi;

                        if (peserta.length === 0) {
                            $('#peserta-container').html('<div class="alert alert-warning">Tidak ada peserta di kelas ini.</div>');
                            return;
                        }

                        // Buat tabel peserta
                        let html = `
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-peserta">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Nama Peserta</th>
                                        <th>Username</th>
                                        <th width="25%">Ruang</th>
                                        <th width="20%">Sesi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                        peserta.forEach((p, index) => {
                            html += `
                            <tr data-id="${p.id}">
                                <td>${index + 1}</td>
                                <td>${p.nama}</td>
                                <td>${p.username}</td>
                                <td>
                                    <select class="form-select form-select-sm ruang-select">
                                        <option value="">-- Pilih Ruang --</option>`;
                            ruang.forEach(r => {
                                const selected = (p.ruang_id == r.id) ? 'selected' : '';
                                html += `<option value="${r.id}" ${selected}>${r.nama}</option>`;
                            });
                            html += `</select></td>
                                <td>
                                    <select class="form-select form-select-sm sesi-select">
                                        <option value="">-- Pilih Sesi --</option>`;
                            sesi.forEach(s => {
                                const selected = (p.sesi_id == s.id) ? 'selected' : '';
                                html += `<option value="${s.id}" ${selected}>${s.nama}</option>`;
                            });
                            html += `</select></td>
                            </tr>`;
                        });

                        html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                        $('#peserta-container').html(html);

                    } else {
                        $('#peserta-container').html('<div class="alert alert-danger">Gagal memuat data peserta.</div>');
                    }
                },
                error: function() {
                    $('#peserta-container').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>');
                }
            });
        });


        // Fungsi simpan penempatan (dipakai ulang)
        function simpanPenempatan() {
            const data = [];
            $('#table-peserta tbody tr').each(function() {
                const pesertaId = $(this).data('id');
                const ruangId = $(this).find('.ruang-select').val();
                const sesiId = $(this).find('.sesi-select').val();
                data.push({
                    peserta_id: pesertaId,
                    ruang_id: ruangId,
                    sesi_id: sesiId
                });
            });

            if (data.length === 0) {
                Swal.fire('Peringatan', 'Tidak ada data peserta untuk disimpan.', 'warning');
                return;
            }

            $.ajax({
                url: "<?= site_url('panel/ruangsesi/simpanPenempatan') ?>",
                type: "POST",
                data: {
                    data: data
                },
                success: function(res) {
                    Swal.fire('Berhasil', 'Penempatan peserta berhasil disimpan.', 'success');
                },
                error: function() {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan data.', 'error');
                }
            });
        }

        // Tombol simpan manual
        $(document).on('click', '#btnSave', function() {
            simpanPenempatan();
        });

        // Tombol Set Semua → langsung simpan
        $(document).on('click', '#btnSetSemua', function() {
            const ruangId = $('#setRuang').val();
            const sesiId = $('#setSesi').val();

            if (!ruangId && !sesiId) {
                Swal.fire('Peringatan', 'Pilih Ruang atau Sesi terlebih dahulu.', 'warning');
                return;
            }

            $('#table-peserta tbody tr').each(function() {
                if (ruangId) $(this).find('.ruang-select').val(ruangId);
                if (sesiId) $(this).find('.sesi-select').val(sesiId);
            });

            $('#modalSetSemua').modal('hide');

            // Simpan otomatis setelah diterapkan
            simpanPenempatan();
        });






    });
</script>
<?= $this->endSection() ?>