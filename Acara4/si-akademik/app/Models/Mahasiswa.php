<?php

class Mahasiswa
{
    private string $nim;
    private string $nama;
    private string $prodi;

    public function __construct(string $nim, string $nama, string $prodi)
    {
        $this->nim   = $nim;
        $this->nama  = $nama;
        $this->prodi = $prodi;
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

    // Tugas mandiri:
    // Asumsi format NIM diawali 2 digit tahun masuk, contoh: 24010001 => angkatan 2024
    public function getAngkatan(): string
    {
        return '20' . substr($this->nim, 0, 2);
    }
}
