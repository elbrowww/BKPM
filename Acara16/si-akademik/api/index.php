<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';

function respond(int $code, array $body): void
{
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if (trim($raw) === '') {
        respond(400, ['status' => 'error', 'message' => 'Body request kosong']);
    }
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        respond(400, ['status' => 'error', 'message' => 'Format JSON tidak valid']);
    }
    return $data;
}

// Validasi data mahasiswa; mengembalikan array error (kosong jika valid)
function validate(array $d): array
{
    $errors = [];
    foreach (['nim', 'nama', 'email', 'jurusan'] as $f) {
        if (!array_key_exists($f, $d)) {
            $errors[$f] = "Field $f wajib ada (data JSON tidak lengkap)";
        } elseif (trim((string)$d[$f]) === '') {
            $errors[$f] = "Field $f tidak boleh kosong";
        }
    }
    if (!isset($errors['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }
    return $errors;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$segments = explode('/', trim($_GET['url'] ?? '', '/'));
if (($segments[0] ?? '') !== 'mahasiswa') {
    respond(404, ['status' => 'error', 'message' => 'Endpoint tidak ditemukan']);
}

$id = $segments[1] ?? null;
if ($id !== null && (!ctype_digit($id) || (int)$id < 1)) {
    respond(400, ['status' => 'error', 'message' => 'ID harus berupa angka positif']);
}

try {
    $db = getConnection();

    // ---------- /api/mahasiswa ----------
    if ($id === null) {
        if ($method === 'GET') {
            $rows = $db->query('SELECT * FROM mahasiswa ORDER BY id')->fetchAll();
            respond(200, ['status' => 'success', 'total' => count($rows), 'data' => $rows]);
        }

        if ($method === 'POST') {
            $data = getJsonInput();
            if ($errors = validate($data)) {
                respond(422, ['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $errors]);
            }
            $cek = $db->prepare('SELECT id FROM mahasiswa WHERE nim = ?');
            $cek->execute([$data['nim']]);
            if ($cek->fetch()) {
                respond(409, ['status' => 'error', 'message' => 'NIM sudah terdaftar']);
            }
            $stmt = $db->prepare('INSERT INTO mahasiswa (nim, nama, email, jurusan) VALUES (?, ?, ?, ?)');
            $stmt->execute([trim($data['nim']), trim($data['nama']), trim($data['email']), trim($data['jurusan'])]);
            $newId = (int)$db->lastInsertId();
            $row = $db->query("SELECT * FROM mahasiswa WHERE id = $newId")->fetch();
            header("Location: /si-akademik/api/mahasiswa/$newId");
            respond(201, ['status' => 'success', 'message' => 'Mahasiswa berhasil ditambahkan', 'data' => $row]);
        }

        respond(405, ['status' => 'error', 'message' => 'Method tidak diizinkan']);
    }

    // ---------- /api/mahasiswa/{id} ----------
    $stmt = $db->prepare('SELECT * FROM mahasiswa WHERE id = ?');
    $stmt->execute([$id]);
    $mhs = $stmt->fetch();
    if (!$mhs) {
        respond(404, ['status' => 'error', 'message' => "Mahasiswa dengan ID $id tidak ditemukan"]);
    }

    switch ($method) {
        case 'GET':
            respond(200, ['status' => 'success', 'data' => $mhs]);

        case 'PUT':
            $data = getJsonInput();
            if ($errors = validate($data)) {
                respond(422, ['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $errors]);
            }
            $cek = $db->prepare('SELECT id FROM mahasiswa WHERE nim = ? AND id <> ?');
            $cek->execute([$data['nim'], $id]);
            if ($cek->fetch()) {
                respond(409, ['status' => 'error', 'message' => 'NIM sudah dipakai mahasiswa lain']);
            }
            $upd = $db->prepare('UPDATE mahasiswa SET nim = ?, nama = ?, email = ?, jurusan = ? WHERE id = ?');
            $upd->execute([trim($data['nim']), trim($data['nama']), trim($data['email']), trim($data['jurusan']), $id]);
            $stmt->execute([$id]);
            respond(200, ['status' => 'success', 'message' => 'Mahasiswa berhasil diubah', 'data' => $stmt->fetch()]);

        case 'DELETE':
            $db->prepare('DELETE FROM mahasiswa WHERE id = ?')->execute([$id]);
            respond(200, ['status' => 'success', 'message' => "Mahasiswa ID $id berhasil dihapus"]);

        default:
            respond(405, ['status' => 'error', 'message' => 'Method tidak diizinkan']);
    }
} catch (PDOException $e) {
    respond(500, ['status' => 'error', 'message' => 'Kesalahan server database']);
}
