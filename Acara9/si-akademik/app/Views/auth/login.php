<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="container flex-grow-1 d-flex align-items-center justify-content-center py-4">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <h1 class="text-center mb-4">Politeknik Negeri Jember</h1>

        <div class="card shadow">
            <div class="card-body p-4">

                <h2 class="text-center mb-1">Login</h2>
                <p class="text-center text-muted">Sistem Informasi Akademik</p>

                <?php require __DIR__ . '/../partials/flash.php'; ?>

                <form method="POST" action="<?= BASE_URL ?>/login">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>

                </form>

            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
