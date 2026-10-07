<?php

class MatakuliahRepository
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getConnection();
    }

    // JOIN ke prodi supaya daftar menampilkan nama prodi
    public function all(): array
    {
        return $this->pdo->query(
            "SELECT mk.id, mk.kode, mk.nama, mk.sks, p.nama AS prodi
             FROM matakuliah mk
             JOIN prodi p ON p.id = mk.prodi_id
             ORDER BY mk.kode"
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function kodeExists(string $kode, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM matakuliah WHERE kode = :kode AND id <> :id");
        $stmt->execute(['kode' => $kode, 'id' => $exceptId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $d): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO matakuliah (kode, nama, sks, prodi_id) VALUES (:kode, :nama, :sks, :prodi_id)"
        );
        $stmt->execute(['kode' => $d['kode'], 'nama' => $d['nama'], 'sks' => $d['sks'], 'prodi_id' => $d['prodi_id']]);
    }

    public function update(int $id, array $d): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id"
        );
        $stmt->execute(['kode' => $d['kode'], 'nama' => $d['nama'], 'sks' => $d['sks'], 'prodi_id' => $d['prodi_id'], 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
