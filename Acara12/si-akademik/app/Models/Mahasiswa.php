<?php

// Entity Mahasiswa: semua atribut private, diakses lewat getter/setter.
// Setter memvalidasi nilai dan melempar InvalidArgumentException bila tidak valid.
class Mahasiswa
{
    public const STATUS = ['aktif', 'cuti', 'lulus'];

    private string $nim      = '';
    private string $nama     = '';
    private string $email    = '';
    private int    $prodiId  = 0;
    private string $prodi    = '';      // nama prodi (hasil JOIN), hanya untuk tampilan
    private int    $angkatan = 0;
    private string $status   = 'aktif';

    // ---------- Getter ----------

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getProdiId(): int
    {
        return $this->prodiId;
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function getAngkatan(): string
    {
        return (string) $this->angkatan;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // ---------- Setter + validasi ----------

    public function setNim(string $nim): self
    {
        $nim = trim($nim);

        if ($nim === '' || !ctype_digit($nim) || strlen($nim) > 20) {
            throw new InvalidArgumentException('NIM wajib diisi dan harus berupa angka (maksimal 20 digit).');
        }

        $this->nim = $nim;
        return $this;
    }

    public function setNama(string $nama): self
    {
        $nama = trim($nama);

        if ($nama === '' || strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama wajib diisi.');
        }

        $this->nama = $nama;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            throw new InvalidArgumentException('Email tidak valid.');
        }

        $this->email = $email;
        return $this;
    }

    public function setProdiId(int $prodiId): self
    {
        if ($prodiId <= 0) {
            throw new InvalidArgumentException('Program studi wajib dipilih.');
        }

        $this->prodiId = $prodiId;
        return $this;
    }

    public function setProdi(string $prodi): self
    {
        $this->prodi = $prodi;
        return $this;
    }

    public function setAngkatan(int $angkatan): self
    {
        if ($angkatan < 1990 || $angkatan > (int) date('Y') + 1) {
            throw new InvalidArgumentException('Angkatan tidak valid.');
        }

        $this->angkatan = $angkatan;
        return $this;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::STATUS, true)) {
            throw new InvalidArgumentException('Status tidak valid.');
        }

        $this->status = $status;
        return $this;
    }

    // ---------- Pembantu ----------

    // Membuat object dari satu baris hasil query
    public static function fromRow(array $row): self
    {
        return (new self())
            ->setNim($row['nim'])
            ->setNama($row['nama'])
            ->setEmail($row['email'])
            ->setProdiId((int) $row['prodi_id'])
            ->setProdi($row['prodi'] ?? '')
            ->setAngkatan((int) $row['angkatan'])
            ->setStatus($row['status']);
    }

    // Untuk mengisi value form edit
    public function toArray(): array
    {
        return [
            'nim'      => $this->nim,
            'nama'     => $this->nama,
            'email'    => $this->email,
            'prodi_id' => $this->prodiId,
            'angkatan' => $this->angkatan,
            'status'   => $this->status,
        ];
    }
}
