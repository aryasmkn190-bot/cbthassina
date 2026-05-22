<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>
<style>
    /* Semua icon di dalam .action-icons */
    .action-icons svg {
        transition: transform 0.2s ease, filter 0.2s ease;
        cursor: pointer;
    }

    /* Hover: sedikit membesar + warna lebih gelap */
    .action-icons a:hover svg,
    .action-icons button:hover svg {
        transform: scale(1.2);
        filter: brightness(0.8);
    }

    /* Saat ditekan (klik) → efek mengecil biar ada feedback */
    .action-icons a:active svg,
    .action-icons button:active svg {
        transform: scale(0.9);
    }
</style>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h5 class="mb-0">Manajemen Bank Soal</h5>
                <div class="d-flex gap-2">
                    <button type="button" onclick="add()" class="btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah
                    </button>
                    <button type="button" id="refreshList" class="btn btn-outline-secondary">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>


            <!-- Filter & Search -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <select id="filterVisibility" class="form-select form-select-sm" style="width:auto;">
                    <option value="">Semua</option>
                    <option value="1">Public</option>
                    <option value="0">Private</option>
                </select>

                <div class="input-group" style="flex:1;">
                    <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari bank soal...">
                </div>
            </div>

            <!-- List -->
            <div id="bankSoalList" class="d-flex flex-column gap-3"></div>

            <!-- Pagination -->
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Form Bank Soal -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <form id="form" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Bank Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessages"></div>
                <input type="hidden" name="id">
                <div class="form-group mb-3">
                    <label>Kode (ex : STS_GANJIL_XI_BINDO)</label>
                    <input type="text" name="kode" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control form-control-sm"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Pembuat (Guru)</label>
                    <select name="created_by" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select name="is_active" class="form-control form-control-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Visibilitas</label>
                    <select name="is_public" class="form-control form-control-sm" required>
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="save()" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Form Topik -->
<div class="modal fade" id="modal_topik" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_topik" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Form Topik Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessagesTopik"></div>
                <input type="hidden" name="id">
                <input type="hidden" name="bank_soal_id">
                <div class="form-group mb-3">
                    <label>Nama Topik</label>
                    <input type="text" name="nama" class=" form-control form-control-sm-sm" required>
                </div>
                <div class="form-group mb-3">
                    <label>Keterangan (Opsional)</label>
                    <textarea name="keterangan" class=" form-control form-control-sm-sm"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveTopik()" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Share Bank Soal -->
<div class="modal fade" id="modal_share" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Bank Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Visibilitas</label>
                    <select id="shareVisibility" class="form-select">
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Link Share</label>
                    <div class="input-group">
                        <input type="text" id="shareLink" class="form-control form-control-sm" readonly>
                        <button class="btn btn-outline-primary" id="copyShareLink">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let save_method;
    let allData = [];
    let currentPage = 1;
    const perPage = 5;

    $(document).ready(function() {
        loadData();

        $('#refreshList').on('click', function() {
            loadData();
        });

        $('#searchBox, #filterVisibility').on('input change', function() {
            currentPage = 1;
            renderList();
        });

        // Delegasi tombol edit/hapus
        $(document).on('click', '.btn-edit', function() {
            edit($(this).data('id'));
        });
        $(document).on('click', '.btn-duplicate', function() {
            duplicate($(this).data('id'));
        });
        $(document).on('click', '.btn-hapus', function() {
            hapus($(this).data('id'));
        });

        let autoFill = true;

        $('input[name="kode"]').on('input', function() {
            if (autoFill) {
                $('input[name="nama"]').val($(this).val());
            }
        });

        // $('input[name="nama"]').on('input', function() {
        //     // kalau user edit manual nama → hentikan autofill
        //     if ($(this).val().trim() !== $('input[name="kode"]').val().trim()) {
        //         autoFill = false;
        //     } else {
        //         autoFill = true;
        //     }
        // });
    });

    function loadData() {
        $.ajax({
            url: '<?= base_url('panel/banksoal/list') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                allData = res.data || [];
                currentPage = 1;
                renderList();
            },
            error: function(err) {
                console.error("Gagal load data:", err);
            }
        });
    }

    function renderList() {
        const search = $('#searchBox').val().toLowerCase();
        const visibility = $('#filterVisibility').val();

        const filtered = allData.filter(item =>
            (!visibility || item.is_public == visibility) &&
            (item.nama.toLowerCase().includes(search) || (item.deskripsi || '').toLowerCase().includes(search))
        );

        const totalPages = Math.ceil(filtered.length / perPage);
        const start = (currentPage - 1) * perPage;
        const paginated = filtered.slice(start, start + perPage);

        let html = '';
        if (!paginated.length) {
            html = `<div class="alert alert-light text-center">Tidak ada data</div>`;
        } else {
            paginated.forEach((row, idx) => {
                const urutan = start + idx + 1;
                const badge = (label, count, color) =>
                    count > 0 ? `<span class="badge bg-${color} me-1 mb-1">${label} ${count}</span>` : '';

                html += `
<div class="card mb-3 border-0 shadow-sm banksoal-card" style="transition:all .2s ease;">
  <div class="card-body d-flex flex-wrap align-items-start p-3">

    <!-- Nomor urut -->
   

    <div class="flex-grow-1">
     <div class="fw-semibold text-dark mb-1">${urutan}. ${row.nama}</div>
      <!-- Header atas -->
      <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="small d-flex flex-wrap gap-3 align-items-center">
          <span class="${row.is_active == 1 ? 'text-success fw-semibold' : 'text-danger fw-semibold'}">
            ${row.is_active == 1 ? 'Aktif' : 'Nonaktif'}
          </span>
          <span class="text-muted">#${row.kode}</span>
          <span class="${row.is_public == 1 ? 'text-info' : 'text-warning'} d-flex align-items-center gap-1">
            ${row.is_public == 1 
              ? '<i data-feather="globe" style="width:14px; height:14px;"></i> Public' 
              : '<i data-feather="lock" class="icon-sm" style="width:14px; height:14px;"></i> Private'}
          </span>
        </div>

        <!-- Aksi (desktop & tablet) -->
        <div class="d-none d-sm-flex gap-3 action-icons">
          <a href="<?= base_url() ?>panel/banksoal/soal/${row.id}" class="text-secondary" title="Tambah Soal">
            <i data-feather="plus-circle"></i>
          </a>
          <button class="btn-edit text-primary border-0 bg-transparent p-0" data-id="${row.id}" title="Edit">
            <i data-feather="edit"></i>
          </button>
          <button class="btn-duplicate text-warning border-0 bg-transparent p-0" data-id="${row.id}" title="Duplikat Bank Soal">
            <i data-feather="copy"></i>
          </button>
          <button class="btn-hapus text-danger border-0 bg-transparent p-0" data-id="${row.id}" title="Hapus">
            <i data-feather="trash-2"></i>
          </button>
          <button class="btn-share text-success border-0 bg-transparent p-0" data-id="${row.id}" title="Share">
            <i data-feather="share-2"></i>
          </button>
        </div>
      </div>

      <!-- Judul & Deskripsi -->
     
      <div class="text-muted small mb-3">${row.deskripsi ?? '-'}</div>

      <!-- Info jumlah -->
      <div class="d-flex flex-column flex-sm-row justify-content-between mb-1 small">
  <!-- Info jumlah -->
 <div class="d-flex flex-wrap gap-3 text-muted">
  <span><i data-feather="folder" class="me-1 text-warning" style="width:14px; height:14px;"></i> ${row.jumlah_topik} Topik</span>
  <span><i data-feather="list" class="me-1 text-info" style="width:14px; height:14px;"></i> ${row.jumlah_total_soal} Soal</span>
  <span><i data-feather="award" class="me-1 text-success" style="width:14px; height:14px;"></i> ${row.total_bobot} Poin</span>
</div>


  <!-- Tipe soal -->
  <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
    ${row.jumlah_pg > 0 ? `<span class="text-primary">PG (${row.jumlah_pg})</span>` : ''}
    ${row.jumlah_mpg > 0 ? `<span class="text-success">MPG (${row.jumlah_mpg})</span>` : ''}
    ${row.jumlah_bs > 0 ? `<span class="text-warning">BS (${row.jumlah_bs})</span>` : ''}
    ${row.jumlah_jodohkan > 0 ? `<span class="text-info">Jodohkan (${row.jumlah_jodohkan})</span>` : ''}
    ${row.jumlah_isian > 0 ? `<span class="text-secondary">Isian (${row.jumlah_isian})</span>` : ''}
    ${row.jumlah_esai > 0 ? `<span class="text-dark">Esai (${row.jumlah_esai})</span>` : ''}
  </div>
</div>


      <!-- Daftar Topik -->
      <div class="mb-2 d-flex flex-wrap align-items-center gap-2 small">
        ${(row.daftar_topik || []).map(n => `<span class="px-2 py-1 border rounded text-muted">${n}</span>`).join('')}
        <button class="btn btn-sm btn-outline-primary toggle-topik" style="flex-shrink: 0;" data-id="${row.id}">+ Topik</button>
      </div>
      <div class="topik-container mt-2" id="topik-${row.id}" style="display:none;"></div>

      <!-- Aksi (khusus HP) -->
      <div class="d-flex d-sm-none gap-3 mt-2 action-icons">
        <a href="<?= base_url() ?>panel/banksoal/soal/${row.id}" class="text-secondary" title="Tambah Soal">
          <i data-feather="plus-circle"></i>
        </a>
        <button class="btn-edit text-primary border-0 bg-transparent p-0" data-id="${row.id}" title="Edit">
          <i data-feather="edit"></i>
        </button>
        <button class="btn-duplicate text-warning border-0 bg-transparent p-0" data-id="${row.id}" title="Duplikat Bank Soal">
          <i data-feather="copy"></i>
        </button>
        <button class="btn-hapus text-danger border-0 bg-transparent p-0" data-id="${row.id}" title="Hapus">
          <i data-feather="trash-2"></i>
        </button>
        <button class="btn-share text-success border-0 bg-transparent p-0" data-id="${row.id}" title="Share">
          <i data-feather="share-2"></i>
        </button>
      </div>
    </div>
  </div>
</div>
`;

            });
        }

        $('#bankSoalList').html(html);
        renderPagination(totalPages);
        feather.replace(); // <-- ini penting
        $('.banksoal-card').hover(
            function() {
                $(this).css({
                    'transform': 'translateY(-3px)',
                    'box-shadow': '0 6px 18px rgba(0,0,0,0.1)'
                });
            },
            function() {
                $(this).css({
                    'transform': 'none',
                    'box-shadow': '0 2px 6px rgba(0,0,0,0.05)'
                });
            }
        );
    }

    function renderPagination(totalPages) {
        let html = '';
        const maxVisible = 5; // jumlah halaman maksimum (tidak termasuk ellipsis)
        const current = currentPage;

        // tombol Prev (feather icon left)
        html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="gotoPage(${current - 1})" ${current === 1 ? 'disabled' : ''}>
                    <i data-feather="chevron-left"></i>
                </button>
            </li>`;

        if (totalPages <= maxVisible) {
            // kalau total halaman sedikit, tampilkan semua
            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                        <button class="page-link" onclick="gotoPage(${i})">${i}</button>
                    </li>`;
            }
        } else {
            // halaman pertama
            html += `<li class="page-item ${1 === current ? 'active' : ''}">
                    <button class="page-link" onclick="gotoPage(1)">1</button>
                </li>`;

            // ellipsis kiri
            if (current > 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }

            // halaman sekitar current
            let start = Math.max(2, current - 1);
            let end = Math.min(totalPages - 1, current + 1);

            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                        <button class="page-link" onclick="gotoPage(${i})">${i}</button>
                    </li>`;
            }

            // ellipsis kanan
            if (current < totalPages - 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }

            // halaman terakhir
            html += `<li class="page-item ${totalPages === current ? 'active' : ''}">
                    <button class="page-link" onclick="gotoPage(${totalPages})">${totalPages}</button>
                </li>`;
        }

        // tombol Next (feather icon right)
        html += `<li class="page-item ${current === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="gotoPage(${current + 1})" ${current === totalPages ? 'disabled' : ''}>
                    <i data-feather="chevron-right"></i>
                </button>
            </li>`;

        $('#pagination').html(html);

        // render feather icons
        if (window.feather) {
            feather.replace();
        }
    }



    function gotoPage(page) {
        currentPage = page;
        renderList();
    }
    // SHARE
    $(document).on('click', '.btn-share', function() {
        const id = $(this).data('id');
        const item = allData.find(i => i.id == id);
        if (!item) return;

        // Set visibilitas di modal
        $('#shareVisibility').val(item.is_public);

        // Buat link share (bisa pakai token unik jika mau aman)
        const link = `<?= base_url() ?>panel/banksoal/public/${id}`;
        $('#shareLink').val(link);

        // Simpan ID bank soal sementara
        $('#modal_share').data('bankid', id);

        $('#modal_share').modal('show');
    });

    // Copy link
    $('#copyShareLink').on('click', function() {
        const link = $('#shareLink').val();
        navigator.clipboard.writeText(link).then(() => {
            Snackbar.show({
                text: 'Link berhasil disalin!',
                pos: 'top-center'
            });
        });
    });

    // Update visibilitas langsung dari modal
    $('#shareVisibility').on('change', function() {
        const id = $('#modal_share').data('bankid');
        const val = $(this).val();
        $.post(`<?= base_url('panel/banksoal/update_visibility/') ?>${id}`, {
            is_public: val
        }, function(res) {
            if (res.status) {
                // Update local data
                const item = allData.find(i => i.id == id);
                if (item) item.is_public = parseInt(val);
                renderList();
                Snackbar.show({
                    text: 'Visibilitas berhasil diperbarui!',
                    pos: 'top-center'
                });
            }
        }, 'json');
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.modal-title').text('Tambah Bank Soal');
        $('#modal_form').modal('show');
    }

    function edit(id) {
        const item = allData.find(i => i.id == id);
        if (item) {
            $('[name="id"]').val(item.id);
            $('[name="kode"]').val(item.kode);
            $('[name="nama"]').val(item.nama);
            $('[name="deskripsi"]').val(item.deskripsi);
            $('[name="is_active"]').val(item.is_active);
            $('[name="created_by"]').val(item.created_by);
            $('[name="is_public"]').val(item.is_public);
            $('.modal-title').text('Edit Bank Soal');
            save_method = 'edit';
            $('#modal_form').modal('show');
        }
    }

    function save() {
        const url = save_method === 'add' ?
            '<?= base_url('panel/banksoal/create') ?>' :
            '<?= base_url('panel/banksoal/update') ?>/' + $('[name="id"]').val();

        if ($('#form')[0].checkValidity() === false) {
            $('#form').addClass('was-validated');
            return;
        }

        $.post(url, $('#form').serialize(), function(res) {
            if (res.status) {
                $('#modal_form').modal('hide');
                loadData();
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                errors += '</ul></div>';
                $('#errorMessages').html(errors);
            }
        }, 'json');
    }

    function hapus(id) {
        Swal.fire({
                title: 'Yakin hapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            })
            .then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/banksoal/delete/') ?>${id}`, function(res) {
                        loadData();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    }, 'json');
                }
            });
    }

    function duplicate(id) {
        Swal.fire({
            title: 'Duplikat Bank Soal?',
            text: 'Ini akan menyalin bank soal beserta semua topik, soal, dan opsi soal di dalamnya.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Duplikat',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menduplikasi bank soal.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post(`<?= base_url('panel/banksoal/duplicate/') ?>${id}`, function(res) {
                    Swal.close();
                    if (res.status) {
                        loadData();
                        Snackbar.show({
                            text: res.message,
                            pos: 'top-center'
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: res.message,
                            icon: 'error'
                        });
                    }
                }, 'json').fail(function() {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server.',
                        icon: 'error'
                    });
                });
            }
        });
    }

    function renderTopik(bankId) {
        const container = $(`#topik-${bankId}`);
        $.get(`<?= base_url('panel/banksoal/topik/list/') ?>${bankId}`, function(res) {
            let html = `
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-sm btn-outline-success add-topik-btn" data-bankid="${bankId}">
                    + Topik Baru
                </button>
            </div>
        `;

            if (res.data && res.data.length > 0) {
                html += '<ul class="list-group">';
                res.data.forEach(topik => {
                    html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${topik.nama} ${topik.keterangan ? '- ' + topik.keterangan : ''}</span>
                    <div>
                        <button class="btn btn-sm btn-outline-warning edit-topik" data-bank="${bankId}" data-id="${topik.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-danger delete-topik" data-bank="${bankId}" data-id="${topik.id}">Hapus</button>
                    </div>
                </li>`;
                });
                html += '</ul>';
            } else {
                html += '<div class="text-muted small">Belum ada topik</div>';
            }

            container.html(html);
            container.data('loaded', true);
            container.show(); // pastikan container tetap terlihat
        });
    }

    // Toggle daftar topik per Bank Soal
    $(document).on('click', '.toggle-topik', function() {
        const bankSoalId = $(this).data('id');
        const container = $(`#topik-${bankSoalId}`);

        if (container.is(':visible')) {
            container.slideUp();
        } else {
            if (!container.data('loaded')) {
                $.get(`<?= base_url('panel/banksoal/topik/list/') ?>${bankSoalId}`, function(res) {
                    let html = '';

                    // Tambahkan tombol add-topik di kanan atas
                    html += `
                        <div class="d-flex justify-content-end mb-2">
                          <button class="btn btn-sm btn-outline-success add-topik-btn" data-bankid="${bankSoalId}">
                            + Topik Baru
                        </button>

                        </div>
                    `;


                    if (res.data && res.data.length > 0) {
                        html += '<ul class="list-group">';
                        res.data.forEach(topik => {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${topik.nama} ${topik.keterangan ? '- ' + topik.keterangan : ''}</span>
                                <div>
                                    <button class="btn btn-sm btn-outline-warning edit-topik" data-bank="${bankSoalId}" data-id="${topik.id}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger delete-topik" data-id="${topik.id}">Hapus</button>
                                </div>
                            </li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<div class="text-muted small">Belum ada topik</div>';
                    }

                    container.html(html);
                    container.data('loaded', true);
                });
            }
            container.slideDown();
        }
    });



    let save_method_topik;
    $(document).on('click', '.add-topik-btn', function() {
        const bankId = $(this).data('bankid');
        addTopik(bankId);
    });

    function addTopik(bankId) {
        save_method_topik = 'add';
        $('#form_topik')[0].reset();
        $('[name="bank_soal_id"]').val(bankId);
        $('.modal-title').text('Tambah Topik');
        $('#modal_topik').modal('show');
    }

    function saveTopik() {
        const url = save_method_topik === 'add' ?
            '<?= base_url('panel/banksoal/topik/create') ?>' :
            '<?= base_url('panel/banksoal/topik/update') ?>/' + $('[name="id"]').val();

        if ($('#form_topik')[0].checkValidity() === false) {
            $('#form_topik').addClass('was-validated');
            return;
        }

        $.post(url, $('#form_topik').serialize(), function(res) {
            if (res.status) {
                $('#modal_topik').modal('hide');
                // Reload topik list card
                const bankId = $('[name="bank_soal_id"]').val();
                renderTopik(bankId);
                Snackbar.show({
                    text: res.message,
                    pos: 'top-center'
                });
            } else {
                let errors = '<div class="alert alert-danger"><ul>';
                $.each(res.message, (k, v) => errors += `<li>${v}</li>`);
                errors += '</ul></div>';
                $('#errorMessagesTopik').html(errors);
            }
        }, 'json');
    }

    $(document).on('click', '.edit-topik', function() {
        const id = $(this).data('id');
        const bankId = $(this).data('bank');

        $.get(`<?= base_url('panel/banksoal/topik/list/') ?>${bankId}`, function(res) {
            const item = res.data.find(i => i.id == id);
            if (item) {
                $('[name="id"]').val(item.id);
                $('[name="nama"]').val(item.nama);
                $('[name="keterangan"]').val(item.keterangan);
                $('[name="bank_soal_id"]').val(bankId);
                $('.modal-title').text('Edit Topik');
                save_method_topik = 'edit';
                $('#modal_topik').modal('show');
            }
        });
    });

    // Delete Topik
    $(document).on('click', '.delete-topik', function() {
        const id = $(this).data('id');
        const bankId = $(this).data('bank');

        Swal.fire({
            title: 'Yakin hapus topik ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/banksoal/topik/delete/') ?>${id}`, function(res) {
                    Snackbar.show({
                        text: res.message,
                        pos: 'top-center'
                    });
                    // Reload topik container langsung tanpa harus toggle lagi
                    renderTopik(bankId); // refresh container saja
                }, 'json');
            }
        });
    });
</script>

<?= $this->endSection(); ?>