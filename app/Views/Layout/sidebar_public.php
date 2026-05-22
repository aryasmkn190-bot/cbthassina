<!--  BEGIN SIDEBAR FILTER  -->
<nav id="sidebar" class="sidebar-public">
    <div class="p-3 border-bottom">
        <h6 class="fw-bold mb-0">Filter Bank Soal</h6>
    </div>

    <div class="p-3">
        <!-- Tingkat -->
        <div class="mb-3">
            <label class="form-label small text-muted">Tingkat</label>
            <select id="filterTingkat" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="sd">SD</option>
                <option value="smp">SMP</option>
                <option value="sma">SMA</option>
                <option value="smk">SMK</option>
            </select>
        </div>

        <!-- Topik -->
        <div class="mb-3">
            <label class="form-label small text-muted">Topik</label>
            <select id="filterTopik" class="form-select form-select-sm">
                <option value="">Semua</option>
                <?php if (!empty($topikList)): ?>
                    <?php foreach ($topikList as $t): ?>
                        <option value="<?= esc($t['id']) ?>"><?= esc($t['nama']) ?></option>
                    <?php endforeach ?>
                <?php endif ?>
            </select>
        </div>

        <!-- Jenis Soal -->
        <div class="mb-3">
            <label class="form-label small text-muted">Jenis Soal</label>
            <div class="d-flex flex-column gap-1 small">
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="pg"> Pilihan Ganda</div>
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="mpg"> Multi PG</div>
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="bs"> Benar/Salah</div>
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="jodohkan"> Menjodohkan</div>
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="isian"> Isian</div>
                <div><input type="checkbox" class="form-check-input me-1 filterJenis" value="esai"> Esai</div>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label small text-muted">Status</label>
            <select id="filterStatus" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        <!-- Tombol Reset -->
        <div class="d-grid mt-4">
            <button id="resetFilter" class="btn btn-light btn-sm border">
                <i data-feather="x-circle"></i> Reset Filter
            </button>
        </div>
    </div>
</nav>
<!--  END SIDEBAR FILTER  -->