<div class="card shadow">
    <div class="card-body">

        <h2 class="text-center mb-3">Dashboard</h2>

        <p class="text-center">
            Selamat datang,
            <strong><?= htmlspecialchars(ucfirst($_SESSION['username'])) ?></strong>!
        </p>

        <hr>

        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-primary">Data Mahasiswa</a>
            <a href="<?= BASE_URL ?>/prodi" class="btn btn-primary">Program Studi</a>
            <a href="<?= BASE_URL ?>/matakuliah" class="btn btn-primary">Mata Kuliah</a>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-danger">Logout</a>
        </div>

    </div>
</div>
