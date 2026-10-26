<?php
class AuthController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function register(): void {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body harus berisi JSON yang valid.');
        }

        $username = trim((string)($data['username'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($username === '' || $email === '' || $password === '') {
            Response::json(400, false, 'Username, email, dan password wajib diisi.');
        }
        if (strlen($username) > 50) {
            Response::json(422, false, 'Username maksimal 50 karakter.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            Response::json(422, false, 'Format email tidak valid atau melebihi 100 karakter.');
        }
        if (strlen($password) < 8) {
            Response::json(422, false, 'Password minimal 8 karakter.');
        }
        if ($this->model->findByUsername($username)) {
            Response::json(422, false, 'Username sudah digunakan.');
        }

        try {
            $id = $this->model->create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            Response::json(201, true, 'Registrasi berhasil.', ['id' => $id, 'username' => $username]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(422, false, 'Username sudah digunakan.');
            }
            error_log('Register error: ' . $e->getMessage());
            Response::json(500, false, 'Registrasi gagal karena kesalahan database.');
        }
    }


    public function login(): void {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body harus berisi JSON yang valid.');
        }
        $username = trim((string)($data['username'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($username === '' || $password === '') {
            Response::json(400, false, 'Username dan password wajib diisi.');
        }
        $user = $this->model->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            Response::json(401, false, 'Username atau password salah.');
        }
        $token = Auth::generateToken([
            'id' => (int)$user['id'],
            'role' => $user['role'] ?? 'user',
            'username' => $user['username'],
        ]);
        Response::json(200, true, 'Login berhasil.', [
            'token' => $token,
            'role' => $user['role'] ?? 'user',
            'expires_in' => 300,
        ]);
    }
}
