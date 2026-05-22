<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Inter", sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem 0;
            font-size: .95rem;
        }

        .status-item i {
            font-size: 1.2rem;
        }

        .btn-lg {
            border-radius: .75rem;
            font-weight: 600;
            padding: .75rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card shadow-lg p-4 text-center">
            <?php if ($ujian): ?>
                <h3 class="fw-bold mb-2"><?= esc($ujian['nama_ujian']) ?></h3>
                <p class="text-muted">Kode Ujian:
                    <span class="fw-semibold text-primary"><?= esc($ujian['token']) ?></span>
                </p>

                <div class="text-start mb-4">
                    <div class="status-item">
                        <?php if ($validasi['kode']): ?>
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Kode ujian valid</span>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill text-danger"></i>
                            <span>Kode ujian tidak valid</span>
                        <?php endif; ?>
                    </div>
                    <div class="status-item">
                        <?php if ($validasi['dibagikan']): ?>
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Ujian sudah dibagikan</span>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill text-danger"></i>
                            <span>Ujian belum dibagikan</span>
                        <?php endif; ?>
                    </div>
                    <div class="status-item">
                        <?php if ($ujian['butuh_login']): ?>
                            <?php if ($validasi['butuhLogin']): ?>
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Login terpenuhi</span>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger"></i>
                                <span>Harus login dulu</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Tidak perlu login</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($validasi['kode'] && $validasi['dibagikan'] && $validasi['butuhLogin']): ?>
                    <a href="<?= base_url('ujian/mulai/' . $ujian['id']) ?>"
                        class="btn btn-primary btn-lg w-100 shadow-sm">
                        <i class="bi bi-play-circle me-2"></i> Mulai Ujian
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="bi bi-lock-fill me-2"></i> Tidak dapat memulai ujian
                    </button>
                <?php endif; ?>

            <?php else: ?>
                <h5 class="text-danger">
                    <i class="bi bi-x-circle me-2"></i> Ujian tidak ditemukan
                </h5>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>