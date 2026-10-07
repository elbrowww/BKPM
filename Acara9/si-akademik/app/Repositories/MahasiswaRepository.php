<?php

// Operasi CRUD data mahasiswa. Dependency (Database) di-inject lewat constructor.
class MahasiswaRepository
{
    private PDO $pdo;

    // JOIN ke prodi supaya daftar menampilkan nama prodi
    private const SELECT = "SELECT m.nim, m.nama, m.email, m.prodi_id, p.nama AS prodi, m.angkatan, m.status
                            FROM mahasiswa m
                            JOIN prodi p ON p.id = m.prodi_id";

    public function __construct(Database $database)
    {
        $this->pdo = $database->getConnection();
    }

    /** @return Mahasiswa[] */
    public function all(): array
    {
        return $this->search('');
    }

    /** @return Mahasiswa[] pencarian nama/NIM dengan LIKE + prepared statement */
    public function search(string $keyword = ''): array
    {
        if ($keyword === '') {
            $stmt = $this->pdo->query(self::SELECT . " ORDER BY m.nim");
        } else {
            $stmt = $this->pdo->prepare(
                self::SELECT . " WHERE m.nama LIKE :q_nama OR m.nim LIKE :q_nim ORDER BY m.nim"
            );
            // escape wildcard milik user agar dianggap teks biasa
            $like = '%' . addcslashes($keyword, '%_\\') . '%';
            $stmt->execute(['q_nama' => $like, 'q_nim' => $like]);
        }

        return array_map([Mahasiswa::class, 'fromRow'], $stmt->fetchAll());
    }

    public function find(string $nim): ?Mahasiswa
    {
        $stmt = $this->pdo->prepare(self::SELECT . " WHERE m.nim = :nim");
        $stmt->execute(['nim' => $nim]);
        $row = $stmt->fetch();

        return $row ? Mahasiswa::fromRow($row) : null;
    }

    public function create(Mahasiswa $m): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan, status)
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
             SET nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan, status = :status
             WHERE nim = :nim"
        );
        $stmt->execute([
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'prodi_id' => $m->getProdiId(),
            'angkatan' => $m->getAngkatan(),
            'status'   => $m->getStatus(),
            'nim'      => $m->getNim(),
        ]);
    }

    public function delete(string $nim): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE nim = :nim");
        $stmt->execute(['nim' => $nim]);
    }
}
