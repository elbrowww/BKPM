<?php
class Router {
    // Peta sederhana: nama resource pada URL => nama class Controller
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'register' => 'AuthController',
    ];

    public function dispatch(): void {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segments = explode('/', $uri);
        $resource = end($segments) ?: 'mahasiswa';

        $controllerName = $this->map[$resource] ?? null;
        if (!$controllerName) {
            Response::json(404, false, 'Endpoint tidak ditemukan.');
        }

        $controller = new $controllerName();

        // Endpoint registrasi hanya menerima POST, sesuai modul Acara 19.
        if ($resource === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register();
            }
            header('Allow: POST');
            Response::json(405, false, 'Method tidak diizinkan. Gunakan POST untuk registrasi.');
        }

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':    $controller->index(); break;
            case 'POST':   $controller->store(); break;
            case 'PUT':    $controller->update($_GET['id'] ?? null); break;
            case 'DELETE': $controller->destroy($_GET['id'] ?? null); break;
            default:
                header('Allow: GET, POST, PUT, DELETE');
                Response::json(405, false, 'Method tidak diizinkan.');
        }
    }
}
