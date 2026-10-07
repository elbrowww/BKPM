<?php

class ProdiController extends Controller
{
    private ProdiModel $model;

    public function __construct()
    {
        $this->model = new ProdiModel();
    }

    public function index()
    {
        $this->view('prodi/index', ['title' => 'Program Studi', 'prodi' => $this->model->all()]);
    }

    public function create()
    {
        $this->form('create', 'Tambah Program Studi', ['id' => 0, 'kode' => '', 'nama' => '']);
    }

    public function store()
    {
        $d      = $this->input();
        $errors = $this->validate($d);

        if ($errors) {
            $this->form('create', 'Tambah Program Studi', $d, $errors);
            return;
        }

        $this->model->create($d['kode'], $d['nama']);
        $this->flash('success', 'Program studi berhasil ditambahkan.');
        $this->redirect('/prodi');
    }

    public function edit()
    {
        $row = $this->model->find((int) ($_GET['id'] ?? 0));

        if ($row === null) {
            $this->flash('danger', 'Program studi tidak ditemukan.');
            $this->redirect('/prodi');
        }

        $this->form('edit', 'Edit Program Studi', $row);
    }

    public function update()
    {
        $d = $this->input();

        if ($this->model->find($d['id']) === null) {
            $this->flash('danger', 'Program studi tidak ditemukan.');
            $this->redirect('/prodi');
        }

        $errors = $this->validate($d);

        if ($errors) {
            $this->form('edit', 'Edit Program Studi', $d, $errors);
            return;
        }

        $this->model->update($d['id'], $d['kode'], $d['nama']);
        $this->flash('success', 'Program studi berhasil diperbarui.');
        $this->redirect('/prodi');
    }

    public function delete()
    {
        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->model->delete($id);
            $this->flash('success', 'Program studi berhasil dihapus.');
        } catch (PDOException $e) {
            // Foreign key RESTRICT: masih dipakai mahasiswa / mata kuliah
            $this->flash('danger', 'Program studi tidak bisa dihapus karena masih dipakai oleh mahasiswa atau mata kuliah.');
        }

        $this->redirect('/prodi');
    }

    private function input(): array
    {
        return [
            'id'   => (int) ($_POST['id'] ?? 0),
            'kode' => strtoupper(trim($_POST['kode'] ?? '')),
            'nama' => trim($_POST['nama'] ?? ''),
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];

        if ($d['kode'] === '' || strlen($d['kode']) > 10) {
            $errors[] = 'Kode wajib diisi (maksimal 10 karakter).';
        } elseif ($this->model->kodeExists($d['kode'], $d['id'])) {
            $errors[] = 'Kode sudah dipakai.';
        }
        if ($d['nama'] === '' || strlen($d['nama']) > 100) {
            $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
        }

        return $errors;
    }

    private function form(string $name, string $title, array $old, array $errors = []): void
    {
        $this->view('prodi/' . $name, ['title' => $title, 'errors' => $errors, 'old' => $old]);
    }
}
