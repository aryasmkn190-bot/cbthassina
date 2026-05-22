<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?= $title ?> </title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>assets/img/favicon.ico" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url() ?>layouts/vertical-light-menu/loader.js"></script>

    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('/assets/fonts/nunito/Nunito-VariableFont_wght.ttf') format('truetype');
            font-weight: 200 900;
            /* range weight yang tersedia */
            font-style: normal;
        }
    </style>
    <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/light/plugins.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="<?= base_url() ?>src/plugins/src/apex/apexcharts.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/light/components/list-group.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/light/dashboard/dash_2.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>src/assets/css/dark/components/list-group.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/dark/dashboard/dash_2.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>src/plugins/src/font-icons/fontawesome/css/regular.css">
    <link rel="stylesheet" href="<?= base_url() ?>src/plugins/src/font-icons/fontawesome/css/fontawesome.css">

    <!--  DATATABLE  -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/plugins/src/table/datatable/datatables.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/plugins/css/light/table/datatable/dt-global_style.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/plugins/css/dark/table/datatable/dt-global_style.css">

    <!--  MODALLL  -->
    <link href="<?= base_url() ?>src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/light/components/carousel.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/light/components/modal.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/light/components/tabs.css" rel="stylesheet" type="text/css">

    <!-- toastr -->
    <link href="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/light/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/dark/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/sweetalerts2/sweetalerts2.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/plugins/css/light/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/css/dark/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/tomSelect/tom-select.default.min.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/css/light/tomSelect/custom-tomSelect.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/css/dark/tomSelect/custom-tomSelect.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/light/forms/switches.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/dark/forms/switches.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/plugins/src/tagify/tagify.css">
    <link href="<?= base_url() ?>src/plugins/css/light/tagify/custom-tagify.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/css/dark/tagify/custom-tagify.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/src/flatpickr/flatpickr.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/src/noUiSlider/nouislider.min.css" rel="stylesheet" type="text/css">
    <!-- END THEME GLOBAL STYLES -->
    <link href="<?= base_url() ?>src/assets/css/light/components/timeline.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/assets/css/dark/components/timeline.css" rel="stylesheet" type="text/css">
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="<?= base_url() ?>src/plugins/css/light/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url() ?>src/plugins/css/dark/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/assets/css/light/widgets/modules-widgets.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>src/assets/css/dark/widgets/modules-widgets.css">
    <link href="<?= base_url() ?>src/assets/css/light/apps/invoice-preview.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/dark/apps/invoice-preview.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/plugins/src/editors/quill/katex.min.css" rel="stylesheet" type="text/css" />
    <!-- <link href="<?= base_url() ?>assets/css/admin-modern.css" rel="stylesheet" type="text/css" /> -->
    <style>
        /* -----------------------------
   UNIVERSAL FORM CONTROL MODERN
----------------------------- */
        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            color: #111827;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* Hover effect */
        .form-control:hover {
            border-color: #c7d2fe;
            box-shadow: 0 3px 8px rgba(79, 70, 229, 0.15);
            transform: translateY(-1px);
        }

        /* Fokus dengan animasi */
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transform: translateY(-2px) scale(1.01);
        }

        /* Disabled */
        .form-control:disabled {
            background: #f3f4f6;
            color: #9ca3af;
            border-color: #e5e7eb;
            box-shadow: none;
            transform: none;
        }

        /* -----------------------------
   LABEL MODERN
----------------------------- */
        .form-group>label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            transition: color 0.3s ease;
        }

        /* Warna label berubah saat input fokus */
        .form-control:focus~label,
        .form-group>label:has(+ .form-control:focus) {
            color: #4f46e5;
        }

        /* -----------------------------
   DARK MODE
----------------------------- */
        body.dark .form-control {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        body.dark .form-control:hover {
            border-color: #818cf8;
            box-shadow: 0 3px 10px rgba(129, 140, 248, 0.2);
        }

        body.dark .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        body.dark .form-group>label {
            color: #9ca3af;
        }

        body.dark .form-control:focus~label,
        body.dark .form-group>label:has(+ .form-control:focus) {
            color: #818cf8;
        }
    </style>
    <?= $this->renderSection("css") ?>
</head>

<body class="layout-boxed">
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
            <a href="javascript:void(0);" class="sidebarCollapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </a>
            <span class="d-none d-sm-inline" style="font-size: large;"><b><?= $setting->appname ?> | <?= $setting->nama_sekolah ?> </b></span>


            <ul class="navbar-item flex-row ms-lg-auto ms-0">
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
                                    <h6 class=""><?= session()->get('user')['full_name'] ?></h6>
                                    <p class=""><?= session()->get('user')['role'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-item">
                            <a href="<?= base_url('auth/panel/logout') ?>">
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

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">

            <?php include "sidebar.php" ?>
        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <?= $this->renderSection('content') ?>
            <!--  BEGIN FOOTER  -->
            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © <span class="dynamic-year"><?= date('Y') ?></span> <?= $setting->nama_sekolah ?></p>
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
    <!-- END MAIN CONTAINER -->
    <script>
        var logoUrl = "<?= base_url('assets/img/') . $setting->logo ?>";
    </script>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/plugins/src/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/mousetrap/mousetrap.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/waves/waves.min.js"></script>
    <script src="<?= base_url() ?>layouts/vertical-light-menu/app.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- toastr -->
    <!-- BEGIN THEME GLOBAL STYLE -->
    <script src="<?= base_url() ?>src/plugins/src/notification/snackbar/snackbar.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/sweetalerts2/sweetalerts2.min.js"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="<?= base_url() ?>src/plugins/src/apex/apexcharts.min.js"></script>
    <link href="<?= base_url() ?>src/plugins/css/light/apex/custom-apexcharts.css" rel="stylesheet" type="text/css">

    <link href="<?= base_url() ?>src/plugins/css/dark/apex/custom-apexcharts.css" rel="stylesheet" type="text/css">
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <!-- <script src="<?= base_url() ?>src/assets/js/dashboard/dash_2.js"></script> -->
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <script src="<?= base_url() ?>src/plugins/src/font-icons/feather/feather.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/table/datatable/datatables.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/tomSelect/tom-select.base.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/tagify/tagify.min.js"></script>
    <script src="<?= base_url() ?>src/plugins/src/flatpickr/flatpickr.js"></script>
    <!-- <script src="<?= base_url() ?>src/plugins/src/cryptojs/crypto-js.min.js"></script> -->


    <?= $this->renderSection("pagejs") ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <!-- <script src="<?= base_url() ?>src/plugins/src/apex/apexcharts.min.js"></script>
    <script src="<?= base_url() ?>src/assets/js/dashboard/dash_1.js"></script> -->
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script>
        function encryptText(text) {
            // Enkripsi teks menggunakan Base64 dan 
            key = "<?= $setting->key_encrypt ?>";
            var encrypted = btoa(text + key);
            return encrypted;
        }

        function decryptText(encrypted_text) {
            // Dekripsi teks menggunakan Base64 dan kunci
            var decrypted = atob(encrypted_text);
            key = "<?= $setting->key_encrypt ?>";
            // Hapus bagian kunci dari teks terdekripsi
            decrypted = decrypted.replace(key, '');
            return decrypted;
        }

        $(".datepickermodal").flatpickr({
            defaultDate: "today",
            static: true
        });
        feather.replace();
        var tomselectElements = document.querySelectorAll(".tomselect");
        if (tomselectElements.length > 0) {
            var tomselect = new TomSelect(".tomselect", {
                valueField: 'id',
                labelField: 'title',
                searchField: 'title',
            });
        }
    </script>
</body>

</html>