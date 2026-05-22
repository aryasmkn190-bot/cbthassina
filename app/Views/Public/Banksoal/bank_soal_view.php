<?= $this->extend('Layout/main_public'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h5 class="mb-0">Daftar Bank Soal Publik</h5>
            </div>

            <!-- Search -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <div class="input-group" style="flex:1;">
                    <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                    <input type="text" id="searchBox" class="form-control" placeholder="Cari bank soal...">
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

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    let allData = [];
    let currentPage = 1;
    const perPage = 5;

    $(document).ready(function() {
        loadData();

        $('#searchBox').on('input', function() {
            currentPage = 1;
            renderList();
        });
    });

    function loadData() {
        $.ajax({
            url: '<?= base_url('public/banksoal/list') ?>',
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

        const filtered = allData.filter(item =>
            item.nama.toLowerCase().includes(search) || (item.deskripsi || '').toLowerCase().includes(search)
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
    <div class="me-3 text-center rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
         style="width:36px;height:36px;flex:0 0 36px;">#${urutan}</div>

    <div class="flex-grow-1">
      <!-- Header atas -->
      <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="small">
          <span class="badge ${row.is_active == 1 ? 'bg-success' : 'bg-danger'}">
            ${row.is_active == 1 ? 'Aktif' : 'Nonaktif'}
          </span>
          <span class="badge bg-secondary">#${row.kode}</span>
          <span class="badge bg-info"><i data-feather="globe"></i> Public</span>
        </div>
      </div>

      <!-- Judul & Deskripsi -->
      <a href="<?= base_url('public/banksoal/detail') ?>/${row.id}" class="fw-semibold text-dark text-decoration-none">
        ${row.nama}
      </a>
      <div class="text-muted small mb-2">${row.deskripsi ?? '-'}</div>

      <!-- Info jumlah -->
      <div class="d-flex flex-wrap gap-3 small text-muted mb-2">
        <span><i class="bi bi-folder text-warning"></i> ${row.jumlah_topik} Topik</span>
        <span><i class="bi bi-list-check text-info"></i> ${row.jumlah_total_soal} Soal</span>
      </div>

      <!-- Badge tipe soal -->
      <div class="d-flex flex-wrap small">
        ${badge('PG', row.jumlah_pg, 'primary')}
        ${badge('MPG', row.jumlah_mpg, 'success')}
        ${badge('BS', row.jumlah_bs, 'warning')}
        ${badge('Jodohkan', row.jumlah_jodohkan, 'info')}
        ${badge('Isian', row.jumlah_isian, 'secondary')}
        ${badge('Esai', row.jumlah_esai, 'dark')}
      </div>
    </div>
  </div>
</div>
`;
            });
        }

        $('#bankSoalList').html(html);
        renderPagination(totalPages);
        feather.replace();
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
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i===currentPage?'active':''}"><button class="page-link" onclick="gotoPage(${i})">${i}</button></li>`;
        }
        $('#pagination').html(html);
    }

    function gotoPage(page) {
        currentPage = page;
        renderList();
    }
</script>
<?= $this->endSection(); ?>