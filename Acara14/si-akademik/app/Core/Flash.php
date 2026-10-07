<?php

// Flash Message berbasis $_SESSION: disimpan sekali, ditampilkan sekali, lalu dihapus.
// Juga menyimpan input lama + daftar error validasi agar PRG tetap berjalan saat validasi gagal.
class Flash
{
    private const KEY   = 'flash';
    private const INPUT = 'flash_input';

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

    // Simpan isian form + error validasi untuk request GET berikutnya (setelah redirect)
    public static function withInput(array $old, array $errors): void
    {
        $_SESSION[self::INPUT] = ['old' => $old, 'errors' => $errors];
    }

    // Ambil isian lama + error, lalu hapus dari session
    public static function pullInput(): array
    {
        $data = $_SESSION[self::INPUT] ?? ['old' => [], 'errors' => []];
        unset($_SESSION[self::INPUT]);

        return $data;
    }
}
