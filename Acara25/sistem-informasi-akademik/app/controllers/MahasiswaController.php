<?php
class MahasiswaController {
    // Publik: tanpa login (dipakai views/index.html di Acara 24)
    public function index(): void {
        $rows = Database::get()->query('SELECT id, nim, nama, prodi FROM mahasiswa ORDER BY nim')->fetchAll();
        Response::json(200, true, 'Data mahasiswa', $rows);
    }

    public function store(): void {
        Auth::requireAdmin();   // Acara 25: sebelumnya Auth::requireAuth()
        [$nim, $nama, $prodi] = $this->validasi();
        try {
            $st = Database::get()->prepare('INSERT INTO mahasiswa (nim, nama, prodi) VALUES (?, ?, ?)');
            $st->execute([$nim, $nama, $prodi]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') Response::json(409, false, 'NIM sudah terdaftar');
            throw $e;
        }
        Response::json(201, true, 'Mahasiswa ditambahkan', ['id' => (int)Database::get()->lastInsertId()]);
    }

    public function update($id): void {
        Auth::requireAdmin();   // Acara 25
        [$nim, $nama, $prodi] = $this->validasi();
        try {
            $st = Database::get()->prepare('UPDATE mahasiswa SET nim = ?, nama = ?, prodi = ? WHERE id = ?');
            $st->execute([$nim, $nama, $prodi, $id]);
            if ($st->rowCount() === 0) {
                $cek = Database::get()->prepare('SELECT id FROM mahasiswa WHERE id = ?');
                $cek->execute([$id]);
                if (!$cek->fetch()) Response::json(404, false, 'Data tidak ditemukan');
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') Response::json(409, false, 'NIM sudah dipakai mahasiswa lain');
            throw $e;
        }
        Response::json(200, true, 'Mahasiswa diperbarui');
    }

    public function destroy($id): void {
        Auth::requireAdmin();   // Acara 25
        $st = Database::get()->prepare('DELETE FROM mahasiswa WHERE id = ?');
        $st->execute([$id]);
        if ($st->rowCount() === 0) Response::json(404, false, 'Data tidak ditemukan');
        Response::json(200, true, 'Mahasiswa dihapus');
    }

    private function validasi(): array {
        $in = Request::json();
        $nim = trim($in['nim'] ?? '');
        $nama = trim($in['nama'] ?? '');
        $prodi = trim($in['prodi'] ?? '');
        if ($nim === '' || $nama === '' || $prodi === '') {
            Response::json(422, false, 'NIM, nama, dan prodi wajib diisi');
        }
        return [$nim, $nama, $prodi];
    }
}
