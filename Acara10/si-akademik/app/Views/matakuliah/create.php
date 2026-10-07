<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">

        <div class="card shadow">
            <div class="card-body">

                <h2 class="mb-4">Tambah Mata Kuliah</h2>

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

                <form method="POST" action="<?= BASE_URL ?>/matakuliah">

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode</label>
                        <input type="text" id="kode" name="kode" class="form-control" maxlength="10"
                            placeholder="Contoh: TI101"
                            value="<?= htmlspecialchars((string) $old['kode']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" id="nama" name="nama" class="form-control" maxlength="150"
                            placeholder="Masukkan nama mata kuliah"
                            value="<?= htmlspecialchars((string) $old['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="sks" class="form-label">SKS</label>
                        <input type="number" id="sks" name="sks" class="form-control" min="1" max="6"
                            value="<?= htmlspecialchars((string) $old['sks']) ?>" required>
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

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="<?= BASE_URL ?>/matakuliah" class="btn btn-secondary">Kembali</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
