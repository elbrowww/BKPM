<?php

class MahasiswaController extends Controller
{
    private MahasiswaModel $repo;
    private ProdiModel $prodiModel;

    public function __construct()
    {
        $this->repo       = new MahasiswaModel();
        $this->prodiModel = new ProdiModel();
    }

    public function index()
    {
        $this->view('mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $this->repo->all(),
        ]);
    }

    public function create()
    {
        $this->view('mahasiswa/create', ['title' => 'Tambah Mahasiswa', 'errors' => []]);
    }

    public function store()
    {
        $nim   = trim($_POST['nim'] ?? '');
        $nama  = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        $errors = [];
        if ($nim === '' || !ctype_digit($nim) || strlen($nim) < 2) {
            $errors[] = 'NIM wajib diisi dan harus berupa angka.';
        } elseif ($this->repo->find($nim) !== null) {
            $errors[] = 'NIM sudah terdaftar.';
        }
        if ($nama === '')  { $errors[] = 'Nama wajib diisi.'; }

        $prodiRow = $prodi === '' ? null : $this->prodiModel->findByNameOrCode($prodi);
        if ($prodi === '') {
            $errors[] = 'Program studi wajib diisi.';
        } elseif ($prodiRow === null) {
            $errors[] = 'Program studi tidak ditemukan. Gunakan: '
                . implode(', ', array_column($this->prodiModel->all(), 'nama')) . '.';
        }

        if ($errors) {
            $this->view('mahasiswa/create', ['title' => 'Tambah Mahasiswa', 'errors' => $errors]);
            return;
        }

        // Form belum punya kolom email; isi email default dari NIM, angkatan dari 2 digit awal NIM
        $this->repo->insert($nim, $nama, $nim . '@email.com', (int) $prodiRow['id'], (int) ('20' . substr($nim, 0, 2)));
        $this->flash('success', 'Data mahasiswa berhasil ditambahkan.');
        $this->redirect('/mahasiswa');
    }

    // /mahasiswa/{id}: cari berdasarkan NIM, tampilkan di form edit
    public function show($id)
    {
        $this->redirect('/mahasiswa/edit?nim=' . urlencode((string) $id));
    }

    public function edit()
    {
        $mhs = $this->repo->find($_GET['nim'] ?? '');

        if ($mhs === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $this->view('mahasiswa/edit', [
            'title'  => 'Edit Mahasiswa',
            'errors' => [],
            'nim'    => $mhs->getNim(),
            'nama'   => $mhs->getNama(),
            'prodi'  => $mhs->getProdi(),
        ]);
    }

    public function update()
    {
        $nim   = $_POST['nim'] ?? '';
        $nama  = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        if ($this->repo->find($nim) === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $errors = [];
        if ($nama === '')  { $errors[] = 'Nama wajib diisi.'; }

        $prodiRow = $prodi === '' ? null : $this->prodiModel->findByNameOrCode($prodi);
        if ($prodi === '') {
            $errors[] = 'Program studi wajib diisi.';
        } elseif ($prodiRow === null) {
            $errors[] = 'Program studi tidak ditemukan. Gunakan: '
                . implode(', ', array_column($this->prodiModel->all(), 'nama')) . '.';
        }

        if ($errors) {
            $this->view('mahasiswa/edit', [
                'title' => 'Edit Mahasiswa', 'errors' => $errors,
                'nim' => $nim, 'nama' => $nama, 'prodi' => $prodi,
            ]);
            return;
        }

        $this->repo->update($nim, $nama, (int) $prodiRow['id']);
        $this->flash('success', 'Data mahasiswa berhasil diperbarui.');
        $this->redirect('/mahasiswa');
    }

    public function delete()
    {
        $nim = $_GET['nim'] ?? '';

        if ($this->repo->find($nim) === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
        } else {
            $this->repo->delete($nim);
            $this->flash('success', 'Data mahasiswa berhasil dihapus.');
        }

        $this->redirect('/mahasiswa');
    }
}
