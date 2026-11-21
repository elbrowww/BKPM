<?php
// Semua nilai sensitif dibaca dari environment variable; nilai default hanya untuk development lokal.
// Produksi: set DB_PASS, APP_SECRET, SEED_ADMIN_PASS, SEED_USER_PASS (atau kosongkan seed lalu buat akun manual).
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DB_NAME') ?: 'si_akademik27',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    // Kunci rahasia untuk menandatangani token. WAJIB diganti di produksi (APP_SECRET).
    'secret'  => getenv('APP_SECRET') ?: 'ganti-dengan-kunci-rahasia-anda',
    'token_ttl' => 7200, // 2 jam
    // Akun awal (seed). Password dibaca dari environment; default hanya untuk development.
    'seed_users' => [
        ['admin', getenv('SEED_ADMIN_PASS') ?: 'admin123', 'admin'],
        ['user',  getenv('SEED_USER_PASS')  ?: 'user123',  'user'],
    ],
];
