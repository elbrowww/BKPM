<?php

// Daftar route: $routes[METHOD][URI] = [NamaController, method, 'auth']
// - {id} pada URI dikirim sebagai parameter ke method controller.
// - Elemen ketiga 'auth' (opsional) berarti route dilindungi AuthMiddleware.

$routes = [
    'GET' => [
        '/'                  => ['HomeController', 'index'],

        // Auth
        '/login'             => ['AuthController', 'loginForm'],
        '/logout'            => ['AuthController', 'logout'],

        // Dashboard (wajib login)
        '/dashboard'         => ['DashboardController', 'index', 'auth'],

        // Mahasiswa (wajib login). Pencarian: /mahasiswa?q=kata
        '/mahasiswa'         => ['MahasiswaController', 'index', 'auth'],
        '/mahasiswa/create'  => ['MahasiswaController', 'create', 'auth'],
        '/mahasiswa/edit'    => ['MahasiswaController', 'edit', 'auth'],
        '/mahasiswa/delete'  => ['MahasiswaController', 'delete', 'auth'],
        '/mahasiswa/{id}'    => ['MahasiswaController', 'show', 'auth'],

        // Program Studi (wajib login)
        '/prodi'             => ['ProdiController', 'index', 'auth'],
        '/prodi/create'      => ['ProdiController', 'create', 'auth'],
        '/prodi/edit'        => ['ProdiController', 'edit', 'auth'],

        // Mata Kuliah (wajib login)
        '/matakuliah'        => ['MatakuliahController', 'index', 'auth'],
        '/matakuliah/create' => ['MatakuliahController', 'create', 'auth'],
        '/matakuliah/edit'   => ['MatakuliahController', 'edit', 'auth'],
    ],

    'POST' => [
        '/login'             => ['AuthController', 'login'],

        // Mahasiswa
        '/mahasiswa'         => ['MahasiswaController', 'store', 'auth'],
        '/mahasiswa/update'  => ['MahasiswaController', 'update', 'auth'],

        // Program Studi (hapus lewat POST)
        '/prodi'             => ['ProdiController', 'store', 'auth'],
        '/prodi/update'      => ['ProdiController', 'update', 'auth'],
        '/prodi/delete'      => ['ProdiController', 'delete', 'auth'],

        // Mata Kuliah (hapus lewat POST)
        '/matakuliah'        => ['MatakuliahController', 'store', 'auth'],
        '/matakuliah/update' => ['MatakuliahController', 'update', 'auth'],
        '/matakuliah/delete' => ['MatakuliahController', 'delete', 'auth'],
    ],
];

return $routes;
