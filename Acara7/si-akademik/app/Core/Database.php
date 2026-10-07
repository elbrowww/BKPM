<?php

// Membuat satu koneksi PDO yang dipakai bersama oleh semua Model
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $c   = require ROOT_PATH . '/config/database.php';
            $dsn = "mysql:host={$c['host']};dbname={$c['database']};charset={$c['charset']}";

            try {
                self::$pdo = new PDO($dsn, $c['username'], $c['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Koneksi database gagal. Periksa config/database.php dan pastikan MySQL menyala.');
            }
        }

        return self::$pdo;
    }
}
