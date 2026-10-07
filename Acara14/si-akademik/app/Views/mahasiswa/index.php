<div class="card shadow">
    <div class="card-body">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <h2 class="card-title mb-0">Data Mahasiswa</h2>
            <a href="<?= BASE_URL ?>/mahasiswa/create" class="btn btn-success">
                + Tambah Mahasiswa
            </a>
        </div>

        <form method="GET" action="<?= BASE_URL ?>/mahasiswa" class="d-flex flex-column flex-sm-row gap-2 mb-4">
            <input type="text" name="q" class="form-control" placeholder="Cari nama atau NIM..."
                value="<?= htmlspecialchars($q ?? '') ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if (!empty($q)): ?>
                <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($mahasiswa)): ?>
                        <?php foreach ($mahasiswa as $mhs): ?>
                            <tr>
                                <td><?= htmlspecialchars($mhs->getNim()) ?></td>
                                <td><?= htmlspecialchars($mhs->getNama()) ?></td>
                                <td><?= htmlspecialchars($mhs->getProdi()) ?></td>
                                <td><?= htmlspecialchars($mhs->getAngkatan()) ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= BASE_URL ?>/mahasiswa/edit?id=<?= (int) $mhs->getId() ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form method="POST" action="<?= BASE_URL ?>/mahasiswa/delete" class="d-inline"
                                            onsubmit="return confirm('Hapus data ini?');">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $mhs->getId() ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center"><?= !empty($q) ? 'Data tidak ditemukan.' : 'Belum ada data mahasiswa.' ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
