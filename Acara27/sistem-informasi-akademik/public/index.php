<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Jawab preflight request agar tidak diteruskan ke Router
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Browser mengirim multipart update melalui POST + _method=PUT agar PHP mengisi $_FILES.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
    $_SERVER['REQUEST_METHOD'] = 'PUT';
}

require_once __DIR__ . '/../app/core/autoload.php';
(new Router())->dispatch();
