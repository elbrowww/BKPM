<?php

class ProdiModel extends Model
{
    protected string $table = 'prodi';

    // Cari prodi berdasarkan nama atau kode (tidak peka huruf besar/kecil)
    public function findByNameOrCode(string $text): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM prodi WHERE LOWER(nama) = LOWER(?) OR LOWER(kode) = LOWER(?) LIMIT 1");
        $stmt->execute([$text, $text]);

        return $stmt->fetch() ?: null;
    }
}
