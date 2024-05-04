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
});
$routes->group("hasilbaca", static function ($routes) {
    $routes->get("verif", "HasilBaca\Verif::index");
});
