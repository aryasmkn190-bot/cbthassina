<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Halaman Tidak Ditemukan </title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>src/assets/img/favicon.ico" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url() ?>layouts/vertical-light-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
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
    <link href="<?= base_url() ?>src/assets/css/light/pages/error/error.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>layouts/vertical-light-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>src/assets/css/dark/pages/error/error.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <style>
        body.dark .theme-logo.dark-element {
            display: inline-block;
        }

        .theme-logo.dark-element {
            display: none;
        }

        body.dark .theme-logo.light-element {
            display: none;
        }

        .theme-logo.light-element {
            display: inline-block;
        }
    </style>

</head>

<body class="error text-center">

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mr-auto mt-5 text-md-left text-center">
                <!-- <a href="index.html" class="ml-md-5">
                    <img alt="image-404" src="<?= base_url() ?>src/assets/img/logo.svg" class="dark-element theme-logo">
                    <img alt="image-404" src="<?= base_url() ?>src/assets/img/logo2.svg" class="light-element theme-logo">
                </a> -->
            </div>
        </div>
    </div>
    <div class="container-fluid error-content">
        <div class="">
            <h1 class="error-number">404</h1>
            <p class="mini-text">Ooops!</p>
            <p class="error-text mb-5 mt-1">The page you requested was not found!</p>
            <img src="<?= base_url() ?>src/assets/img/error.svg" alt="EQUATION-admin-404" class="error-img">
            <a href="#" onclick="history.back(); return false;" class="btn btn-dark mt-5">Go Back</a>

        </div>
    </div>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url() ?>src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
</body>

</html>