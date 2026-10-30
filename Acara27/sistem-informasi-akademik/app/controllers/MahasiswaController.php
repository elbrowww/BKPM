<?php
class MahasiswaController {
    private function model(): Mahasiswa {
        return new Mahasiswa(Database::get());
    }

    public function index(): void {
        // Acara 27: GET /mahasiswa?id=N mengembalikan satu data (dipakai form_edit.html).
        if (isset($_GET['id'])) {
            $row = $this->model()->find((int)$_GET['id']);
            if (!$row) Response::json(404, false, 'Data tidak ditemukan');
            Response::json(200, true, 'Data mahasiswa', [
                'id' => $row['id'], 'nim' => $row['nim'], 'nama' => $row['nama'],
                'prodi' => $row['prodi'], 'foto' => $row['foto'],
            ]);
        }
        $rows = Database::get()->query('SELECT id, nim, nama, prodi, foto FROM mahasiswa ORDER BY nim')->fetchAll();
        Response::json(200, true, 'Data mahasiswa', $rows);
    }

    public function store(): void {
        Auth::requireAdmin();
        $in = Request::input();
        $nim = trim($in['nim'] ?? '');
        $nama = trim($in['nama'] ?? '');
        $prodi = trim($in['prodi'] ?? '');
        if ($nim === '' || $nama === '' || $prodi === '') {
            Response::json(422, false, 'NIM, nama, dan prodi wajib diisi');
        }

        $foto = $this->simpanFoto($_FILES['foto'] ?? null);
        try {
            $id = $this->model()->create(compact('nim', 'nama', 'prodi', 'foto'));
        } catch (PDOException $e) {
            $this->hapusFoto($foto);
            if ($e->getCode() === '23000') Response::json(409, false, 'NIM sudah terdaftar');
            throw $e;
        }
        Response::json(201, true, 'Mahasiswa ditambahkan', ['id' => $id, 'foto' => $foto]);
    }

    public function update($id): void {
        Auth::requireAdmin();
        $id = (int)$id;
        $lama = $this->model()->find($id);
        if (!$lama) Response::json(404, false, 'Data tidak ditemukan');

        $in = Request::input();
        $nim = trim($in['nim'] ?? '');
        $nama = trim($in['nama'] ?? '');
        $prodi = trim($in['prodi'] ?? '');
        if ($nim === '' || $nama === '' || $prodi === '') {
            Response::json(422, false, 'NIM, nama, dan prodi wajib diisi');
        }

        // Jika tidak ada foto baru, pertahankan foto lama.
        $fotoBaru = null;
        if (isset($_FILES['foto']) && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $fotoBaru = $this->simpanFoto($_FILES['foto']);
        }
        $fotoSimpan = $fotoBaru ?? ($lama['foto'] ?? null);
        try {
            $this->model()->update($id, compact('nim', 'nama', 'prodi') + ['foto' => $fotoSimpan]);
        } catch (PDOException $e) {
            $this->hapusFoto($fotoBaru);
            if ($e->getCode() === '23000') Response::json(409, false, 'NIM sudah dipakai mahasiswa lain');
            throw $e;
        }

        // Hapus foto lama hanya setelah data baru berhasil disimpan.
        if ($fotoBaru !== null && !empty($lama['foto'])) $this->hapusFoto($lama['foto']);
        Response::json(200, true, 'Mahasiswa diperbarui', ['foto' => $fotoSimpan]);
    }

    public function destroy($id): void {
        Auth::requireAdmin();
        $model = $this->model();
        $lama = $model->find((int)$id);
        if (!$lama) Response::json(404, false, 'Data tidak ditemukan');
        $st = Database::get()->prepare('DELETE FROM mahasiswa WHERE id = ?');
        $st->execute([(int)$id]);
        if (!empty($lama['foto'])) $this->hapusFoto($lama['foto']);
        Response::json(200, true, 'Mahasiswa dihapus');
    }

    /** Validasi gambar nyata, ekstensi JPG/PNG, dan ukuran maksimal 2 MiB. */
    private function simpanFoto(?array $file): ?string {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            Response::json(422, false, 'Upload foto gagal. Silakan pilih ulang berkas.');
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }
        if (!is_uploaded_file($file['tmp_name'] ?? '')) {
            Response::json(422, false, 'Berkas upload tidak valid');
        }
        $info = @getimagesize($file['tmp_name']);
        $mime = $info['mime'] ?? '';
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!$info || !isset($allowed[$mime])) {
            Response::json(422, false, 'Berkas harus berupa gambar JPG/PNG yang valid');
        }
        $dir = __DIR__ . '/../../public/uploads';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            Response::json(500, false, 'Folder upload tidak dapat dibuat');
        }
        $nama = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $nama)) {
            Response::json(500, false, 'Foto gagal disimpan');
        }
        return $nama;
    }

    private function hapusFoto(?string $nama): void {
        if (!$nama || basename($nama) !== $nama) return;
        $path = __DIR__ . '/../../public/uploads/' . $nama;
        if (is_file($path)) @unlink($path);
    }
}
