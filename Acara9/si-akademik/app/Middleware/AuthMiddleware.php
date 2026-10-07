<?php

class AuthMiddleware
{
    // Dijalankan sebelum Controller: jika belum login, arahkan ke /login
    public static function handle()
    {
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
