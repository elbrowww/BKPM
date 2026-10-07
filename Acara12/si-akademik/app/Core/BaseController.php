<?php

// BaseController: method bersama untuk semua controller (view, redirect, flash).
// Controller lain cukup "extends BaseController" tanpa copy-paste.
abstract class BaseController
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

    // Arahkan ke URL lain (relatif terhadap BASE_URL) lalu hentikan eksekusi
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    // Simpan pesan flash untuk ditampilkan satu kali pada request berikutnya
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
