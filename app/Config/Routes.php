<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/auth', 'Auth::index');
$routes->get('/auth/login', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

$routes->group("api", static function ($routes) {
    $routes->group("sikompak", static function ($routes) {
        $routes->get("cabang", "Api\Sikompak\Cabang::index");
        $routes->group("pegawai", static function ($routes) {
            $routes->get("", "Api\Sikompak\Pegawai::index");
            $routes->get("kampung", "Api\Sikompak\Pegawai::findKampung");
        });
    });
    $routes->group("master", static function ($routes) {
        $routes->group("user", static function ($routes) {
            $routes->get("", "Api\Master\User::index");
            $routes->get("(:any)/kampung", "Api\Master\User::kampung/$1");
            $routes->post("", "Api\Master\User::save");
            $routes->delete("(:any)", "Api\Master\User::delete/$1");
        });
    });
    $routes->group("hasilbaca", static function ($routes) {
        $routes->get("", "Api\HasilBaca::index");
        $routes->get("gagal", "Api\HasilBaca::getDataGagal/$1");
    });
    $routes->group("cekfoto", static function ($routes) {
        $routes->get("", "Api\CekFoto::index");
        $routes->get("detail/(:num)", "Api\CekFoto::detail/$1");
    });
    $routes->group("selisih",  static function ($routes) {
        $routes->get("", "Api\Selisih::index");
        $routes->get("default_property", "Api\Selisih::defaultPropertyGrid");
    });
    $routes->group("laporan", static function ($routes) {
        $routes->get("hasil_baca", "Api\Laporan\HasilBaca::index");
        $routes->get("target", "Api\Laporan\Target::index");
        $routes->get("kondisi", "Api\Laporan\Kondisi::index");
        $routes->get("hasil_baca_0", "Api\Laporan\HasilBaca0::index");
        $routes->get("kondisi_pakai_0", "Api\Laporan\KondisiBaca0::index");
    });
});

$routes->group("master", static function ($routes) {
    $routes->group("user", static function ($routes) {
        $routes->get("", "Master\User::index");
        $routes->get("add", "Master\User::add");
    });
    $routes->group("transfer_kampung", static function ($routes) {
        $routes->get("", "Master\TransferKampung::index");
    });
});
$routes->group("hasilbaca", static function ($routes) {
    $routes->get("verif", "HasilBaca\Verif::index");
    $routes->get("cekfoto", "HasilBaca\CekFoto::index");
    $routes->get("selisih", "HasilBaca\Selisih::index");
});

$routes->group("laporan", static function ($routes) {
    $routes->get("hasil_baca", "Laporan\HasilBaca::index");
    $routes->get("target", "Laporan\Target::index");
    $routes->get("kondisi", "Laporan\Kondisi::index");
    $routes->get("hasil_baca_0", "Laporan\HasilBaca0::index");
    $routes->get("kondisi_pakai_0", "Laporan\KondisiBaca0::index");
});

$routes->group("cetak", static function ($routes) {
    $routes->get("foto_meter/(:any)/(:any)/(:num)", "Cetak\FotoMeter::index/$1/$2/$3");
    $routes->get("selisih_foto", "Cetak\SelisihFoto::index");
    $routes->get("detail_hasil_baca", "Cetak\DetailHasilBaca::index");
    $routes->get("rekap_kondisi_baca", "Cetak\RekapKondisiBaca::index");
    $routes->get("detail_hasil_baca_0", "Cetak\DetailHasilBaca0::index");
    $routes->get("rekap_kondisi_baca_0", "Cetak\RekapKondisiBaca0::index");
});
