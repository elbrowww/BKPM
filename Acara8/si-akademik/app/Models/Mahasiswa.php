<?php

// Entity sederhana untuk satu baris mahasiswa (dipakai oleh view)
class Mahasiswa
{
    private string $nim;
    private string $nama;
    private string $prodi;
    private ?string $angkatan;
    private string $status;

    public function __construct(string $nim, string $nama, string $prodi, ?string $angkatan = null, string $status = 'aktif')
    {
        $this->nim      = $nim;
        $this->nama     = $nama;
        $this->prodi    = $prodi;
        $this->angkatan = $angkatan;
        $this->status   = $status;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // Angkatan diambil dari database; jika kosong, turunkan dari NIM (24010001 => 2024)
    public function getAngkatan(): string
    {
        return $this->angkatan ?? '20' . substr($this->nim, 0, 2);
    }
}
