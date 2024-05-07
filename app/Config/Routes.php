<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/auth', 'Auth::index');
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
    $routes->group("hasilbaca", static function ($routes) {
        $routes->get("", "Api\HasilBaca::index");
        $routes->get("gagal", "Api\HasilBaca::getDataGagal/$1");
    });
    $routes->group("verif", static function ($routes) {
        $routes->get("", "Api\Verif::index");
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
        $routes->get("target", "Api\Laporan\Target::index");
        $routes->get("kondisi", "Api\Laporan\Kondisi::index");
        $routes->get("hasil_baca_0", "Api\Laporan\HasilBaca0::index");
        $routes->get("kondisi_pakai_0", "Api\Laporan\KondisiBaca0::index");
    });
});
$routes->group("hasilbaca", static function ($routes) {
    $routes->get("verif", "HasilBaca\Verif::index");
    $routes->get("cekfoto", "HasilBaca\CekFoto::index");
    $routes->get("selisih", "HasilBaca\Selisih::index");
});

$routes->group("laporan", static function ($routes) {
    $routes->get("target", "Laporan\Target::index");
    $routes->get("kondisi", "Laporan\Kondisi::index");
    $routes->get("hasil_baca_0", "Laporan\HasilBaca0::index");
    $routes->get("kondisi_pakai_0", "Laporan\KondisiBaca0::index");
});
