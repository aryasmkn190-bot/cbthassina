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
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            /* range weight yang tersedia */
            font-style: normal;
        }
    </style>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet"> -->
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

            <!-- <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg></a> -->

            <ul class="navbar-item theme-brand flex-row  text-center">
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
                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
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
                                <div class="emoji me-2">
                                    &#x1F44B;
                                </div>
                                <div class="media-body">
                                    <?php if (session()->get('peserta')): ?>
                                        <h6><?= esc(session()->get('peserta')['nama']) ?></h6>
                                        <p><?= esc(session()->get('peserta')['role']) ?></p>
                                    <?php else: ?>
                                        <h6>Guest</h6>
                                        <p>Tanpa Login</p>
                                    <?php endif; ?>
                                </div>

                            </div>
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
    </script>
</body>

</html>