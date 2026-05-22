<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<style>
    .ba-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
    }



    .action-icons svg {
        width: 20px;
        height: 20px;
        transition: 0.25s;
        transition: transform 0.2s ease, filter 0.2s ease;
        cursor: pointer;
    }



    .action-icons a:hover svg,
    .action-icons button:hover svg {
        transform: scale(1.2);
        filter: brightness(0.8);
    }

    .action-icons a:active svg,
    .action-icons button:active svg {
        transform: scale(0.9);
    }
</style>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">

        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Berita Acara Ujian</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah Berita Acara
                    </button>
                    <button id="refreshList" class="btn btn-outline-secondary">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Filter Ruang & Sesi (kelas dihapus) -->
            <div class="d-flex gap-2 mb-3 flex-wrap">

                <select id="filterRuang" class="form-select form-control-sm form-select form-control-sm-sm" style="width:auto;">
                    <option value="">Semua Ruang</option>
                    <?php foreach ($ruang as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                    <?php endforeach ?>
                </select>

                <select id="filterSesi" class="form-select form-control-sm form-select form-control-sm-sm" style="width:auto;">
                    <option value="">Semua Sesi</option>
                    <?php foreach ($sesi as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= esc($s['nama']) ?></option>
                    <?php endforeach ?>
                </select>

                <div class="input-group" style="flex:1;">
                    <span class="input-group-text bg-white">
                        <i data-feather="search"></i>
                    </span>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari berita acara...">
                </div>
            </div>

            <!-- List -->
            <div id="baList" class="d-flex flex-column gap-3"></div>

            <!-- Pagination -->
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
            </nav>

        </div>

    </div>
</div>

<!-- Modal Form Berita Acara -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form" class="modal-content needs-validation" novalidate>

            <div class="modal-header">
                <h5 class="modal-title">Form Berita Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="modal-body">

                <div id="errorMessages"></div>

                <input type="hidden" name="id">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label>Nama Ujian</label>
                        <select name="ujian_id" class="form-select form-control-sm" required>
                            <option value="">-- Pilih Ujian --</option>
                            <?php foreach ($ujian as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['nama_jenis_ujian']) ?> - <?= esc($u['nama_ujian']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Ruang</label>
                        <select name="ruang_id" class="form-select form-control-sm" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($ruang as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Sesi</label>
                        <select name="sesi_id" class="form-select form-control-sm" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($sesi as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= esc($s['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label>Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label>Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label>Peserta Seharusnya</label>
                        <input type="number" name="jumlah_peserta_seharusnya" class="form-control form-control-sm" value="0">
                    </div>

                    <div class="col-md-4">
                        <label>Hadir</label>
                        <input type="number" name="jumlah_hadir" class="form-control form-control-sm" value="0">
                    </div>

                    <div class="col-md-4">
                        <label>Tidak Hadir</label>
                        <input type="number" name="jumlah_tidak_hadir" class="form-control form-control-sm" value="0">
                    </div>

                    <div class="col-md-12">
                        <label>Daftar Peserta Tidak Hadir</label>
                        <textarea name="peserta_tidak_hadir" class="form-control form-control-sm"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label>Proktor - Nama</label>
                        <input type="text" name="proktor_nama" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label>Proktor - NIP</label>
                        <input type="text" name="proktor_nip" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label>Pengawas - Nama</label>
                        <input type="text" name="pengawas_nama" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label>Pengawas - NIP</label>
                        <input type="text" name="pengawas_nip" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label>Kepala Sekolah - Nama</label>
                        <input type="text" name="kepala_sekolah_nama" value="<?= $setting->nama_kepsek ?>" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label>Kepala Sekolah - NIP</label>
                        <input type="text" name="kepala_sekolah_nip" value="<?= $setting->nip_kepsek ?>" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-12">
                        <label>Catatan / Kendala</label>
                        <textarea name="catatan" class="form-control form-control-sm"></textarea>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" onclick="save()">Simpan</button>
            </div>

        </form>
    </div>
</div>


<?= $this->endSection(); ?>


<?= $this->section('pagejs'); ?>
<script>
    let allData = [];
    let currentPage = 1;
    const perPage = 5;
    let save_method = 'add';

    $(document).ready(function() {
        loadData();

        $('#searchBox, #filterRuang, #filterSesi').on('input change', function() {
            currentPage = 1;
            renderList();
        });

        $('#refreshList').on('click', function() {
            loadData();
        });
    });

    // ==============================
    // LOAD LIST DATA
    // ==============================
    function loadData() {
        $.get('<?= base_url('panel/beritaacara/list') ?>', function(res) {
            allData = res.data || [];
            renderList();
        }, 'json');
    }

    function renderList() {
        let search = $('#searchBox').val().toLowerCase();
        let filterRuang = $('#filterRuang').val();
        let filterSesi = $('#filterSesi').val();

        let filtered = allData.filter(item =>
            (!filterRuang || item.ruang_id == filterRuang) &&
            (!filterSesi || item.sesi_id == filterSesi) &&
            (
                (item.nama_ujian || '').toLowerCase().includes(search) ||
                (item.pengawas_nama || '').toLowerCase().includes(search)
            )
        );
        let totalPages = Math.ceil(filtered.length / perPage);
        let start = (currentPage - 1) * perPage;
        let paginated = filtered.slice(start, start + perPage);

        let html = '';

        if (paginated.length === 0) {
            html = `
        <div class="alert alert-light text-center">
            Tidak ada data ditemukan.
        </div>`;
        } else {
            paginated.forEach(row => {

                html += `
<div class="card p-3 shadow-sm ba-card border-0 rounded-3 mb-3" style="transition:.25s;">

    <div class="d-flex justify-content-between">

        <!-- LEFT -->
        <div class="flex-grow-1">

            <h6 class="fw-semibold mb-2">${row.nama_ujian}</h6>

            <!-- Chips -->
            <div class="d-flex flex-wrap gap-2 mb-2">
             <span class="badge bg-light text-danger border px-2 py-1 rounded-pill">
                    <i data-feather="map-pin" width="12" class="me-1"></i>${row.nama_jenis_ujian}
                </span>
                <span class="badge bg-light text-primary border px-2 py-1 rounded-pill">
                    <i data-feather="map-pin" width="12" class="me-1"></i>${row.nama_ruang}
                </span>
                <span class="badge bg-light text-info border px-2 py-1 rounded-pill">
                    <i data-feather="clock" width="12" class="me-1"></i>Sesi ${row.nama_sesi}
                </span>
            </div>

            <!-- Compact Info -->
            <div class="text-muted small d-flex flex-wrap gap-3 mb-2">

                <span class="d-flex align-items-center">
                    <i data-feather="user-check" width="13" class="me-1"></i>
                    ${row.pengawas_nama || '-'}
                </span>

                <span class="d-flex align-items-center">
                    <i data-feather="clock" width="13" class="me-1"></i>
                    ${row.jam_mulai}–${row.jam_selesai}
                </span>

                <span class="d-flex align-items-center">
                    <i data-feather="calendar" width="13" class="me-1"></i>
                    ${row.tanggal}
                </span>

            </div>

            <!-- Mini Stats -->
            <div class="d-flex flex-wrap gap-2 mb-1">

                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 small">
                    S: <b>${row.jumlah_peserta_seharusnya}</b>
                </span>

                <span class="badge bg-success-subtle text-success px-2 py-1 small">
                    H: <b>${row.jumlah_hadir}</b>
                </span>

                <span class="badge bg-danger-subtle text-danger px-2 py-1 small">
                    TH: <b>${row.jumlah_tidak_hadir}</b>
                </span>

            </div>

        </div>

      <!-- ACTIONS -->
        <div class="d-flex align-items-center gap-3 ms-3">

            <button class="text-primary bg-transparent border-0 p-0 edit-btn" 
                    data-id="${row.id}" title="Edit">
                <i data-feather="edit" width="18"></i>
            </button>

            <button class="text-danger bg-transparent border-0 p-0 delete-btn" 
                    data-id="${row.id}" title="Hapus">
                <i data-feather="trash-2" width="18"></i>
            </button>

            <button class="text-secondary bg-transparent border-0 p-0 print-btn" 
                    data-id="${row.id}" title="Print">
                <i data-feather="printer" width="18"></i>
            </button>

        </div>

    </div>

    <!-- Catatan (minimalis) -->
    <div class="mt-2 p-2 rounded-2 bg-light small text-muted border">
        ${row.catatan?.trim() ? row.catatan : '<i>Tidak ada catatan.</i>'}
    </div>

</div>`;

            });
        }

        $('#baList').html(html);
        renderPagination(totalPages);
        feather.replace();
    }


    function renderPagination(totalPages) {
        let html = `
        <li class="page-item ${currentPage == 1 ? 'disabled' : ''}">
            <button class="page-link" onclick="gotoPage(${currentPage - 1})">
                <i data-feather="chevron-left"></i>
            </button>
        </li>`;

        for (let i = 1; i <= totalPages; i++) {
            html += `
            <li class="page-item ${i == currentPage ? 'active' : ''}">
                <button class="page-link" onclick="gotoPage(${i})">${i}</button>
            </li>`;
        }

        html += `
        <li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">
            <button class="page-link" onclick="gotoPage(${currentPage + 1})">
                <i data-feather="chevron-right"></i>
            </button>
        </li>`;

        $('#pagination').html(html);
        feather.replace();
    }

    function gotoPage(p) {
        currentPage = p;
        renderList();
    }

    // ==============================
    // ADD
    // ==============================
    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('#errorMessages').html('');
        $('[name=id]').val('');

        $('.modal-title').text('Tambah Berita Acara');
        $('#modal_form').modal('show');
    }

    // ==============================
    // SAVE (ADD / EDIT) — versi fix
    // ==============================
    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/beritaacara/create') ?>' :
            '<?= base_url('panel/beritaacara/update') ?>/' + $('[name="id"]').val();

        // Validasi form HTML5
        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        // Hapus pesan error
        $('#errorMessages').html('');

        $.post(url, $('#form').serialize(), function(res) {

            if (res.status) {
                $('#modal_form').modal('hide');
                loadData();

                Snackbar.show({
                    text: res.message || 'Berhasil disimpan!',
                    pos: 'top-center'
                });

            } else {

                // Jika error array (validasi server)
                if (typeof res.message === 'object') {
                    let errors = '<div class="alert alert-danger"><ul>';
                    $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                    errors += '</ul></div>';
                    $('#errorMessages').html(errors);
                } else {
                    // Jika error string biasa
                    $('#errorMessages').html(`
                    <div class="alert alert-danger">${res.message}</div>
                `);
                }
            }

        }, 'json');
    }

    // PRINT
    $(document).on('click', '.print-btn', function() {
        let id = $(this).data('id');
        window.open("<?= base_url() ?> panel/beritaacara/print/" + id, '_blank');
    });

    // ==============================
    // EDIT DATA
    // ==============================
    $(document).on('click', '.edit-btn', function() {
        save_method = 'edit';
        $('#form')[0].reset();
        $('#errorMessages').html('');

        let id = $(this).data('id');
        let item = allData.find(a => a.id == id);

        if (!item) {
            Snackbar.show({
                text: "Data tidak ditemukan!",
                pos: 'top-center'
            });
            return;
        }

        // Set semua field
        $('[name=id]').val(item.id);
        $('[name=ujian_id]').val(item.ujian_id);
        $('[name=ruang_id]').val(item.ruang_id);
        $('[name=sesi_id]').val(item.sesi_id);
        $('[name=tanggal]').val(item.tanggal);
        $('[name=jam_mulai]').val(item.jam_mulai);
        $('[name=jam_selesai]').val(item.jam_selesai);
        $('[name=jumlah_peserta_seharusnya]').val(item.jumlah_peserta_seharusnya);
        $('[name=jumlah_hadir]').val(item.jumlah_hadir);
        $('[name=jumlah_tidak_hadir]').val(item.jumlah_tidak_hadir);
        $('[name=peserta_tidak_hadir]').val(item.peserta_tidak_hadir);
        $('[name=proktor_nama]').val(item.proktor_nama);
        $('[name=proktor_nip]').val(item.proktor_nip);
        $('[name=pengawas_nama]').val(item.pengawas_nama);
        $('[name=pengawas_nip]').val(item.pengawas_nip);
        $('[name=kepala_sekolah_nama]').val(item.kepala_sekolah_nama);
        $('[name=kepala_sekolah_nip]').val(item.kepala_sekolah_nip);
        $('[name=catatan]').val(item.catatan);

        // Tampilkan modal
        $('.modal-title').text('Edit Berita Acara');
        $('#modal_form').modal('show');
    });

    // ==============================
    // DELETE DATA
    // ==============================
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        hapus(id);
    });


    function hapus(id) {

        Swal.fire({
            title: "Hapus Berita Acara?",
            text: "Data yang dihapus tidak dapat dikembalikan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: "Menghapus...",
                html: "Mohon tunggu sebentar",
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            $.ajax({
                url: "<?= base_url('panel/beritaacara/delete') ?>/" + id,
                type: "DELETE",
                dataType: "JSON",
                success: function(res) {
                    Swal.close();

                    if (res.status) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: "Berita acara berhasil dihapus"
                        });

                        loadData();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: res.message ?? "Tidak dapat menghapus data!"
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Koneksi gagal!"
                    });
                }
            });

        });
    }
</script>

<?= $this->endSection(); ?>