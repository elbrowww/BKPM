<?php

// Logika bisnis mahasiswa: validasi data, cek NIM duplikat, exception handling, dan logging.
// Alur: Controller -> MahasiswaService -> (Validasi) -> MahasiswaRepository -> MySQL
//       Jika terjadi exception: Logging -> pesan aman ke pengguna (tanpa detail teknis)
class MahasiswaService
{
    private MahasiswaRepository $repo;
    private ProdiRepository $prodiRepo;

    public function __construct(MahasiswaRepository $repo, ProdiRepository $prodiRepo)
    {
        $this->repo      = $repo;
        $this->prodiRepo = $prodiRepo;
    }

    // ---------- Proses tambah ----------
    public function create(array $input): array
    {
        $input = $this->normalize($input);

        try {
            [$mhs, $errors] = $this->fill(new Mahasiswa(), $input);

            if ($errors) {
                return $this->fail($input, $errors);
            }

            if ($this->repo->existsByNim($mhs->getNim())) {
                return $this->fail($input, [], 'NIM sudah terdaftar.');
            }

            $this->repo->create($mhs);
        } catch (PDOException $e) {
            // Dua request bersamaan dengan NIM sama: ditolak oleh UNIQUE di database
            if ($this->isDuplicateKey($e)) {
                return $this->fail($input, [], 'NIM sudah terdaftar.');
            }

            Logger::error('Gagal menambah mahasiswa', $e, ['nim' => $input['nim']]);
            return $this->fail($input, [], 'Data gagal disimpan.');
        }

        return $this->ok($input, 'Data mahasiswa berhasil ditambahkan.');
    }

    // ---------- Proses ubah ----------
    public function update(array $input): array
    {
        $input = $this->normalize($input);

        try {
            $mhs = $input['id'] > 0 ? $this->repo->find($input['id']) : null;

            if ($mhs === null) {
                $result = $this->fail($input, [], 'Data mahasiswa tidak ditemukan.');
                $result['not_found'] = true;
                return $result;
            }

            $fields = $input;
            unset($fields['id']);                    // id adalah kunci, tidak diubah
            [$mhs, $errors] = $this->fill($mhs, $fields);

            if ($errors) {
                return $this->fail($input, $errors);
            }

            if ($this->repo->existsByNim($mhs->getNim(), $mhs->getId())) {
                return $this->fail($input, [], 'NIM sudah terdaftar.');
            }

            $this->repo->update($mhs);
        } catch (PDOException $e) {
            if ($this->isDuplicateKey($e)) {
                return $this->fail($input, [], 'NIM sudah terdaftar.');
            }

            Logger::error('Gagal mengubah mahasiswa', $e, ['id' => $input['id'], 'nim' => $input['nim']]);
            return $this->fail($input, [], 'Data gagal disimpan.');
        }

        return $this->ok($input, 'Data mahasiswa berhasil diubah.');
    }

    // ---------- Proses hapus ----------
    public function delete(int $id): array
    {
        try {
            if ($this->repo->find($id) === null) {
                return $this->fail([], [], 'Data mahasiswa tidak ditemukan.');
            }

            $this->repo->delete($id);
        } catch (PDOException $e) {
            Logger::error('Gagal menghapus mahasiswa', $e, ['id' => $id]);
            return $this->fail([], [], 'Data gagal dihapus.');
        }

        return $this->ok([], 'Data mahasiswa berhasil dihapus.');
    }

    // ---------- Data untuk Controller ----------
    // Error database pada proses baca ditangkap handler global di public/index.php (dicatat ke log).
    public function search(string $keyword = ''): array { return $this->repo->search($keyword); }
    public function find(int $id): ?Mahasiswa          { return $this->repo->find($id); }
    public function prodiList(): array                 { return $this->prodiRepo->all(); }
    public function statusList(): array                { return Mahasiswa::STATUS; }

    // ---------- Validasi (private) ----------
    private function normalize(array $in): array
    {
        return [
            'id'       => (int) ($in['id'] ?? 0),
            'nim'      => trim((string) ($in['nim'] ?? '')),
            'nama'     => trim((string) ($in['nama'] ?? '')),
            'email'    => trim((string) ($in['email'] ?? '')),
            'prodi_id' => (int) ($in['prodi_id'] ?? 0),
            'angkatan' => (int) ($in['angkatan'] ?? date('Y')),
            'status'   => (string) ($in['status'] ?? 'aktif'),
        ];
    }

    // Isi entity lewat setter; setter yang gagal validasi dikumpulkan sebagai error
    // (validasi NIM, nama, email, prodi, angkatan, status)
    private function fill(Mahasiswa $mhs, array $input): array
    {
        $errors = [];

        foreach ($input as $field => $value) {
            $setter = 'set' . str_replace('_', '', ucwords($field, '_'));   // prodi_id => setProdiId
            try {
                $mhs->$setter($value);
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (($input['prodi_id'] ?? 0) > 0 && $this->prodiRepo->find($input['prodi_id']) === null) {
            $errors[] = 'Program studi wajib dipilih.';
        }

        return [$mhs, $errors];
    }

    // Kode error MySQL 1062 = duplicate entry (pelanggaran UNIQUE)
    private function isDuplicateKey(PDOException $e): bool
    {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }

    private function ok(array $input, string $message): array
    {
        return ['success' => true, 'message' => $message, 'errors' => [], 'input' => $input, 'not_found' => false];
    }

    private function fail(array $input, array $errors, ?string $message = null): array
    {
        return ['success' => false, 'message' => $message, 'errors' => $errors, 'input' => $input, 'not_found' => false];
    }
}
