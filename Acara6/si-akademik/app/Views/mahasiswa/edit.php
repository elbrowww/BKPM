<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">

        <div class="card shadow">
            <div class="card-body">

                <h2 class="mb-4">Edit Mahasiswa</h2>

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

                <form method="POST" action="<?= BASE_URL ?>/mahasiswa/update">

                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" id="nim" name="nim" class="form-control"
                            value="<?= htmlspecialchars($nim) ?>" readonly required>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Masukkan nama mahasiswa"
                            value="<?= htmlspecialchars($nama) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" id="prodi" name="prodi" class="form-control"
                            placeholder="Masukkan program studi"
                            value="<?= htmlspecialchars($prodi) ?>" required>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-secondary">Kembali</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
