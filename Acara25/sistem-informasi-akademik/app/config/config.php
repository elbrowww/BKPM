<?php
// Sesuaikan dengan MySQL Laragon Anda (default: root tanpa password)
return [
    'db_host' => 'localhost',
    'db_name' => 'si_akademik24',
    'db_user' => 'root',
    'db_pass' => '',
    // Kunci rahasia untuk menandatangani token. Ganti di produksi.
    'secret'  => 'ganti-dengan-kunci-rahasia-anda',
    'token_ttl' => 7200, // 2 jam
];
