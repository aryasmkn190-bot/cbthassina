<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?> </title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>assets/img/favicon.ico" />
    <link href="<?= base_url() ?>layouts/horizontal-light-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/horizontal-light-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url() ?>layouts/horizontal-light-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/horizontal-light-menu/css/light/plugins.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/horizontal-light-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <link href="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/light/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/dark/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/sweetalerts2/sweetalerts2.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/plugins/css/light/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/dark/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />

    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            font-style: normal;
        }

        /* ===== CSS Variable System ===== */
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --card-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #311042 100%);
            --text-color: #334155;
            --title-color: #0f172a;
            --border-radius-custom: 16px;
            --card-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);
        }

        /* Premium micro-animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .hover-scale {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s;
        }
        .hover-scale:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.08);
        }
        .hover-scale:active {
            transform: scale(0.96);
        }

        /* Laser Line Animation for Absen QR */
        @keyframes scanAnim {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
        .laser-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: #22c55e;
            top: 0;
            box-shadow: 0 0 10px #22c55e;
            animation: scanAnim 2s infinite linear;
        }

        /* Adapt layout for mobile apps */
        @media (max-width: 767.98px) {
            .header-container {
                display: none !important;
            }
            .footer-wrapper {
                display: none !important;
            }
            #content, .main-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            .layout-px-spacing {
                padding: 0 !important;
                margin-top: 0 !important;
            }
            body {
                background-color: #f8fafc !important;
                padding-bottom: 85px !important; /* Space for bottom nav */
            }
            .middle-content {
                padding: 0 16px !important;
                margin-top: 0 !important;
            }
        }

        /* ===== Mobile Header ===== */
        .mobile-app-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1030;
            margin-bottom: 20px;
            padding: 12px 16px;
        }

        /* ===== Mobile Bottom Sticky Nav Bar ===== */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 68px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 -4px 15px rgba(0,0,0,0.03);
            padding-bottom: env(safe-area-inset-bottom);
            z-index: 1040;
        }
        .bottom-nav-item {
            color: #8fa0b5;
            font-size: 0.65rem;
            font-weight: 700;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: color 0.2s;
        }
        .bottom-nav-item.active {
            color: #4f46e5;
        }
        .bottom-nav-item svg {
            margin-bottom: 3px;
            transition: transform 0.2s;
        }
        .bottom-nav-item:active svg {
            transform: scale(0.85);
        }
        .center-scan-btn {
            position: relative;
            top: -14px;
            margin-bottom: -14px;
        }
        .scan-btn-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transition: transform 0.2s;
            border: 4px solid #fff;
        }
        .center-scan-btn:active .scan-btn-circle {
            transform: scale(0.9);
        }

        /* Modal styling */
        .modal-content-custom {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        .modal-header-custom {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 22px 24px 18px 24px;
        }
        .modal-body-custom {
            padding: 24px;
        }

        /* Dark Mode overrides */
        body.dark {
            --text-color: #cbd5e1;
            --title-color: #ffffff;
        }
        body.dark .mobile-app-header {
            background: #191e3a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        body.dark .bg-glass {
            background: rgba(25, 30, 58, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        body.dark .scan-btn-circle {
            border-color: #0e1726;
        }
        body.dark .modal-content-custom {
            background: #191e3a;
            color: #fff;
        }
        body.dark .modal-header-custom {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
        body.dark .bottom-nav-item {
            color: #8fa0b5;
        }
        body.dark .bottom-nav-item.active {
            color: #8b5cf6;
        }
    </style>

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/assets/css/light/widgets/modules-widgets.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/assets/css/dark/widgets/modules-widgets.css">
    <link href="<?= base_url() ?>src/assets/css/light/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/dark/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/editors/quill/katex.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <?= $this->renderSection('css') ?>
</head>

<body class="layout-boxed enable-secondaryNav">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container container-xxl">
        <header class="header navbar navbar-expand-sm expand-header">
            <ul class="navbar-item theme-brand flex-row text-center">
                <li class="nav-item theme-logo">
                    <a href="javascript:void(0)">
                        <img src="<?= base_url('assets/img/') . $setting->logo ?>" class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <a href="javascript:void(0)" class="nav-link"> CANDY EXAM </a>
                </li>
            </ul>
            <ul class="navbar-item flex-row ms-lg-auto ms-0 action-area">
                <li class="nav-item theme-toggle-item">
                    <a href="javascript:void(0);" class="nav-link theme-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon dark-mode">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun light-mode">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                    </a>
                </li>
                <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="<?= base_url() ?>src/assets/img/profile-30.png" class="rounded-circle">
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="emoji me-2">&#x1F44B;</div>
                                <div class="media-body">
                                    <?php if (session()->get('peserta')): ?>
                                        <h6><?= esc(session()->get('peserta')['nama']) ?></h6>
                                        <p>Siswa</p>
                                    <?php else: ?>
                                        <h6>Guest</h6>
                                        <p>Tanpa Login</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?= base_url('siswa/profil') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg> <span>Profil Saya</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?= base_url('auth/logout') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg> <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <!-- 📱 Mobile App Header (Only visible on mobile screens) -->
            <div class="mobile-app-header d-md-none">
                <div class="d-flex justify-content-between align-items-center w-100 py-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-container" onclick="showQRCard()" style="cursor: pointer;">
                            <img src="<?= base_url() ?>src/assets/img/profile-30.png" alt="Profile" class="rounded-circle border border-2 border-white shadow-sm" width="40" height="40">
                        </div>
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem; line-height: 1.1;">Halo,</span>
                            <span class="fw-bold text-dark d-block student-greeting" style="font-size: 0.92rem; line-height: 1.2; cursor: pointer;" onclick="showQRCard()"><?= esc(session()->get('peserta')['nama'] ?? 'Siswa') ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#beritaModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-dark"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55rem; padding: 0.25em 0.4em; margin-top: -2px; margin-left: -2px;">3</span>
                        </div>
                        <a href="<?= base_url('auth/logout') ?>" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>

            <!--  BEGIN FOOTER  -->
            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © <?= $setting->nama_sekolah ?>, <span class="dynamic-year"><?= date('Y') ?></span> </p>
                </div>
                <div class="footer-section f-section-2">
                    <p class="">Coded with <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg></p>
                </div>
            </div>
            <!--  END FOOTER  -->
        </div>
        <!--  END CONTENT AREA  -->
    </div>

    <!-- 📱 Mobile Sticky Bottom Navigation (Hidden on desktop) -->
    <?php
        $uri = service('uri');
        $segment = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '';
        $firstSegment = $uri->getTotalSegments() >= 1 ? $uri->getSegment(1) : '';
    ?>
    <div class="mobile-bottom-nav d-md-none fixed-bottom shadow-lg">
        <div class="d-flex justify-content-around align-items-center py-2 bg-glass" style="height: 100%;">
            <a href="<?= base_url('peserta/home') ?>" class="bottom-nav-item <?= ($firstSegment === 'peserta' || $segment === 'home') ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>Beranda</span>
            </a>
            <a href="<?= base_url('siswa/jadwal') ?>" class="bottom-nav-item <?= ($segment === 'jadwal') ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Jadwal</span>
            </a>
            <a href="<?= base_url('siswa/absensi') ?>" class="bottom-nav-item center-scan-btn <?= ($segment === 'absensi') ? 'active' : '' ?>">
                <div class="scan-btn-circle shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M3 7V5a2 2 0 0 1 2-2h2"></path><path d="M17 3h2a2 2 0 0 1 2 2v2"></path><path d="M21 17v2a2 2 0 0 1-2 2h-2"></path><path d="M7 21H5a2 2 0 0 1-2-2v-2"></path><rect x="7" y="7" width="10" height="10" rx="1"></rect></svg>
                </div>
                <span>Absen</span>
            </a>
            <a href="<?= base_url('siswa/rapor') ?>" class="bottom-nav-item <?= ($segment === 'rapor') ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                <span>Rapor</span>
            </a>
            <a href="<?= base_url('siswa/profil') ?>" class="bottom-nav-item <?= ($segment === 'profil') ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Profil</span>
            </a>
        </div>
    </div>

    <!-- Common Shared Modals -->
    <div class="modal fade" id="beritaModal" tabindex="-1" aria-labelledby="beritaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark" id="beritaModalLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>Informasi & Pengumuman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <div class="berita-list">
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-primary">
                            <span class="text-primary small fw-bold d-block mb-1">26 Mei 2026</span>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Pengumuman Libur Hari Raya Waisak</h6>
                            <p class="text-muted small mb-0">Sehubungan dengan Hari Raya Waisak, kegiatan belajar mengajar ditiadakan pada tanggal 28 Mei 2026 dan masuk kembali tanggal 29 Mei 2026.</p>
                        </div>
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-purple" style="border-left-color: #8b5cf6 !important;">
                            <span class="text-purple small fw-bold d-block mb-1" style="color: #8b5cf6;">20 Mei 2026</span>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Jadwal Ulang Ujian Susulan Praktek Olahraga</h6>
                            <p class="text-muted small mb-0">Ujian susulan praktek lari 12 menit bagi siswa yang sakit dilaksanakan hari Kamis di stadion utama pukul 14:00 WIB.</p>
                        </div>
                        <div class="p-3 bg-light rounded-4 border-start border-4 border-success" style="border-left-color: #10b981 !important;">
                            <span class="text-success small fw-bold d-block mb-1" style="color: #10b981;">15 Mei 2026</span>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Vaksinasi Booster Gratis Tahap-2</h6>
                            <p class="text-muted small mb-0">Bagi siswa yang ingin mendapatkan vaksin booster gratis, silakan kumpulkan fotokopi KK ke ruang UKS maksimal hari Jumat ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="studentQRCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-custom">
                <div class="modal-body modal-body-custom text-center py-4">
                    <h6 class="fw-bold text-dark mb-3">QR Pelajar Digital</h6>
                    <div class="p-3 bg-white d-inline-block rounded-4 shadow-sm mb-3 border">
                        <svg width="140" height="140" viewBox="0 0 100 100" style="display: block;">
                            <path d="M5 5h25v5H10v20H5V5zm65 0h25v25h-5V10H70V5zM5 70h5v20h20v5H5V70zm90 0v25H70v-5h20V70h5z" fill="#0f172a"/>
                            <path d="M15 15h20v20H15V15zm5 5v10h10V20H20z" fill="#0f172a"/>
                            <path d="M22 22h6v6h-6v-6z" fill="#4f46e5"/>
                            <path d="M65 15h20v20H65V15zm5 5v10h10V20H70z" fill="#0f172a"/>
                            <path d="M72 22h6v6h-6v-6z" fill="#4f46e5"/>
                            <path d="M15 65h20v20H15V65zm5 5v10h10V20H20z" fill="#0f172a"/>
                            <path d="M22 72h6v6h-6v-6z" fill="#4f46e5"/>
                            <rect x="42" y="15" width="6" height="6" fill="#0f172a"/>
                            <rect x="52" y="15" width="8" height="6" fill="#4f46e5"/>
                            <rect x="42" y="25" width="12" height="6" fill="#0f172a"/>
                            <rect x="48" y="35" width="6" height="12" fill="#4f46e5"/>
                            <rect x="15" y="42" width="6" height="6" fill="#0f172a"/>
                            <rect x="25" y="48" width="6" height="12" fill="#0f172a"/>
                            <rect x="35" y="42" width="12" height="6" fill="#4f46e5"/>
                            <rect x="65" y="42" width="18" height="6" fill="#0f172a"/>
                            <rect x="75" y="52" width="6" height="12" fill="#4f46e5"/>
                            <rect x="85" y="48" width="8" height="6" fill="#0f172a"/>
                            <rect x="42" y="65" width="6" height="18" fill="#4f46e5"/>
                            <rect x="52" y="75" width="12" height="6" fill="#0f172a"/>
                            <rect x="75" y="75" width="12" height="12" fill="#4f46e5"/>
                            <rect x="65" y="65" width="6" height="6" fill="#0f172a"/>
                            <circle cx="50" cy="50" r="10" fill="white"/>
                            <path d="M50 43c-3.8 0-7 3.2-7 7s3.2 7 7 7 7-3.2 7-7-3.2-7-7-7zm0 2c2.8 0 5 2.2 5 5s-2.2 5-5 5-5-2.2-5-5 2.2-5 5-5z" fill="#4f46e5"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold text-dark student-name-card-qr mb-0"><?= esc(session()->get('peserta')['nama'] ?? 'Nama Siswa') ?></h6>
                    <p class="text-muted small mb-0">NISN: <?= esc(session()->get('peserta')['nisn'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        var logoUrl = "<?= base_url('assets/img/') . $setting->logo ?>";
    </script>
    <!-- END MAIN CONTAINER -->
    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/mousetrap/mousetrap.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/waves/waves.min.js"></script>
    <script src="<?= base_url() ?>layouts/horizontal-light-menu/app.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/plugins/src/font-icons/feather/feather.min.js"></script>
    <!-- BEGIN THEME GLOBAL STYLE -->
    <script src="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/sweetalerts2/sweetalerts2.min.js"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <?= $this->renderSection('pagejs') ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            feather.replace();
        });

        function showQRCard() {
            const modal = new bootstrap.Modal(document.getElementById('studentQRCardModal'));
            modal.show();
        }

        // Web Audio API Sound Generator for Scan Beep
        function playScanBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(1000, audioCtx.currentTime); 
                gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime);
                oscillator.start();
                setTimeout(() => {
                    oscillator.stop();
                }, 120);
            } catch(e) {
                console.log("Audio simulation failed or blocked: ", e);
            }
        }
    </script>
</body>
</html>