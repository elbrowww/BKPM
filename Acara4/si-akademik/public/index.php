<?php

// index.php (sementara) - menyiapkan data lalu mengirimkannya ke View.
// Akan dipindahkan ke Controller di pertemuan berikutnya.

require_once __DIR__ . '/../app/Models/Mahasiswa.php';

$halaman = $_GET['halaman'] ?? 'index';

if ($halaman === 'create') {
    $title = 'Tambah Mahasiswa';
    $view  = __DIR__ . '/../app/Views/mahasiswa/create.php';
} else {
    $title = 'Data Mahasiswa';
    $view  = __DIR__ . '/../app/Views/mahasiswa/index.php';

    $mahasiswa = [
        new Mahasiswa('22010001', 'Budi Santoso',  'Teknik Informatika'),
        new Mahasiswa('23010002', 'Siti Aminah',   'Manajemen Informatika'),
        new Mahasiswa('23010003', 'Agus Prasetyo', 'Teknik Komputer'),
        new Mahasiswa('24010004', 'Dewi Lestari',  'Teknik Informatika'),
    ];
}

require __DIR__ . '/../app/Views/layouts/main.php';
