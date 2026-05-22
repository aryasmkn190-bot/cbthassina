<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | <?= esc($setting->appname) ?></title>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon">

    <!-- Fonts -->
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            /* range weight yang tersedia */
            font-style: normal;
        }

        :root {
            --primary: #6366f1;
            --bg: #f9fafb;
            --text: #111827;
            --border: #e5e7eb;
            --muted: #6b7280;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            transition: background .3s, color .3s;
        }


        /* Navbar */
        .navbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            z-index: 1050;
        }


        /* Navbar dark mode text fix */
        body.dark .navbar {
            background: #243447;
        }

        body.dark .navbar span,
        body.dark .navbar .nav-link {
            color: #f5f7fa !important;
            /* teks putih terang */
        }

        body.dark .navbar .nav-link:hover {
            color: #4dabf7 !important;
            /* aksen hover biru */
        }

        .navbar .nav-link {
            color: var(--text);
            font-size: 14px;
        }

        /* Sidebar (desktop) */
        .sidebar-public {
            width: 260px;
            min-height: 100vh;
            background: #fff;
            padding: 20px;
            transition: transform .35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow .3s;
            will-change: transform;

            /* ✅ rounded sisi kanan */
            box-shadow: 4px 0 16px rgba(0, 0, 0, 0.05);
            /* ✅ shadow halus */
        }

        /* 🌙 Dark Mode Modern */
        body.dark {
            background-color: #121821;
            /* abu gelap */
            color: #e0e6ed;
        }

        /* Navbar */
        body.dark .navbar {
            background: #243447;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Sidebar */
        body.dark .sidebar-public {
            background: #1e2a38;
            color: #e0e6ed;
            border-radius: 0 14px 14px 0;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.5);
        }

        /* Label & Heading */
        body.dark .sidebar-public .form-label {
            color: #b0bec5;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Input & Select */
        body.dark .form-control,
        body.dark .form-select {
            background-color: #2e3b4e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e0e6ed;
            border-radius: 8px;
        }

        body.dark .form-control:focus,
        body.dark .form-select:focus {
            border-color: #4dabf7;
            box-shadow: 0 0 0 0.2rem rgba(77, 171, 247, .25);
        }

        /* Checkbox */
        body.dark .form-check-input {
            background-color: #2e3b4e;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        body.dark .form-check-input:checked {
            background-color: #4dabf7;
            border-color: #4dabf7;
        }

        /* Button */
        body.dark .btn-outline-secondary {
            color: #e0e6ed;
            border-color: rgba(255, 255, 255, 0.2);
        }

        body.dark .btn-outline-secondary:hover {
            background-color: #4dabf7;
            border-color: #4dabf7;
            color: #fff;
        }



        /* Sidebar mobile (offcanvas) */
        @media (max-width: 991px) {
            .sidebar-public {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                transform: translateX(-100%);
                z-index: 1040;
                box-shadow: 6px 0 20px rgba(0, 0, 0, 0.1);

                /* ✅ lebih round di HP */
            }

            .sidebar-public.active {
                transform: translateX(0);
                animation: slideIn .35s ease forwards;
            }

            .sidebar-public.closing {
                animation: slideOut .35s ease forwards;
            }

            #overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1030;
                opacity: 0;
                transition: opacity .3s ease;
            }

            #overlay.show {
                display: block;
                opacity: 1;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(-100%);
                }

                to {
                    transform: translateX(0);
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(-100%);
                }
            }
        }

        /* Hover & interactive effect */
        .sidebar-public .form-select,
        .sidebar-public .form-check-input {
            transition: box-shadow .2s, transform .2s;
        }

        .sidebar-public .form-select:hover,
        .sidebar-public .form-check-input:hover {
            box-shadow: 0 0 0 2px var(--primary, #6366f1, .3);
            transform: scale(1.02);
        }

        .sidebar-public h6 {
            font-size: 14px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 12px;
            transition: color .3s;
        }

        .sidebar-public h6:hover {
            color: var(--primary);
        }

        /* Navbar modern minimalis */
        .navbar-animated {
            background: #ffffff;
            color: #333;
            transition: all 0.3s ease-in-out;
            border-bottom: 1px solid #eee;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Link navbar */
        .navbar-animated .nav-link {
            color: #444;
            font-weight: 500;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .navbar-animated .nav-link:hover {
            color: #0d6efd;
            /* biru bootstrap */
            transform: translateY(-1px);
        }

        /* Tombol toggle sidebar */
        .sidebar-toggle {
            border: none;
            background: #f8f9fa;
            color: #333;
            border-radius: 8px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .sidebar-toggle:hover {
            background: #e9ecef;
            transform: scale(1.05);
        }





        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .form-select,
        .form-check-input {
            border-radius: 8px;
            font-size: 14px;
        }

        .form-check-label {
            font-size: 13px;
        }

        /* Content */
        #content {
            flex: 1;
            padding: 32px;
            transition: margin-left .3s;
        }

        @media (max-width: 991px) {
            #content {
                padding: 20px;
            }
        }

        /* Footer */
        .footer-wrapper {
            margin-top: 40px;
            border-top: 1px solid var(--border);
            padding: 16px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }

        /* Toggle button */
        .sidebar-toggle {
            cursor: pointer;
            font-size: 20px;
            border: none;
            background: none;
            color: var(--text);
        }

        /* Sidebar */
        #sidebar {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        #sidebar.closed {
            transform: translateX(-100%);
            /* sembunyi */
            box-shadow: none;
        }

        #sidebar.open {
            transform: translateX(0);
            /* muncul */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
    </style>

    <link href="<?= base_url("src/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet">
    <?= $this->renderSection("css") ?>
</head>

<body>
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand px-3 d-flex justify-content-between align-items-center navbar-animated">
        <div class="d-flex align-items-center gap-2">
            <!-- Sidebar toggle -->
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i data-feather="menu"></i>
            </button>
            <!-- Brand -->
            <span class="fw-semibold">
                Candy CBT <span class="d-none d-md-inline">| <?= esc($setting->nama_sekolah) ?></span>
            </span>
        </div>
        <ul class="navbar-nav flex-row align-items-center gap-2">
            <li class="nav-item">
                <a href="<?= base_url('auth/login') ?>" class="nav-link px-2" title="Login">
                    <i data-feather="log-in"></i>
                    <span class="d-none d-md-inline">Login</span>
                </a>
            </li>
            <li class="nav-item">
                <button class="btn btn-light rounded-circle theme-toggle p-2" title="Toggle theme">
                    <i data-feather="moon"></i>
                </button>
            </li>
        </ul>
    </nav>


    <div id="overlay"></div>
    <div class="d-flex">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar-public">
            <h6 class="mb-4 text-primary fw-bold d-flex align-items-center">
                <i data-feather="sliders" class="me-2"></i> Filter
            </h6>

            <!-- Tingkat -->
            <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                    <i data-feather="layers" class="me-2 text-muted" style="width:16px;height:16px;"></i> Tingkat
                </label>
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
                <label class="form-label d-flex align-items-center">
                    <i data-feather="book" class="me-2 text-muted" style="width:16px;height:16px;"></i> Topik
                </label>
                <select id="filterTopik" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($topikList ?? [] as $t): ?>
                        <option value="<?= esc($t['id']) ?>"><?= esc($t['nama']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <!-- Jenis Soal -->
            <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                    <i data-feather="list" class="me-2 text-muted" style="width:16px;height:16px;"></i> Jenis Soal
                </label>
                <div class="d-flex flex-column gap-2 ps-1">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="pg" id="jenis-pg">
                        <label for="jenis-pg" class="form-check-label"><i data-feather="check-square" class="me-1"></i> Pilihan Ganda</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="mpg" id="jenis-mpg">
                        <label for="jenis-mpg" class="form-check-label"><i data-feather="grid" class="me-1"></i> Multi PG</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="bs" id="jenis-bs">
                        <label for="jenis-bs" class="form-check-label"><i data-feather="toggle-right" class="me-1"></i> Benar/Salah</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="jodohkan" id="jenis-jodohkan">
                        <label for="jenis-jodohkan" class="form-check-label"><i data-feather="shuffle" class="me-1"></i> Menjodohkan</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="isian" id="jenis-isian">
                        <label for="jenis-isian" class="form-check-label"><i data-feather="edit-3" class="me-1"></i> Isian</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input filterJenis" value="esai" id="jenis-esai">
                        <label for="jenis-esai" class="form-check-label"><i data-feather="file-text" class="me-1"></i> Esai</label>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                    <i data-feather="activity" class="me-2 text-muted" style="width:16px;height:16px;"></i> Status
                </label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <!-- Reset button -->
            <button id="resetFilter" class="btn btn-outline-primary btn-sm w-100 mt-3 d-flex align-items-center justify-content-center gap-2">
                <i data-feather="refresh-ccw"></i> Reset Filter
            </button>
        </nav>


        <!-- Content -->
        <main id="content">
            <?= $this->renderSection('content') ?>
            <div class="footer-wrapper">
                &copy; <?= date('Y') ?> <?= esc($setting->nama_sekolah) ?> | Built with ❤️
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url("src/plugins/src/jquery/jquery-3.6.0.min.js") ?>"></script>
    <script src="<?= base_url("src/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/plugins/src/font-icons/feather/feather.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebar.classList.add('closing');
            setTimeout(() => {
                sidebar.classList.remove('closing');
            }, 350);
            overlay.classList.remove('show');
        });
    </script>


    <script>
        // Dark mode toggle
        document.querySelector('.theme-toggle').addEventListener('click', () => {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        });
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
        }
    </script>

    <?= $this->renderSection("pagejs") ?>
</body>

</html>