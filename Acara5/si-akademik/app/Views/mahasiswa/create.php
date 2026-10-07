<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">

        <div class="card shadow">
            <div class="card-body">

                <h2 class="mb-4">Tambah Mahasiswa</h2>

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

                <form method="POST" action="/si-akademik/public/mahasiswa">

                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" id="nim" name="nim" class="form-control"
                            placeholder="Masukkan NIM (harus angka)"
                            value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Masukkan nama mahasiswa"
                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" id="prodi" name="prodi" class="form-control"
                            placeholder="Masukkan program studi"
                            value="<?= htmlspecialchars($_POST['prodi'] ?? '') ?>" required>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="/si-akademik/public/mahasiswa" class="btn btn-secondary">Kembali</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
