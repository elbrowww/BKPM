<?php

// Operasi CRUD data mahasiswa. Mewarisi BaseRepository (akses PDO).
// Semua query SQL hanya ada di sini dan SELALU memakai Prepared Statement (aman dari SQL Injection).
class MahasiswaRepository extends BaseRepository
{
    // JOIN ke prodi supaya daftar menampilkan nama prodi
    private const SELECT = "SELECT m.id, m.nim, m.nama, m.email, m.prodi_id, p.nama AS prodi, m.angkatan, m.status
                            FROM mahasiswa m
                            JOIN prodi p ON p.id = m.prodi_id";

    /** @return Mahasiswa[] */
    public function all(): array
    {
        $stmt = $this->pdo->prepare(self::SELECT . " ORDER BY m.nim");
        $stmt->execute();

        return array_map([Mahasiswa::class, 'fromRow'], $stmt->fetchAll());
    }

    /** @return Mahasiswa[] pencarian nama/NIM dengan LIKE + prepared statement */
    public function search(string $keyword = ''): array
    {
        if ($keyword === '') {
            return $this->all();
        }

        $stmt = $this->pdo->prepare(
            self::SELECT . " WHERE m.nama LIKE :q_nama OR m.nim LIKE :q_nim ORDER BY m.nim"
        );
        // escape wildcard milik user agar dianggap teks biasa
        $like = '%' . addcslashes($keyword, '%_\\') . '%';
        $stmt->execute(['q_nama' => $like, 'q_nim' => $like]);

        return array_map([Mahasiswa::class, 'fromRow'], $stmt->fetchAll());
    }

    public function find(int $id): ?Mahasiswa
    {
        $stmt = $this->pdo->prepare(self::SELECT . " WHERE m.id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Mahasiswa::fromRow($row) : null;
    }

    // Cek NIM sudah dipakai (abaikan baris $exceptId saat edit)
    public function existsByNim(string $nim, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim AND id <> :id");
        $stmt->execute(['nim' => $nim, 'id' => $exceptId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(Mahasiswa $m): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mahasiswa_x (nim, nama, email, prodi_id, angkatan, status)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan, :status)"
        );
        $stmt->execute([
            'nim'      => $m->getNim(),
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'prodi_id' => $m->getProdiId(),
            'angkatan' => $m->getAngkatan(),
            'status'   => $m->getStatus(),
        ]);
    }

    public function update(Mahasiswa $m): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan, status = :status
             WHERE id = :id"
        );
        $stmt->execute([
            'nim'      => $m->getNim(),
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'prodi_id' => $m->getProdiId(),
            'angkatan' => $m->getAngkatan(),
            'status'   => $m->getStatus(),
            'id'       => $m->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
