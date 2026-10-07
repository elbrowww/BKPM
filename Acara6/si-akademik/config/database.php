<?php

// Sementara data disimpan di file JSON (storage/data). Nanti bisa diganti MySQL.
return [
    'driver' => 'json',
    'path'   => dirname(__DIR__) . '/storage/data',
];
