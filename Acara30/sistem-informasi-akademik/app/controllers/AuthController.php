<?php
class AuthController {
    public function login(): void {
        $in = Request::json();
        $username = trim($in['username'] ?? '');
        $password = $in['password'] ?? '';
        if ($username === '' || $password === '') {
            Response::json(422, false, 'Username dan password wajib diisi');
        }

        $st = Database::get()->prepare('SELECT * FROM users WHERE username = ?');
        $st->execute([$username]);
        $user = $st->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            Response::json(401, false, 'Username atau password salah');
        }

        $token = Auth::generateToken([
            'sub' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ]);
        Response::json(200, true, 'Login berhasil', [
            'token' => $token,
            'role' => $user['role'],
            'username' => $user['username'],
        ]);
    }

    public function register(): void {
        $in = Request::json();
        $username = trim($in['username'] ?? '');
        $password = $in['password'] ?? '';
        if ($username === '' || strlen($password) < 6) {
            Response::json(422, false, 'Username wajib diisi dan password minimal 6 karakter');
        }
        try {
            // Registrasi publik selalu role "user"; admin hanya dibuat lewat database
            $st = Database::get()->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, "user")');
            $st->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'Username sudah dipakai');
            }
            throw $e;
        }
        Response::json(201, true, 'Registrasi berhasil');
    }
}
