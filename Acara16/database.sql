CREATE DATABASE IF NOT EXISTS si_akademik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE si_akademik;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO mahasiswa (nim, nama, email, jurusan) VALUES
('2024001', 'Budi Santoso', 'budi@mail.com', 'Teknik Informatika'),
('2024002', 'Siti Aminah', 'siti@mail.com', 'Sistem Informasi'),
('2024003', 'Andi Pratama', 'andi@mail.com', 'Teknik Informatika');
