<?php

// FRONT CONTROLLER - semua request masuk lewat file ini (lihat .htaccess)

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Autoloader sederhana (tanpa Composer)
spl_autoload_register(function (string $class) {
    foreach (['Core', 'Controllers', 'Models', 'Middleware', 'Services', 'Repositories'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$config = require ROOT_PATH . '/config/app.php';
$routes = require ROOT_PATH . '/routes/web.php';

// Ambil path URL, lalu buang base folder (mis. /si-akademik/public)
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

define('BASE_URL', $base);   // contoh: /si-akademik/public

$uri = '/' . trim($uri, '/');   // "" menjadi "/", "/mahasiswa/" menjadi "/mahasiswa"

(new Router($routes))->dispatch($uri, $_SERVER['REQUEST_METHOD']);
