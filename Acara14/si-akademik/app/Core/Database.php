<?php

// Bertugas mengelola koneksi ke database menggunakan PDO (singleton)
class Database
{
    private static ?Database $instance = null;

    private PDO $pdo;

    private function __construct()
    {
        $config = require ROOT_PATH . '/config/database.php';
        $dsn    = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Detail disimpan di log; pengguna hanya melihat pesan umum
            Logger::error('Koneksi database gagal', $e);
            http_response_code(500);
            exit('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Objek PDO yang dipakai Repository
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
