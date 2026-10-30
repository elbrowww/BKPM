<?php
class Mahasiswa {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO mahasiswa (nim, nama, prodi, foto) VALUES (:nim, :nama, :prodi, :foto)'
        );
        $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':prodi' => $data['prodi'],
            ':foto' => $data['foto'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE mahasiswa SET nim = :nim, nama = :nama, prodi = :prodi, foto = :foto WHERE id = :id'
        );
        $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':prodi' => $data['prodi'],
            ':foto' => $data['foto'] ?? null,
            ':id' => $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
