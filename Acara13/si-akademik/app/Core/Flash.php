<?php

// Flash Message berbasis $_SESSION: disimpan sekali, ditampilkan sekali, lalu dihapus.
class Flash
{
    private const KEY = 'flash';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::KEY] = ['type' => $type, 'message' => $message];
    }

    // Ambil pesan lalu langsung hapus dari session
    public static function pull(): ?array
    {
        $flash = $_SESSION[self::KEY] ?? null;
        unset($_SESSION[self::KEY]);

        return $flash;
    }
}