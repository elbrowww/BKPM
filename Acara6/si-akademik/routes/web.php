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

        // Mahasiswa (wajib login)
        '/mahasiswa'         => ['MahasiswaController', 'index', 'auth'],
        '/mahasiswa/create'  => ['MahasiswaController', 'create', 'auth'],
        '/mahasiswa/edit'    => ['MahasiswaController', 'edit', 'auth'],
        '/mahasiswa/delete'  => ['MahasiswaController', 'delete', 'auth'],
        '/mahasiswa/{id}'    => ['MahasiswaController', 'show', 'auth'],
    ],

    'POST' => [
        '/login'             => ['AuthController', 'login'],

        // Mahasiswa (wajib login)
        '/mahasiswa'         => ['MahasiswaController', 'store', 'auth'],
        '/mahasiswa/update'  => ['MahasiswaController', 'update', 'auth'],
    ],
];

return $routes;
