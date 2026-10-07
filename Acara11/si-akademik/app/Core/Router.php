<?php

// Router: mencocokkan URI + method ke Controller, dan menjalankan middleware
class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(string $uri, string $method): void
    {
        $handler = null;
        $params  = [];

        // 1. Route biasa (cocok persis)
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
        } else {
            // 2. Route dengan parameter, contoh: /mahasiswa/5
            foreach ($this->routes[$method] ?? [] as $pattern => $target) {
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
            return;
        }

        [$controllerName, $action] = $handler;

        // Route bertanda 'auth' harus login dulu (dicek sebelum Controller dijalankan)
        if (($handler[2] ?? null) === 'auth') {
            AuthMiddleware::handle();
        }

        $controller = Container::make($controllerName);
        $controller->$action(...$params);
    }
}
