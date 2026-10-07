<?php
// Data contoh sementara agar tampilan tabel terlihat.
// Akan digantikan data dari Controller/Model di pertemuan berikutnya.
if (!isset($mahasiswa)) {
    $mahasiswa = [
        ["nim" => "2024001", "nama" => "Budi Santoso",  "prodi" => "Teknik Informatika"],
        ["nim" => "2024002", "nama" => "Siti Aminah",   "prodi" => "Manajemen Informatika"],
        ["nim" => "2024003", "nama" => "Agus Prasetyo", "prodi" => "Teknik Komputer"],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Politeknik Negeri Jember</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5 mb-4">

        <h1 class="text-center mb-4">Politeknik Negeri Jember</h1>

        <div class="card shadow">
            <div class="card-body">

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
                    <h2 class="card-title mb-0">Data Mahasiswa</h2>
                    <a href="create.php" class="btn btn-success">
                        + Tambah Mahasiswa
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($mahasiswa)): ?>
                                <?php foreach ($mahasiswa as $mhs): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mhs["nim"]) ?></td>
                                        <td><?= htmlspecialchars($mhs["nama"]) ?></td>
                                        <td><?= htmlspecialchars($mhs["prodi"]) ?></td>
                                        <td>
                                            <a href="BKPM/Acara3/si-akademik/public/mahasiswa/edit?nim=<?= urlencode($mhs["nim"]) ?>"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <a href="BKPM/Acara3/si-akademik/public/mahasiswa/delete?nim=<?= urlencode($mhs["nim"]) ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus data ini?');">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data mahasiswa.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
