<?php

// Daftar route: $routes[METHOD][URI] = [NamaController, method]
// {id} pada URI akan dikirim sebagai parameter ke method controller.

$routes = [
    'GET' => [
        '/'                  => ['HomeController', 'index'],
        '/mahasiswa'         => ['MahasiswaController', 'index'],
        '/mahasiswa/create'  => ['MahasiswaController', 'create'],
        '/mahasiswa/{id}'    => ['MahasiswaController', 'show'],
    ],
];

return $routes;
