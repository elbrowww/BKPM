<?php

class MahasiswaModel extends Model
{
    protected string $table = 'mahasiswa';

    // JOIN ke prodi supaya daftar menampilkan nama prodi
    private const SELECT = "SELECT m.nim, m.nama, p.nama AS prodi, m.angkatan, m.status
                            FROM mahasiswa m
                            JOIN prodi p ON p.id = m.prodi_id";

    public function all(): array
    {
        return $this->search('');
    }

    // Pencarian nama/NIM dengan LIKE + prepared statement (tugas mandiri)
    public function search(string $keyword = ''): array
    {
        if ($keyword === '') {
            $stmt = $this->db->query(self::SELECT . " ORDER BY m.nim");
        } else {
            $stmt = $this->db->prepare(
                self::SELECT . " WHERE m.nama LIKE :q_nama OR m.nim LIKE :q_nim ORDER BY m.nim"
            );
            // escape karakter wildcard milik user agar dianggap teks biasa
            $like = '%' . addcslashes($keyword, '%_\\') . '%';
            $stmt->execute(['q_nama' => $like, 'q_nim' => $like]);
        }

        return array_map(
            fn($r) => new Mahasiswa($r['nim'], $r['nama'], $r['prodi'], (string) $r['angkatan'], $r['status']),
            $stmt->fetchAll()
        );
    }

    // Satu baris mentah (untuk form edit): nim, nama, email, prodi_id, angkatan, status
    public function find(string $nim): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE nim = :nim");
        $stmt->execute(['nim' => $nim]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $d): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan, status)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan, :status)"
        );
        $stmt->execute([
            'nim' => $d['nim'], 'nama' => $d['nama'], 'email' => $d['email'],
            'prodi_id' => $d['prodi_id'], 'angkatan' => $d['angkatan'], 'status' => $d['status'],
        ]);
    }

    public function update(string $nim, array $d): void
    {
        $stmt = $this->db->prepare(
            "UPDATE mahasiswa
             SET nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan, status = :status
             WHERE nim = :nim"
        );
        $stmt->execute([
            'nama' => $d['nama'], 'email' => $d['email'], 'prodi_id' => $d['prodi_id'],
            'angkatan' => $d['angkatan'], 'status' => $d['status'], 'nim' => $nim,
        ]);
    }

    public function delete(string $nim): void
    {
        $stmt = $this->db->prepare("DELETE FROM mahasiswa WHERE nim = :nim");
        $stmt->execute(['nim' => $nim]);
    }
}
