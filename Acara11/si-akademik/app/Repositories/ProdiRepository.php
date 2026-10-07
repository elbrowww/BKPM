<?php

class ProdiRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->pdo->query("SELECT * FROM prodi ORDER BY kode")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM prodi WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Cek kode sudah dipakai (abaikan baris $exceptId saat edit)
    public function kodeExists(string $kode, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM prodi WHERE kode = :kode AND id <> :id");
        $stmt->execute(['kode' => $kode, 'id' => $exceptId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $kode, string $nama): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)");
        $stmt->execute(['kode' => $kode, 'nama' => $nama]);
    }

    public function update(int $id, string $kode, string $nama): void
    {
        $stmt = $this->pdo->prepare("UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id");
        $stmt->execute(['kode' => $kode, 'nama' => $nama, 'id' => $id]);
    }

    // Return false bila prodi masih dipakai mahasiswa/mata kuliah (foreign key RESTRICT)
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM prodi WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
