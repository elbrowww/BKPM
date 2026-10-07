<?php

// Penyimpanan data mahasiswa (file JSON) - nanti diganti database
class MahasiswaRepository
{
    private string $file;

    public function __construct()
    {
        $db         = require ROOT_PATH . '/config/database.php';
        $this->file = $db['path'] . '/mahasiswa.json';
    }

    /** @return Mahasiswa[] */
    public function all(): array
    {
        $rows = is_file($this->file) ? json_decode(file_get_contents($this->file), true) : [];

        return array_map(
            fn($r) => new Mahasiswa($r['nim'], $r['nama'], $r['prodi']),
            is_array($rows) ? $rows : []
        );
    }

    public function find(string $nim): ?Mahasiswa
    {
        foreach ($this->all() as $mhs) {
            if ($mhs->getNim() === $nim) {
                return $mhs;
            }
        }
        return null;
    }

    public function add(Mahasiswa $mhs): void
    {
        $all   = $this->all();
        $all[] = $mhs;
        $this->save($all);
    }

    public function update(Mahasiswa $mhs): void
    {
        $all = array_map(
            fn($m) => $m->getNim() === $mhs->getNim() ? $mhs : $m,
            $this->all()
        );
        $this->save($all);
    }

    public function delete(string $nim): void
    {
        $this->save(array_filter($this->all(), fn($m) => $m->getNim() !== $nim));
    }

    private function save(array $list): void
    {
        $rows = array_map(fn($m) => $m->toArray(), array_values($list));
        file_put_contents($this->file, json_encode($rows, JSON_PRETTY_PRINT), LOCK_EX);
    }
}
