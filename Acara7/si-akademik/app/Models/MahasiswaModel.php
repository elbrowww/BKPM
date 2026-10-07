<?php

class MahasiswaModel extends Model
{
    protected string $table = 'mahasiswa';

    private const SELECT = "SELECT m.nim, m.nama, p.nama AS prodi, m.angkatan, m.status
                            FROM mahasiswa m
                            JOIN prodi p ON p.id = m.prodi_id";

    // Semua mahasiswa (beserta nama prodi) sebagai objek Mahasiswa
    public function all(): array
    {
        $rows = $this->db->query(self::SELECT . " ORDER BY m.nim")->fetchAll();

        return array_map([$this, 'toEntity'], $rows);
    }

    public function find(string $nim): ?Mahasiswa
    {
        $stmt = $this->db->prepare(self::SELECT . " WHERE m.nim = ?");
        $stmt->execute([$nim]);
        $row = $stmt->fetch();

        return $row ? $this->toEntity($row) : null;
    }

    public function insert(string $nim, string $nama, string $email, int $prodiId, int $angkatan): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nim, $nama, $email, $prodiId, $angkatan]);
    }

    public function update(string $nim, string $nama, int $prodiId): void
    {
        $stmt = $this->db->prepare("UPDATE mahasiswa SET nama = ?, prodi_id = ? WHERE nim = ?");
        $stmt->execute([$nama, $prodiId, $nim]);
    }

    public function delete(string $nim): void
    {
        $this->db->prepare("DELETE FROM mahasiswa WHERE nim = ?")->execute([$nim]);
    }

    private function toEntity(array $r): Mahasiswa
    {
        return new Mahasiswa($r['nim'], $r['nama'], $r['prodi'], (string) $r['angkatan'], $r['status'] ?? 'aktif');
    }
}
