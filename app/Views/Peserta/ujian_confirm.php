<?= $this->extend('Layout/main_peserta') ?>
<?= $this->section('content') ?>

<style>
    body {
        background: #f3f4f6;
        font-family: 'Inter', sans-serif;
    }

    .confirm-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        padding: 1rem;
    }

    .confirm-card {

        border-radius: 20px;
        max-width: 480px;
        width: 100%;
        padding: 2.5rem 2rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        animation: fadeInUp 0.6s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(25px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .confirm-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .confirm-header h3 {
        font-weight: 700;
        font-size: 1.6rem;
        color: #111827;
    }

    .confirm-header p {
        font-size: 0.95rem;
        color: #6b7280;
        margin: 0;
    }

    .confirm-body {
        text-align: center;
    }

    .confirm-body p {
        font-size: 1rem;
        margin-bottom: 0.7rem;
        color: #374151;
    }

    .confirm-body b {
        color: #111827;
    }

    .form-check {
        margin: 1.5rem 0;
        text-align: left;
    }

    .form-check-label {
        font-size: 0.95rem;
        color: #374151;
        cursor: pointer;
    }

    /* Buttons */
    .btn-group {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-top: 1.5rem;
    }

    .btn-action {
        border-radius: 12px;
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .btn-action:disabled,
    .btn-action.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        border: none;
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(78, 115, 223, 0.25);
    }

    .btn-outline-secondary {
        border: 2px solid #d1d5db;
        color: #374151;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
    }

    /* Status card if already taken */
    .status-card {

        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        color: #92400e;
        font-size: 0.95rem;
    }
</style>

<div class="container confirm-wrapper">
    <div class="confirm-card">
        <div class="confirm-header">
            <h3>Konfirmasi Ujian</h3>
            <p>Pastikan data berikut sudah benar</p>
        </div>

        <div class="confirm-body">
            <p>Nama Ujian: <b><?= esc($ujian['nama_ujian']) ?></b></p>
            <p>Durasi: <b><?= esc($ujian['durasi_ujian']) ?> menit</b></p>

            <?php if ($peserta): ?>
                <p>Peserta: <b><?= esc($peserta['nama']) ?></b></p>
            <?php else: ?>
                <p><span class="badge bg-warning">Mode Guest</span></p>
            <?php endif; ?>

            <?php if (!empty($hasil)): ?>
                <div class="status-card">
                    Anda sudah menyelesaikan ujian ini.<br>
                    <b>Skor:</b> <?= esc($hasil['nilai_total'] ?? '-') ?> |
                    <b>Status:</b> <?= esc($hasil['status'] ?? '-') ?>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('share/ujian/mulai/' . $ujian['id'] . '?token=' . $startToken) ?>"
                        class="btn btn-primary btn-action">
                        Ulangi Ujian
                    </a>
                    <a href="<?= base_url('peserta/home') ?>" class="btn btn-outline-secondary btn-action">
                        Kembali
                    </a>
                </div>
            <?php else: ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeCheck" onchange="toggleButton()">
                    <label class="form-check-label" for="agreeCheck">
                        Saya sudah siap mengerjakan ujian ini
                    </label>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('peserta/home') ?>" class="btn btn-outline-secondary btn-action">
                        Batal
                    </a>
                    <a href="<?= base_url('share/ujian/mulai/' . $ujian['id'] . '?token=' . $startToken) ?>"
                        id="btnMulai" class="btn btn-primary btn-action disabled">
                        Mulai Ujian
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function toggleButton() {
        const check = document.getElementById('agreeCheck');
        const btn = document.getElementById('btnMulai');
        btn.classList.toggle('disabled', !check.checked);
    }
</script>

<?= $this->endSection() ?>