<?php
class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query('SELECT * FROM mahasiswa ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Cek NIM sudah dipakai. $excludeId dipakai saat update agar data sendiri tidak dihitung. */
    public function nimExists(string $nim, ?int $excludeId = null): bool {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $params = [':nim' => $nim];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): string {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO mahasiswa (nim, nama, prodi) VALUES (:nim, :nama, :prodi)'
            );
            $stmt->execute([':nim' => $data['nim'], ':nama' => $data['nama'], ':prodi' => $data['prodi']]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Mahasiswa create error: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server.');
        }
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            'UPDATE mahasiswa SET nim = :nim, nama = :nama, prodi = :prodi WHERE id = :id'
        );
        $stmt->execute([':nim' => $data['nim'], ':nama' => $data['nama'], ':prodi' => $data['prodi'], ':id' => $id]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
