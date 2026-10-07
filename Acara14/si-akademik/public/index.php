<?php

// FRONT CONTROLLER - semua request masuk lewat file ini (lihat .htaccess)

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Cookie session lebih aman: tidak bisa dibaca JavaScript
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// Error teknis PHP tidak ditampilkan ke pengguna, tetapi dicatat ke storage/logs/app.log
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/storage/logs/app.log');
error_reporting(E_ALL);

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

// Penangkap terakhir: exception yang tidak tertangani dicatat, pengguna hanya melihat pesan aman
set_exception_handler(function (Throwable $e) {
    Logger::error('Exception tidak tertangani', $e);

    if (!headers_sent()) {
        http_response_code(500);
    }
    echo 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
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
