<?php
class Request {
    /** Ambil body JSON sebagai array (kosong jika tidak valid). */
    public static function json(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    /** Ambil data form biasa atau multipart/form-data. */
    public static function input(): array {
        if (!empty($_POST)) return $_POST;
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'PUT') {
            $raw = file_get_contents('php://input');
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/x-www-form-urlencoded') === 0) {
                parse_str($raw, $data);
                return is_array($data) ? $data : [];
            }
        }
        return self::json();
    }
}
