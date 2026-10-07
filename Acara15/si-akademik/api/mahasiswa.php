<?php

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

/*
|--------------------------------------------------------------------------
| GET
|--------------------------------------------------------------------------
| GET /api/mahasiswa.php
| GET /api/mahasiswa.php?id=1
|--------------------------------------------------------------------------
*/

if ($method === 'GET') {

    // Jika ada parameter id
    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        try {
            $stmt = $pdo->prepare("
                SELECT id, nim, nama, email
                FROM mahasiswa
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id
            ]);

            $data = $stmt->fetch();

            if (!$data) {
                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan',
                    'data' => null
                ]);

                exit;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diambil',
                'data' => $data
            ]);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil data mahasiswa',
                'error' => $e->getMessage()
            ]);
        }

        exit;
    }

    // Jika tidak ada id, ambil semua data
    try {

        $stmt = $pdo->query("
            SELECT id, nim, nama, email
            FROM mahasiswa
            ORDER BY id ASC
        ");

        $data = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $data
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data mahasiswa',
            'error' => $e->getMessage()
        ]);
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| POST
|--------------------------------------------------------------------------
| POST /api/mahasiswa.php
|
| JSON:
| {
|     "nim": "23004",
|     "nama": "Dewi",
|     "email": "dewi@gmail.com"
| }
|--------------------------------------------------------------------------
*/

if ($method === 'POST') {

    // Ambil JSON dari request body
    $input = json_decode(file_get_contents('php://input'), true);

    // Pastikan JSON valid
    if (!is_array($input)) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Format JSON tidak valid'
        ]);

        exit;
    }

    // Ambil data
    $nim   = trim($input['nim'] ?? '');
    $nama  = trim($input['nama'] ?? '');
    $email = trim($input['email'] ?? '');

    // Validasi
    if ($nim === '' || $nama === '' || $email === '') {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'NIM, nama, dan email wajib diisi'
        ]);

        exit;
    }

    try {

        // Cek NIM sudah ada atau belum
        $cek = $pdo->prepare("
            SELECT id
            FROM mahasiswa
            WHERE nim = :nim
        ");

        $cek->execute([
            ':nim' => $nim
        ]);

        if ($cek->fetch()) {

            http_response_code(409);

            echo json_encode([
                'success' => false,
                'message' => 'NIM sudah terdaftar'
            ]);

            exit;
        }

        // Insert data
        $stmt = $pdo->prepare("
            INSERT INTO mahasiswa (nim, nama, email)
            VALUES (:nim, :nama, :email)
        ");

        $stmt->execute([
            ':nim'   => $nim,
            ':nama'  => $nama,
            ':email' => $email
        ]);

        $id = $pdo->lastInsertId();

        // Ambil kembali data yang baru dibuat
        $stmt = $pdo->prepare("
            SELECT id, nim, nama, email
            FROM mahasiswa
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $data = $stmt->fetch();

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => $data
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan data mahasiswa',
            'error' => $e->getMessage()
        ]);
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| Method tidak didukung
|--------------------------------------------------------------------------
*/

http_response_code(405);

echo json_encode([
    'success' => false,
    'message' => 'Method tidak didukung'
]);