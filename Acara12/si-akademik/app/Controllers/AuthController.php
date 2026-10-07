<?php

class AuthController extends BaseController
{
    // Menampilkan form login
    public function loginForm()
    {
        // Jika sudah login, langsung ke dashboard
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', ['title' => 'Login'], null);
    }

    // Memproses login (sementara: username/password di-hardcode)
    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === 'admin' && $password === '12345') {

            session_regenerate_id(true);

            $_SESSION['login']    = true;
            $_SESSION['username'] = 'admin';
            $this->flash('success', 'Selamat datang, Admin!');

            $this->redirect('/dashboard');
        }

        $this->flash('danger', 'Username atau password salah.');
        $this->redirect('/login');
    }

    // Logout
    public function logout()
    {
        // Kosongkan session, lalu beri pesan flash untuk halaman login
        $_SESSION = [];
        session_regenerate_id(true);

        $this->flash('info', 'Anda telah logout.');
        $this->redirect('/login');
    }
}
