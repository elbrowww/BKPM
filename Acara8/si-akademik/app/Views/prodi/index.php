<div class="card shadow">
    <div class="card-body">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <h2 class="card-title mb-0">Program Studi</h2>
            <a href="<?= BASE_URL ?>/prodi/create" class="btn btn-success">
                + Tambah Program Studi
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($prodi)): ?>
                        <?php foreach ($prodi as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $row['kode']) ?></td>
                                <td><?= htmlspecialchars((string) $row['nama']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/prodi/edit?id=<?= (int) $row['id'] ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="<?= BASE_URL ?>/prodi/delete" class="d-inline"
                                        onsubmit="return confirm('Hapus data ini?');">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
