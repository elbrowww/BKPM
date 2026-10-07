<?php

// BaseRepository: menyimpan akses PDO yang dibutuhkan semua Repository.
// Database di-inject lewat constructor (Dependency Injection).
abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getConnection();
    }
}
