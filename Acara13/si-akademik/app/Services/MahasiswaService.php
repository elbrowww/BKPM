<?php

// Logika bisnis mahasiswa: validasi data + aturan simpan.
// Alur: Controller -> MahasiswaService -> MahasiswaRepository -> Database
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
        [$mhs, $errors] = $this->fill(new Mahasiswa(), $input);

        if ($errors) {
            return $this->fail($input, $errors);
        }

        if ($this->repo->find($mhs->getNim()) !== null) {
            return $this->fail($input, [], 'NIM sudah terdaftar.');
        }

        try {
            $this->repo->create($mhs);
        } catch (PDOException $e) {
            return $this->fail($input, [], 'Data gagal disimpan.');
        }

        return $this->ok($input, 'Data berhasil ditambahkan.');
    }

    // ---------- Proses ubah ----------
    public function update(array $input): array
    {
        $input = $this->normalize($input);
        $mhs   = $this->repo->find($input['nim']);

        if ($mhs === null) {
            $result = $this->fail($input, [], 'Data mahasiswa tidak ditemukan.');
            $result['not_found'] = true;
            return $result;
        }

        $fields = $input;
        unset($fields['nim']);                       // NIM adalah kunci, tidak diubah
        [$mhs, $errors] = $this->fill($mhs, $fields);

        if ($errors) {
            return $this->fail($input, $errors);
        }

        try {
            $this->repo->update($mhs);
        } catch (PDOException $e) {
            return $this->fail($input, [], 'Data gagal disimpan.');
        }

        return $this->ok($input, 'Data berhasil diubah.');
    }

    // ---------- Data untuk Controller ----------
    public function search(string $keyword = ''): array { return $this->repo->search($keyword); }
    public function find(string $nim): ?Mahasiswa      { return $this->repo->find($nim); }
    public function prodiList(): array                 { return $this->prodiRepo->all(); }
    public function statusList(): array                { return Mahasiswa::STATUS; }

    public function delete(string $nim): bool
    {
        if ($this->repo->find($nim) === null) {
            return false;
        }
        $this->repo->delete($nim);
        return true;
    }

    // ---------- Validasi (private) ----------
    private function normalize(array $in): array
    {
        return [
            'nim'      => trim($in['nim'] ?? ''),
            'nama'     => trim($in['nama'] ?? ''),
            'email'    => trim($in['email'] ?? ''),
            'prodi_id' => (int) ($in['prodi_id'] ?? 0),
            'angkatan' => (int) ($in['angkatan'] ?? date('Y')),
            'status'   => $in['status'] ?? 'aktif',
        ];
    }

    // Isi entity lewat setter; setter yang gagal validasi dikumpulkan sebagai error
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

    private function ok(array $input, string $message): array
    {
        return ['success' => true, 'message' => $message, 'errors' => [], 'input' => $input, 'not_found' => false];
    }

    private function fail(array $input, array $errors, ?string $message = null): array
    {
        return ['success' => false, 'message' => $message, 'errors' => $errors, 'input' => $input, 'not_found' => false];
    }
}