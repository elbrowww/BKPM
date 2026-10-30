<?php
class Database {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (self::$pdo === null) {
            $cfg = require __DIR__ . '/../config/config.php';
            try {
                $pdo = new PDO(
                    "mysql:host={$cfg['db_host']};charset=utf8mb4",
                    $cfg['db_user'], $cfg['db_pass'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );
                $dbName = str_replace('`', '', $cfg['db_name']);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$dbName}`");
                self::migrate($pdo);
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                Response::json(500, false, 'Koneksi database gagal: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(60) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin','user') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS mahasiswa (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nim VARCHAR(30) NOT NULL UNIQUE,
            nama VARCHAR(120) NOT NULL,
            prodi VARCHAR(120) NOT NULL,
            foto VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Migrasi database Acara 25: tambahkan kolom foto tanpa menghapus data lama.
        $kolomFoto = $pdo->query("SHOW COLUMNS FROM mahasiswa LIKE 'foto'")->fetch();
        if (!$kolomFoto) {
            $pdo->exec('ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi');
        }

        // Akun awal dibuat hanya jika belum ada. Password disimpan dalam bentuk hash.
        $seed = [
            ['admin', 'admin123', 'admin'],
            ['user', 'user123', 'user'],
        ];
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $insert = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        foreach ($seed as [$username, $password, $role]) {
            $check->execute([$username]);
            if (!$check->fetch()) {
                $insert->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);
            }
        }

        $count = (int)$pdo->query('SELECT COUNT(*) FROM mahasiswa')->fetchColumn();
        if ($count === 0) {
            $sample = $pdo->prepare('INSERT INTO mahasiswa (nim, nama, prodi) VALUES (?, ?, ?)');
            foreach ([
                ['220100101', 'Budi Santoso', 'Teknik Informatika'],
                ['220100102', 'Siti Aminah', 'Sistem Informasi'],
                ['220100103', 'Andi Saputra', 'Teknik Informatika'],
            ] as $row) $sample->execute($row);
        }
    }
}
