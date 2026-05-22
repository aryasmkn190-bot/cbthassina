<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthPesertaController::login');
$routes->get('panel', 'AuthController::login');
$routes->get('unauthorized', 'HomeController::unauthorized');
$routes->group('install', function ($routes) {
    $routes->get('/', 'InstallController::index');           // Halaman instalasi awal (cek koneksi DB)
    $routes->post('dbsave', 'InstallController::saveDb');    // Simpan konfigurasi DB dan cek koneksi
    $routes->post('save-admin', 'InstallController::saveAdmin'); // Simpan data admin ke session
    $routes->get('migrate', 'InstallController::migrate');   // Jalankan migrasi dan buat admin
});

$routes->group('auth', function ($routes) {
    $routes->group('panel', function ($routes) {
        $routes->get('login', 'AuthController::login');
        $routes->post('login', 'AuthController::doLogin');
        $routes->get('logout', 'AuthController::logout');
    });
    $routes->get('login', 'AuthPesertaController::login');
    $routes->post('login', 'AuthPesertaController::doLogin');
    $routes->get('logout', 'AuthPesertaController::logout');
});
$routes->group('peserta', ['filter' => 'role:peserta'], function ($routes) {
    $routes->get('home', 'UjianPesertaController::index');
    $routes->group('ujian', function ($routes) {
        $routes->get('getall', 'UjianPesertaController::getAllUjian');
        $routes->post('cektoken', 'UjianPesertaController::cekToken');
        $routes->get('hasil/(:segment)', 'UjianPesertaController::hasil/$1');
        $routes->get('mulai/(:segment)', 'UjianPesertaController::mulai/$1');
        $routes->get('lanjut/(:segment)', 'UjianPesertaController::lanjut/$1');
        $routes->get('soal/(:segment)', 'UjianPesertaController::getSoal/$1'); // ambil semua soal
        $routes->post('simpan-jawaban', 'UjianPesertaController::simpanJawaban');
        $routes->post('selesai/(:segment)', 'UjianPesertaController::selesaiUjian/$1');
        $routes->get('get-jawaban/(:segment)', 'UjianPesertaController::getJawabanPeserta/$1'); // ambil semua soal
        $routes->get('get-ujian/(:segment)', 'UjianPesertaController::apiGetUjian/$1');
    });
});

// Jalur khusus dibagikan (tanpa filter login)
$routes->group('share/ujian', function ($routes) {
    $routes->get('play/(:any)', 'UjianShareController::play/$1');
    $routes->get('mulai/(:any)', 'UjianShareController::mulai/$1');
    $routes->get('lanjut/(:any)', 'UjianShareController::lanjut/$1');
    $routes->get('soal/(:segment)', 'UjianShareController::getSoal/$1'); // ambil semua soal
    $routes->post('simpan-jawaban', 'UjianShareController::simpanJawaban');
    $routes->post('selesai/(:segment)', 'UjianShareController::selesaiUjian/$1');
    $routes->get('get-jawaban/(:segment)', 'UjianShareController::getJawabanPeserta/$1'); // ambil semua soal
    $routes->get('get-ujian/(:segment)', 'UjianShareController::apiGetUjian/$1');
});

$routes->group('panel', ['filter' => 'role:admin,guru'], function ($routes) {

    $routes->get('home', 'HomeController::index', ['filter' => 'role:admin,guru']);
    $routes->group('pengaturan', ['filter' => 'role:admin'], function ($routes) {
        $routes->group('sekolah', function ($routes) {
            $routes->get('/', 'SettingsController::index');
            $routes->get('getdata', 'SettingsController::getData');
            $routes->post('update', 'SettingsController::update');
        });
        $routes->group('patch', function ($routes) {
            $routes->get('/', 'PatchApp::index');
            $routes->post('update', 'PatchApp::update');
        });
        $routes->group('database', function ($routes) {
            $routes->get('/', 'DatabaseController::index');
            $routes->get('export-json', 'DatabaseController::exportJson');
            $routes->post('restore-json', 'DatabaseController::restoreJson');
            $routes->get('export-sql', 'DatabaseController::exportSql');
            $routes->post('restore-sql', 'DatabaseController::restoreSql');

            $routes->post('delete-tables', 'DatabaseController::deleteTables');
        });
    });
    $routes->group('exambro', ['filter' => 'role:admin,guru'], function ($routes) {
        $routes->get('informasi', 'ExambroSettingController::informasi');
        $routes->group('setting',  function ($routes) {
            $routes->get('/', 'ExambroSettingController::index', ['filter' => 'role:admin']);
            $routes->get('getdata', 'ExambroSettingController::getData');
            $routes->post('update', 'ExambroSettingController::update', ['filter' => 'role:admin']);
            $routes->get('download-config', 'ExambroSettingController::downloadConfig');
        });
        $routes->group('block', ['filter' => 'role:admin'], function ($routes) {
            $routes->get('/', 'ExambroBlockController::index');
            $routes->get('list', 'ExambroBlockController::getAll');
            $routes->post('create', 'ExambroBlockController::create');
            $routes->post('update/(:num)', 'ExambroBlockController::update/$1');
            $routes->post('delete/(:num)', 'ExambroBlockController::delete/$1');
        });
        $routes->group('menu', ['filter' => 'role:admin'], function ($routes) {
            $routes->get('/', 'ExambroMenuController::index');
            $routes->get('list', 'ExambroMenuController::getAll');
            $routes->post('create', 'ExambroMenuController::create');
            $routes->post('update/(:num)', 'ExambroMenuController::update/$1');
            $routes->post('delete/(:num)', 'ExambroMenuController::delete/$1');
        });
    });
    $routes->group('qrgenerator', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('server', 'QrGeneratorController::index');
        $routes->post('generate', 'QrGeneratorController::generate');
        $routes->post('generateserver', 'QrGeneratorController::generateserver');
        $routes->post('generateprint', 'QrGeneratorController::generateprint');
        $routes->post('generateprintlink', 'QrGeneratorController::generateprintlink');
        $routes->get('qrcode', 'QrGeneratorController::qrcode');
        $routes->get('qrujian', 'QrGeneratorController::qrujian');
        $routes->get('linkujian', 'QrGeneratorController::ujian');
        $routes->get('sebconfig', 'SebConfigController::index');       // form generate
        $routes->post('sebconfig/generate', 'SebConfigController::generate'); // proses generate
        $routes->post('sebconfig/upload', 'SebConfigController::upload'); // proses generate
    });

    $routes->group('users', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('list', 'UserController::getAll');
        $routes->post('create', 'UserController::create');
        $routes->post('importExcel', 'UserController::importExcel');
        $routes->post('update/(:segment)', 'UserController::update/$1');
        $routes->post('delete/(:segment)', 'UserController::delete/$1');
    });
    $routes->group('banksoal', function ($routes) {
        $routes->get('/', 'BankSoalController::index');               // tampilan utama
        $routes->get('list', 'BankSoalController::getAll');           // ambil data untuk DataTables
        $routes->post('create', 'BankSoalController::create');        // tambah data
        $routes->post('update/(:segment)', 'BankSoalController::update/$1');
        $routes->post('update_visibility/(:segment)', 'BankSoalController::update_visibility/$1'); // update data
        $routes->post('duplicate/(:segment)', 'BankSoalController::duplicate/$1');
        $routes->post('delete/(:segment)', 'BankSoalController::delete/$1'); // hapus data
        $routes->group('soal', function ($routes) {
            $routes->get('(:segment)', 'SoalController::index/$1');            // Menampilkan form soal berdasarkan bank_soal_id
            $routes->get('list/(:segment)', 'SoalController::getAll/$1');      // Mendapatkan semua soal via AJAX
            $routes->get('detail/(:segment)', 'SoalController::getSoalById/$1');
            $routes->get('print/(:segment)', 'SoalController::printSoal/$1');
            $routes->post('create', 'SoalController::create');
            $routes->get('export/(:segment)', 'SoalController::exportExcel/$1');
            $routes->post('import', 'SoalController::import');
            $routes->post('upload/image', 'SoalController::image');
            $routes->post('upload/audio', 'SoalController::audio');
            $routes->post('upload/video', 'SoalController::video');
            $routes->post('delete-file', 'SoalController::deleteFile');

            $routes->post('update/(:segment)', 'SoalController::update/$1');   // Update soal
            $routes->post('delete/(:segment)', 'SoalController::delete/$1');   // Hapus soal
        });
        $routes->group('topik', static function ($routes) {
            $routes->get('(:segment)', 'TopikSoalController::index/$1');
            $routes->get('list/(:segment)', 'TopikSoalController::getAll/$1');
            $routes->post('create', 'TopikSoalController::create');
            $routes->post('update/(:segment)', 'TopikSoalController::update/$1');
            $routes->post('delete/(:segment)', 'TopikSoalController::delete/$1');
        });
    });
    $routes->group('beritaacara', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'BeritaAcaraController::index');
        $routes->get('list', 'BeritaAcaraController::getAll');
        $routes->post('create', 'BeritaAcaraController::create');
        $routes->post('update/(:segment)', 'BeritaAcaraController::update/$1');
        $routes->delete('delete/(:segment)', 'BeritaAcaraController::delete/$1');
        $routes->get('print/(:segment)', 'BeritaAcaraController::print/$1');
    });

    $routes->group('peserta', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'PesertaController::index');
        $routes->get('getAll', 'PesertaController::getAll');
        $routes->post('create', 'PesertaController::create');
        $routes->post('import', 'PesertaController::import');
        $routes->post('update/(:segment)', 'PesertaController::update/$1');
        $routes->post('delete/(:segment)', 'PesertaController::delete/$1');
        // 🔽 Tambahan untuk update password via Excel
        $routes->post('updatePasswordByExcel', 'PesertaController::updatePasswordByExcel');
        $routes->get('downloadTemplateUpdatePassword', 'PesertaController::downloadTemplateUpdatePassword');
        $routes->get('download', 'PesertaController::downloadExcel');
    });

    $routes->group('ujian',  function ($routes) {
        $routes->get('/', 'UjianController::index');
        $routes->get('list', 'UjianController::list');
        $routes->post('create', 'UjianController::create');
        $routes->post('update/(:segment)', 'UjianController::update/$1');
        $routes->post('delete/(:segment)', 'UjianController::delete/$1');
        $routes->get('filter-peserta/(:segment)', 'UjianController::filterPeserta/$1');
        $routes->get('peserta-ujian/(:segment)', 'UjianController::pesertaUjian/$1');
        $routes->post('add-peserta', 'UjianController::addPeserta');
        $routes->post('cek-peserta-diuji', 'UjianController::cekPesertaDiuji');
        $routes->post('update-share/(:segment)', 'UjianController::updateShare/$1');
        $routes->post('remove-peserta', 'UjianController::removePeserta');
        $routes->post('updateMassal', 'UjianController::updateMassal');
        $routes->get('exportBelumUjian', 'UjianController::exportBelumUjian');
        $routes->get('detail/(:segment)', 'HasilUjianController::index/$1'); // Tampilkan hasil ujian
    });
    // Tingkat
    $routes->group('tingkat', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'TingkatController::index');
        $routes->get('list', 'TingkatController::getAll');
        $routes->post('create', 'TingkatController::create');
        $routes->post('update/(:segment)', 'TingkatController::update/$1');
        $routes->post('delete/(:segment)', 'TingkatController::delete/$1');
    });

    $routes->group('ruang', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'RuangController::index');
        $routes->get('list', 'RuangController::getAll');
        $routes->post('create', 'RuangController::create');
        $routes->post('update/(:segment)', 'RuangController::update/$1');
        $routes->post('delete/(:segment)', 'RuangController::delete/$1');
    });

    $routes->group('sesi', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'SesiController::index');
        $routes->get('list', 'SesiController::getAll');
        $routes->post('create', 'SesiController::create');
        $routes->post('update/(:segment)', 'SesiController::update/$1');
        $routes->post('delete/(:segment)', 'SesiController::delete/$1');
    });

    // Kelas
    $routes->group('kelas', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'KelasController::index');
        $routes->get('list', 'KelasController::getAll');
        $routes->post('create', 'KelasController::create');
        $routes->post('update/(:segment)', 'KelasController::update/$1');
        $routes->post('delete/(:segment)', 'KelasController::delete/$1');
    });

    // Jurusan
    $routes->group('jurusan', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'JurusanController::index');
        $routes->get('list', 'JurusanController::getAll');
        $routes->post('create', 'JurusanController::create');
        $routes->post('update/(:segment)', 'JurusanController::update/$1');
        $routes->post('delete/(:segment)', 'JurusanController::delete/$1');
    });

    // Agama
    $routes->group('agama', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'AgamaController::index');
        $routes->get('list', 'AgamaController::getAll');
        $routes->post('create', 'AgamaController::create');
        $routes->post('update/(:segment)', 'AgamaController::update/$1');
        $routes->post('delete/(:segment)', 'AgamaController::delete/$1');
    });
    // Agama
    $routes->group('jenis-ujian', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'JenisUjianController::index');
        $routes->get('list', 'JenisUjianController::getAll');
        $routes->post('create', 'JenisUjianController::create');
        $routes->post('update/(:segment)', 'JenisUjianController::update/$1');
        $routes->post('delete/(:segment)', 'JenisUjianController::delete/$1');
    });
    $routes->group('sinkronisasi', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'SinkronisasiController::index');
        $routes->post('proses', 'SinkronisasiController::proses');
        $routes->post('simpan_koneksi', 'SinkronisasiController::simpan_koneksi');
    });
    // Agama
    $routes->group('kartu', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'KartuController::index');
        $routes->post('preview', 'KartuController::preview');
        $routes->get('print', 'KartuController::print');
    });
    $routes->group('analisis-butir', ['filter' => 'role:admin,guru'], function ($routes) {
        $routes->get('/', 'AnalisisButirController::index');
        $routes->get('ujian/(:segment)', 'AnalisisButirController::pilihUjian/$1');
        $routes->get('detail/(:segment)', 'AnalisisButirController::detail/$1');
        $routes->get('api/(:segment)', 'AnalisisButirController::apiAnalisis/$1');
        $routes->get('export/(:segment)', 'AnalisisButirController::exportExcel/$1');
    });
    $routes->group('hasil-ujian', function ($routes) {
        $routes->get('get/(:segment)', 'HasilUjianController::getAll/$1');
        $routes->get('exportskoring/(:segment)', 'HasilUjianController::exportSkoring/$1');
        $routes->post('ulang/(:segment)', 'HasilUjianController::ulangUjian/$1');
        $routes->post('reset-device', 'HasilUjianController::resetDevice');
        $routes->get('jawaban/(:segment)', 'HasilUjianController::jawaban/$1');
        $routes->post('sinkronisasi', 'HasilUjianController::sinkronisasi');
        $routes->post('selesai/(:segment)', 'UjianPesertaController::selesaiUjian/$1');
        $routes->post('selesai-semua/(:segment)', 'UjianPesertaController::selesaiSemua/$1');
        $routes->post('koreksi-ulang/(:segment)', 'UjianPesertaController::koreksiUlang/$1');
        $routes->post('koreksi-esai', 'UjianPesertaController::koreksiEssai');
    });
    $routes->group('ruangsesi', function ($routes) {
        $routes->get('/', 'RuangSesiController::index');
        $routes->post('get-peserta', 'RuangSesiController::getPesertaByKelas');
        $routes->post('simpanPenempatan', 'RuangSesiController::simpanRuangSesi');
        $routes->get('print-daftarhadir', 'RuangSesiController::print');
        $routes->get('printDenah', 'RuangSesiController::printDenah');
    });
});

$routes->group('api/exambro', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('get', 'ExambroController::getConfig');
    $routes->post('update', 'ExambroController::updateSetting');
    $routes->post('login_peserta', 'AuthApiController::login');
    $routes->get('ujian', 'UjianPesertaApiController::listUjian');
    $routes->post('mulai-ujian', 'UjianPesertaApiController::mulaiUjian');
    $routes->post('lanjut-ujian', 'UjianPesertaApiController::lanjutUjian');
    $routes->post('simpan-jawaban', 'UjianPesertaApiController::simpanJawaban');
    $routes->post('selesai-ujian', 'UjianPesertaApiController::selesaiUjian');
    $routes->get('jawaban/(:segment)', 'UjianPesertaApiController::getJawabanPeserta/$1');
    $routes->post('cek-token', 'UjianPesertaApiController::cekToken');
    $routes->get('soal/(:segment)', 'UjianPesertaApiController::soal/$1');
    $routes->get('ujian/(:segment)', 'UjianPesertaApiController::getUjianDetail/$1');
});
// app/Config/Routes.php

$routes->group('public', ['namespace' => 'App\Controllers'], function ($routes) {
    // Daftar bank soal publik
    $routes->get('banksoal', 'PublicBankSoalController::index');

    // Detail bank soal publik
    $routes->get('banksoal/detail/(:num)', 'PublicBankSoalController::detail/$1');

    // API list (untuk AJAX)
    $routes->get('banksoal/list', 'PublicBankSoalController::getAll');
});

// --- SEB Config ---


$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('cekkoneksi', 'SinkronisasiApiController::cekKoneksi');
    $routes->get('sinkronisasi', 'SinkronisasiApiController::proses');
    $routes->post('kirimujian', 'SinkronisasiApiController::kirimujian');
});
