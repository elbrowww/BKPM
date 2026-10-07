<?php

// Base Controller: helper umum untuk semua controller
abstract class Controller
{
    // Tampilkan view. $layout = null berarti view berdiri sendiri (mis. halaman login)
    protected function view(string $name, array $data = [], ?string $layout = 'main'): void
    {
        extract($data);

        $viewFile = APP_PATH . '/Views/' . $name . '.php';

        if ($layout === null) {
            require $viewFile;
            return;
        }

        $view = $viewFile;
        require APP_PATH . '/Views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
