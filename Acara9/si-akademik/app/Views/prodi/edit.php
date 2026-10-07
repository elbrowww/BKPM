<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">

        <div class="card shadow">
            <div class="card-body">

                <h2 class="mb-4">Edit Program Studi</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/prodi/update">
                    <input type="hidden" name="id" value="<?= (int) $old['id'] ?>">

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode</label>
                        <input type="text" id="kode" name="kode" class="form-control" maxlength="10"
                            placeholder="Contoh: TI"
                            value="<?= htmlspecialchars((string) $old['kode']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Program Studi</label>
                        <input type="text" id="nama" name="nama" class="form-control" maxlength="100"
                            placeholder="Masukkan nama program studi"
                            value="<?= htmlspecialchars((string) $old['nama']) ?>" required>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="<?= BASE_URL ?>/prodi" class="btn btn-secondary">Kembali</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
