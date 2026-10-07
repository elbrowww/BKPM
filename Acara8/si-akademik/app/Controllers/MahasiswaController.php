<?php

class MahasiswaController extends Controller
{
    private const STATUS = ['aktif', 'cuti', 'lulus'];

    private MahasiswaModel $model;
    private ProdiModel $prodiModel;

    public function __construct()
    {
        $this->model      = new MahasiswaModel();
        $this->prodiModel = new ProdiModel();
    }

    // Daftar + pencarian (?q=nama atau NIM)
    public function index()
    {
        $q = trim($_GET['q'] ?? '');

        $this->view('mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $this->model->search($q),
            'q'         => $q,
        ]);
    }

    public function create()
    {
        $this->form('create', 'Tambah Mahasiswa', [
            'nim' => '', 'nama' => '', 'email' => '', 'prodi_id' => 0,
            'angkatan' => date('Y'), 'status' => 'aktif',
        ]);
    }

    public function store()
    {
        $data = $this->input();
        $errors = $this->validate($data);

        if ($data['nim'] !== '' && $this->model->find($data['nim']) !== null) {
            $errors[] = 'NIM sudah terdaftar.';
        }

        if ($errors) {
            $this->form('create', 'Tambah Mahasiswa', $data, $errors);
            return;
        }

        $this->model->create($data);
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
        $row = $this->model->find($_GET['nim'] ?? '');

        if ($row === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $this->form('edit', 'Edit Mahasiswa', $row);
    }

    public function update()
    {
        $data = $this->input();

        if ($this->model->find($data['nim']) === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $errors = $this->validate($data);

        if ($errors) {
            $this->form('edit', 'Edit Mahasiswa', $data, $errors);
            return;
        }

        $this->model->update($data['nim'], $data);
        $this->flash('success', 'Data mahasiswa berhasil diperbarui.');
        $this->redirect('/mahasiswa');
    }

    public function delete()
    {
        $nim = $_GET['nim'] ?? '';

        if ($this->model->find($nim) === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
        } else {
            $this->model->delete($nim);
            $this->flash('success', 'Data mahasiswa berhasil dihapus.');
        }

        $this->redirect('/mahasiswa');
    }

    // ---------- helper ----------

    private function input(): array
    {
        return [
            'nim'      => trim($_POST['nim'] ?? ''),
            'nama'     => trim($_POST['nama'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
            'angkatan' => (int) ($_POST['angkatan'] ?? date('Y')),
            'status'   => $_POST['status'] ?? 'aktif',
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];

        if ($d['nim'] === '' || !ctype_digit($d['nim']) || strlen($d['nim']) > 20) {
            $errors[] = 'NIM wajib diisi dan harus berupa angka (maksimal 20 digit).';
        }
        if ($d['nama'] === '') {
            $errors[] = 'Nama wajib diisi.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }
        if ($this->prodiModel->find($d['prodi_id']) === null) {
            $errors[] = 'Program studi wajib dipilih.';
        }
        if ($d['angkatan'] < 1990 || $d['angkatan'] > (int) date('Y') + 1) {
            $errors[] = 'Angkatan tidak valid.';
        }
        if (!in_array($d['status'], self::STATUS, true)) {
            $errors[] = 'Status tidak valid.';
        }

        return $errors;
    }

    private function form(string $name, string $title, array $old, array $errors = []): void
    {
        $this->view('mahasiswa/' . $name, [
            'title'      => $title,
            'errors'     => $errors,
            'old'        => $old,
            'prodiList'  => $this->prodiModel->all(),
            'statusList' => self::STATUS,
        ]);
    }
}
