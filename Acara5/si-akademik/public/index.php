<?php

// FRONT CONTROLLER - semua request masuk lewat file ini (lihat .htaccess)

$routes = require __DIR__ . '/../routes/web.php';

// Ambil path URL, lalu buang base folder (mis. /si-akademik/public)
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

$uri    = '/' . trim($uri, '/');   // "" menjadi "/", "/mahasiswa/" menjadi "/mahasiswa"
$method = $_SERVER['REQUEST_METHOD'];

$handler = null;
$params  = [];

// 1. Route biasa (cocok persis)
if (isset($routes[$method][$uri])) {
    $handler = $routes[$method][$uri];
} else {
    // 2. Route dengan parameter, contoh: /mahasiswa/5
    foreach ($routes[$method] ?? [] as $pattern => $target) {
        if (!str_contains($pattern, '{')) {
            continue;
        }

        $regex = '#^' . preg_replace('#\{[a-z_]+\}#', '([0-9]+)', $pattern) . '$#';

        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);
            $handler = $target;
            $params  = $matches;
            break;
        }
    }
}

// 3. Tidak ada route yang cocok
if ($handler === null) {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
    exit;
}

[$controllerName, $action] = $handler;

require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

$controller = new $controllerName();
$controller->$action(...$params);
