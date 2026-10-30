<?php
class Router {
    private array $routes = [
        ['GET',    '/mahasiswa',      'MahasiswaController@index'],
        ['POST',   '/mahasiswa',      'MahasiswaController@store'],
        ['PUT',    '/mahasiswa/{id}', 'MahasiswaController@update'],
        ['DELETE', '/mahasiswa/{id}', 'MahasiswaController@destroy'],
        ['POST',   '/login',          'AuthController@login'],
        ['POST',   '/register',       'AuthController@register'],
    ];

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];

        // Hitung path relatif terhadap folder public/ (agar jalan di subfolder mana pun)
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($base !== '' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');
        if ($path === '/index.php') $path = '/';

        if ($path === '/') {
            Response::json(200, true, 'API Sistem Informasi Akademik berjalan');
        }

        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m !== $method) continue;
            $regex = '#^' . preg_replace('#\{[a-z]+\}#', '([^/]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $match)) {
                array_shift($match);
                [$class, $action] = explode('@', $handler);
                call_user_func_array([new $class(), $action], array_map('urldecode', $match));
                return;
            }
        }
        Response::json(404, false, 'Resource tidak ditemukan: ' . $method . ' ' . $path);
    }
}
