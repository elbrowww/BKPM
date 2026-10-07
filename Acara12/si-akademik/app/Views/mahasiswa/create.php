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

                <form method="POST" action="<?= BASE_URL ?>/mahasiswa">

                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" id="nim" name="nim" class="form-control"
                            placeholder="Masukkan NIM (harus angka)"
                            value="<?= htmlspecialchars((string) $old['nim']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Masukkan nama mahasiswa"
                            value="<?= htmlspecialchars((string) $old['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="contoh@email.com"
                            value="<?= htmlspecialchars((string) $old['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="prodi_id" class="form-label">Program Studi</label>
                        <select id="prodi_id" name="prodi_id" class="form-select" required>
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach ($prodiList as $p): ?>
                                <option value="<?= (int) $p['id'] ?>" <?= (int) $old['prodi_id'] === (int) $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="angkatan" class="form-label">Angkatan</label>
                        <input type="number" id="angkatan" name="angkatan" class="form-control" min="1990"
                            value="<?= htmlspecialchars((string) $old['angkatan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <?php foreach ($statusList as $s): ?>
                                <option value="<?= $s ?>" <?= $old['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
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
