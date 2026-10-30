<?php
class Request {
    /** Ambil body JSON sebagai array (kosong jika tidak valid). */
    public static function json(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }
}
