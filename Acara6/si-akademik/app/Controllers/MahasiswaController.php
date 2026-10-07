<?php

class MahasiswaController extends Controller
{
    private MahasiswaRepository $repo;

    public function __construct()
    {
        $this->repo = new MahasiswaRepository();
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
        if ($nim === '' || !ctype_digit($nim)) {
            $errors[] = 'NIM wajib diisi dan harus berupa angka.';
        } elseif ($this->repo->find($nim) !== null) {
            $errors[] = 'NIM sudah terdaftar.';
        }
        if ($nama === '')  { $errors[] = 'Nama wajib diisi.'; }
        if ($prodi === '') { $errors[] = 'Program studi wajib diisi.'; }

        if ($errors) {
            $this->view('mahasiswa/create', ['title' => 'Tambah Mahasiswa', 'errors' => $errors]);
            return;
        }

        $this->repo->add(new Mahasiswa($nim, $nama, $prodi));
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
        if ($prodi === '') { $errors[] = 'Program studi wajib diisi.'; }

        if ($errors) {
            $this->view('mahasiswa/edit', [
                'title' => 'Edit Mahasiswa', 'errors' => $errors,
                'nim' => $nim, 'nama' => $nama, 'prodi' => $prodi,
            ]);
            return;
        }

        $this->repo->update(new Mahasiswa($nim, $nama, $prodi));
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
