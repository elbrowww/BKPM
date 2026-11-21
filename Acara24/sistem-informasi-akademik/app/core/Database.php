<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $cfg = require __DIR__ . '/../config/config.php';
            try {
                // Koneksi tanpa nama database dulu, supaya database bisa dibuat otomatis
                $pdo = new PDO(
                    "mysql:host={$cfg['db_host']};charset=utf8mb4",
                    $cfg['db_user'],
                    $cfg['db_pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );

                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$cfg['db_name']}`");

                self::migrate($pdo);
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                Response::json(500, false, 'Koneksi database gagal: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        // Contoh struktur tabel; sesuaikan dengan skema database proyek kamu
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS mahasiswa (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nim VARCHAR(30) NOT NULL UNIQUE,
                nama VARCHAR(100) NOT NULL,
                prodi VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}