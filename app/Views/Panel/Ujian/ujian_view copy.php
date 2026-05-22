<?php $this->extend('Layout/main'); ?>
<?php $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex justify-content-between align-items-center">
                    <h5 class="">Manajemen Ujian</h5>
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="button" onclick="add()" class="btn btn-primary">
                            <i data-feather="plus-circle"></i> Tambah
                        </button>
                        <button type="button" id="refreshtable" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table" id="ujianTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Ujian</th>
                                    <th>Bank Soal</th>
                                    <th>Peserta</th>
                                    <th>Token</th>
                                    <th>Pengaturan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tambahPesertaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formTambahPeserta" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ujian_id">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_tingkat" class="form-label">Tingkat</label>
                        <select id="filter_tingkat" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($tingkat as $t): ?>
                                <option value="<?= esc($t['id']) ?>"><?= esc($t['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label for="filter_kelas" class="form-label">Kelas</label>
                        <select id="filter_kelas" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= esc($k['id']) ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_jurusan" class="form-label">Jurusan</label>
                        <select id="filter_jurusan" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($jurusan as $j): ?>
                                <option value="<?= esc($j['id']) ?>"><?= esc($j['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_agama" class="form-label">Agama</label>
                        <select id="filter_agama" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <?php foreach ($agama as $a): ?>
                                <option value="<?= esc($a['id']) ?>"><?= esc($a['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-5">
                        <label>Belum Ditambahkan (<span id="count-belum">0</span>)</label>
                        <select id="list-belum" class="form-select" multiple size="10">
                            <!-- Diisi via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-2 d-flex flex-column justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-tambah">
                            <i data-feather="arrow-right-circle"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btn-hapus">
                            <i data-feather="arrow-left-circle"></i>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label>Sudah Ditambahkan (<span id="count-sudah">0</span>)</label>
                        <select name="peserta_id[]" id="list-sudah" class="form-select" multiple size="10">
                            <!-- Akan dipindahkan dari kiri ke kanan -->
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Ujian</label>
                        <input type="text" name="nama_ujian" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kode Ujian</label>
                        <input type="text" name="kode_ujian" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Token</label>
                        <input type="text" name="token" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>Bank Soal</label>
                        <select name="bank_soal_id" class="form-control form-control-sm">
                            <?php foreach ($banks as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= esc($b['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Durasi Ujian (menit)</label>
                        <input type="number" name="durasi_ujian" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Minimal Durasi (menit)</label>
                        <input type="number" name="minimal_durasi" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Dibuat Oleh</label>
                        <select name="created_by" class="form-control form-control-sm">
                            <?php foreach ($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Pengaturan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="acak_soal" value="1" id="acak_soal">
                            <label class="form-check-label" for="acak_soal">Acak Soal</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="acak_opsi" value="1" id="acak_opsi">
                            <label class="form-check-label" for="acak_opsi">Acak Opsi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pakai_token" value="1" id="pakai_token">
                            <label class="form-check-label" for="pakai_token">Pakai Token</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tampil_nilai" value="1" id="tampil_nilai">
                            <label class="form-check-label" for="tampil_nilai">Tampilkan Nilai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tampil_pembahasan" value="1" id="tampil_pembahasan">
                            <label class="form-check-label" for="tampil_pembahasan">Tampilkan Pembahasan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pakai_webcam" value="1" id="pakai_webcam">
                            <label class="form-check-label" for="pakai_webcam">Pakai Webcam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="single_login" value="1" id="single_login">
                            <label class="form-check-label" for="single_login">Single Login Device</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="perangkat_terkunci" value="1" id="perangkat_terkunci">
                            <label class="form-check-label" for="perangkat_terkunci">Perangkat Terkunci</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="save()" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>
<?php $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        const base_url = '<?= base_url() ?>';
        $('#ujianTable').DataTable({

            ajax: {
                url: '<?= base_url('panel/ujian/list') ?>',
                type: 'GET',
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark">${row.nama_ujian}</span>
                                <span class="badge bg-light text-secondary mt-1" style="width: fit-content;">
                                    <i class="bi bi-hash me-1"></i>${row.kode_ujian}
                                </span>
                            </div>
                        `;
                    }
                },

                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-primary">${row.nama_bank_soal}</span>
                                <span class="badge bg-light text-dark mt-1" style="width: fit-content;">
                                    <i class="bi bi-list-ol me-1"></i>${row.jumlah_soal} Soal
                                </span>
                            </div>
                        `;
                    }
                },

                {
                    data: 'jumlah_peserta',
                    render: function(d, type, row) {
                        if (parseInt(d) === 0) {
                            return `<button class="btn btn-sm btn-primary tambah-peserta-btn"
                                            data-id="${row.id}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#tambahPesertaModal">
                                        <i class="feather-plus-circle"></i> Tambah Peserta
                                    </button>`;

                        } else {
                            return `<button class="badge bg-dark" data-id="${row.id}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#tambahPesertaModal" title="tambah peserta">${d} peserta</button>`;
                        }
                    }
                },

                {
                    data: null,
                    render: row => `
        <div class="d-flex flex-column gap-1">
            <span class="badge bg-light text-dark">🗓️ Tgl Buka: <strong>${row.waktu_mulai}</strong></span>
            <span class="badge bg-light text-dark">🗓️ Tgl Tutup: <strong>${row.waktu_selesai}</strong></span>
            <span class="badge bg-primary">⏱️ Durasi: <strong>${row.durasi_ujian ?? 0} menit</strong></span>
            <span class="badge bg-secondary">Single_login: <strong>${row.single_login == '1' ? 'Ya' : 'Tidak'}</strong></span>
            <span class="badge bg-warning text-dark">🔑 Pakai Token: <strong>${row.pakai_token == '1' ? 'Ya' : 'Tidak'}</strong></span>
            ${row.pakai_token == '1' ? `<span class="badge bg-danger text-light">🔐 Token: <strong>${row.token}</strong></span>` : ''}
            
        </div>
    `

                },
                {
                    data: null,
                    render: row => `
        <div class="d-flex flex-column gap-1">
          
            <span class="badge bg-success">🔀 Acak Soal: <strong>${row.acak_soal == '1' ? 'Ya' : 'Tidak'}</strong></span>
            <span class="badge bg-success">🔁 Acak Opsi: <strong>${row.acak_opsi == '1' ? 'Ya' : 'Tidak'}</strong></span>
            
            <span class="badge bg-info text-dark">📊 Tampilkan Nilai: <strong>${row.tampil_nilai == '1' ? 'Ya' : 'Tidak'}</strong></span>
            <span class="badge bg-info text-dark">📚 Tampilkan Pembahasan: <strong>${row.tampil_pembahasan == '1' ? 'Ya' : 'Tidak'}</strong></span>
            <span class="badge bg-danger text-light">🎥 Pakai Webcam: <strong>${row.pakai_webcam == '1' ? 'Ya' : 'Tidak'}</strong></span>
            <span class="badge bg-dark">🔒 Perangkat Terkunci: <strong>${row.perangkat_terkunci == '1' ? 'Ya' : 'Tidak'}</strong></span>
        </div>
    `
                },
                {
                    data: 'is_active',
                    render: d => d == 1 ?
                        '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'
                },
                {
                    data: null,
                    orderable: false,
                    render: row => `
        <div class="d-flex flex-column align-items-center gap-1">
            <button class="btn btn-outline-primary edit-btn w-100" title="Edit" data-id="${row.id}">
                <i data-feather="edit"></i>
            </button>
            <button class="btn btn-outline-danger delete-btn w-100" title="Hapus" data-id="${row.id}">
                <i data-feather="trash-2"></i>
            </button>
            <a href="${base_url}panel/ujian/detail/${row.id}" class="btn btn-outline-secondary w-100" title="Detail">
                <i data-feather="info"></i>
            </a>
        </div>
    `
                }


            ],
            drawCallback: function(settings) {
                feather.replace(); // ← ini yang penting
            },
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
            },
        });
        $('#refreshtable').on('click', function() {
            $('#ujianTable').DataTable().ajax.reload(null, false); // false = tetap di halaman aktif
        });
        $('#ujianTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            $.get('<?= base_url('panel/ujian/list') ?>', function(res) {
                const item = res.data.find(i => i.id == id);
                if (item) {
                    $('[name="id"]').val(item.id);
                    $('[name="bank_soal_id"]').val(item.bank_soal_id);
                    $('[name="nama_ujian"]').val(item.nama_ujian);
                    $('[name="deskripsi"]').val(item.deskripsi);
                    $('[name="kode_ujian"]').val(item.kode_ujian);
                    $('[name="token"]').val(item.token);
                    $('[name="durasi_ujian"]').val(item.durasi_ujian);
                    $('[name="minimal_durasi"]').val(item.minimal_durasi);
                    $('[name="waktu_mulai"]').val(item.waktu_mulai);
                    $('[name="waktu_selesai"]').val(item.waktu_selesai);
                    $('[name="acak_soal"]').prop('checked', item.acak_soal == '1');
                    $('[name="acak_opsi"]').prop('checked', item.acak_opsi == '1');
                    $('[name="pakai_token"]').prop('checked', item.pakai_token == '1');
                    $('[name="tampil_nilai"]').prop('checked', item.tampil_nilai == '1');
                    $('[name="tampil_pembahasan"]').prop('checked', item.tampil_pembahasan == '1');
                    $('[name="pakai_webcam"]').prop('checked', item.pakai_webcam == '1');
                    $('[name="perangkat_terkunci"]').prop('checked', item.perangkat_terkunci == '1');
                    $('[name="single_login"]').prop('checked', item.single_login == '1');
                    $('[name="created_by"]').val(item.created_by);

                    $('.modal-title').text('Edit Ujian');
                    $('#modal_form').modal('show');
                    save_method = 'edit';
                }
            });
        });

        $('#ujianTable').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus ujian ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/ujian/delete/') ?>${id}`, function(res) {
                        $('#ujianTable').DataTable().ajax.reload();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    });
                }
            });
        });

        $('#ujianTable').on('click', '.tambah-peserta-btn', function() {
            const ujianId = $(this).data('id');
            $('#tambahPesertaModal [name="ujian_id"]').val(ujianId);
            $('#tambahPesertaModal').modal('show');
        });
    });

    function generateToken(length = 6) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let token = '';
        for (let i = 0; i < length; i++) {
            token += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return token;
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('[name="token"]').val(generateToken());
        $('.modal-title').text('Tambah Ujian');
        $('#modal_form').modal('show');
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/ujian/create') ?>' :
            '<?= base_url('panel/ujian/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        let formData = $('#form').serializeArray();

        // Daftar nama checkbox yang ingin dipastikan terkirim
        const checkboxNames = [
            'acak_soal',
            'acak_opsi',
            'pakai_token',
            'tampil_nilai',
            'tampil_pembahasan',
            'pakai_webcam',
            'perangkat_terkunci',
            'single_login'
        ];

        checkboxNames.forEach(name => {
            // Hapus dulu jika sudah ada
            formData = formData.filter(f => f.name !== name);

            // Masukkan ulang sesuai status ceklis
            formData.push({
                name: name,
                value: $(`[name="${name}"]`).is(':checked') ? '1' : '0'
            });
        });

        // Debug: lihat data yg dikirim
        console.table(formData);

        $.post(url, $.param(formData), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                $('#ujianTable').DataTable().ajax.reload();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => {
                    errors += `<li>${v}</li>`;
                });
                errors += '</ul></div>';
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }
</script>
<script>
    $(document).ready(function() {
        // Trigger awal dan saat filter berubah
        $('#filter_tingkat, #filter_kelas, #filter_jurusan, #filter_agama').on('change', loadPesertaBelumDitambahkan);

        // Saat modal dibuka, inisialisasi ID ujian dan load peserta
        $('#tambahPesertaModal').on('show.bs.modal', function(e) {
            const ujianId = $(e.relatedTarget).data('id');
            $('[name="ujian_id"]').val(ujianId);
            loadPesertaBelumDitambahkan();
            loadPesertaSudahDitambahkan(ujianId);
        });

        $('#btn-tambah').click(function() {
            $('#list-belum option:selected').each(function() {
                const cloned = $(this).clone().prop('selected', true); // ✅ auto selected
                $('#list-sudah').append(cloned);
                $(this).remove();
            });
            updateCounts(); // ✅ di sini
        });


        $('#btn-hapus').click(function() {
            const selected = $('#list-sudah option:selected');
            const ujianId = $('[name="ujian_id"]').val();

            if (selected.length === 0) return;

            const pesertaIds = [];
            selected.each(function() {
                pesertaIds.push($(this).val());
            });

            // Kirim ke server untuk cek apakah peserta-peserta itu sudah tercatat di hasil_ujian
            $.post('<?= base_url('panel/ujian/cek-peserta-diuji') ?>', {
                ujian_id: ujianId,
                peserta_id: pesertaIds
            }, function(res) {
                if (!res.status) {
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                    return;
                }

                const {
                    sudahAda,
                    belumAda
                } = res.data;

                // Kalau belum ada, langsung pindah
                belumAda.forEach(id => {
                    const opt = $('#list-sudah option[value="' + id + '"]');
                    $('#list-belum').append(opt.clone());
                    opt.remove();
                    updateCounts(); // ✅ Update jumlah
                });

                // Kalau sudah ada, konfirmasi dulu
                if (sudahAda.length > 0) {
                    if (confirm('Beberapa peserta sudah memiliki data hasil ujian. Menghapus mereka akan menghapus seluruh nilai. Lanjutkan?')) {
                        $.post('<?= base_url('panel/ujian/remove-peserta') ?>', {
                            ujian_id: ujianId,
                            peserta_id: sudahAda
                        }, function(resRemove) {
                            if (resRemove.status) {
                                sudahAda.forEach(id => {
                                    const opt = $('#list-sudah option[value="' + id + '"]');
                                    $('#list-belum').append(opt.clone());
                                    opt.remove();
                                    updateCounts(); // ✅ Update jumlah
                                });
                                Snackbar.show({
                                    text: resRemove.message,
                                    pos: 'top-center'
                                });
                            } else {
                                Snackbar.show({
                                    text: resRemove.message,
                                    pos: 'top-center'
                                });
                            }
                        }, 'json');
                    }
                }
            }, 'json');
        });


        // Submit form peserta ujian
        $('#formTambahPeserta').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post('<?= base_url('panel/ujian/add-peserta') ?>', formData, function(res) {
                if (res.status) {
                    $('#tambahPesertaModal').modal('hide');
                    $('#ujianTable').DataTable().ajax.reload();
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                } else {
                    Snackbar.show({
                        text: 'Gagal menyimpan data peserta.',
                        pos: 'top-center'
                    });
                }
            }, 'json');
        });
    });

    function loadPesertaBelumDitambahkan() {
        const ujianId = $('[name="ujian_id"]').val();
        const data = {
            tingkat_id: $('#filter_tingkat').val(),
            kelas_id: $('#filter_kelas').val(),
            jurusan_id: $('#filter_jurusan').val(),
            agama_id: $('#filter_agama').val()
        };

        if (!ujianId) return;

        $.get(`<?= base_url('panel/ujian/filter-peserta') ?>/${ujianId}`, data, function(res) {
            $('#list-belum').empty();
            if (res.status) {
                $.each(res.data, function(i, peserta) {
                    $('#list-belum').append(`<option value="${peserta.id}">${peserta.nama} (${peserta.nisn})</option>`);
                });
            }
            updateCounts(); // ✅ di sini
        }, 'json');
    }


    // Ambil peserta yang sudah ditambahkan ke ujian
    function loadPesertaSudahDitambahkan(ujianId) {
        $.get(`<?= base_url('panel/ujian/peserta-ujian') ?>/${ujianId}`, function(res) {
            $('#list-sudah').empty();
            if (res.status) {
                $.each(res.data, function(i, peserta) {
                    $('#list-sudah').append(
                        `<option value="${peserta.id}" selected>${peserta.nama} (${peserta.nisn})</option>`
                    );
                });
            }
            updateCounts(); // ✅ di sini
        }, 'json');
    }

    function updateCounts() {
        $('#count-belum').text($('#list-belum option').length);
        $('#count-sudah').text($('#list-sudah option').length);
    }
</script>

<?php $this->endSection(); ?>