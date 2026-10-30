<?php
// Autoloader sederhana: cari class di core/, controllers/
spl_autoload_register(function (string $class) {
    $dirs = [__DIR__, __DIR__ . '/../controllers'];
    foreach ($dirs as $dir) {
        $file = $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
