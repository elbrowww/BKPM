<?php

// Base Model: menyediakan koneksi database dan method umum
abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Ambil semua baris dari tabel
    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY id")->fetchAll();
    }
}
