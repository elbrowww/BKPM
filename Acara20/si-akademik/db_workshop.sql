CREATE DATABASE IF NOT EXISTS db_workshop;
USE db_workshop;

CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(15) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    prodi VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data contoh (opsional, untuk pengujian)
INSERT INTO mahasiswa (nim, nama, prodi) VALUES
('2024001', 'Budi Santoso', 'Teknik Informatika'),
('2024002', 'Siti Aminah', 'Sistem Informasi');


-- Tabel users untuk Acara 19: registrasi user
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
