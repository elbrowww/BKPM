<?php

class MatakuliahController extends BaseController
{
    private MatakuliahRepository $repo;
    private ProdiRepository $prodiRepo;

    public function __construct(MatakuliahRepository $repo, ProdiRepository $prodiRepo)
    {
        $this->repo      = $repo;
        $this->prodiRepo = $prodiRepo;
    }

    public function index()
    {
        $this->view('matakuliah/index', ['title' => 'Mata Kuliah', 'matakuliah' => $this->repo->all()]);
    }

    public function create()
    {
        $this->form('create', 'Tambah Mata Kuliah', ['id' => 0, 'kode' => '', 'nama' => '', 'sks' => 3, 'prodi_id' => 0]);
    }

    public function store()
    {
        $d      = $this->input();
        $errors = $this->validate($d);

        if ($errors) {
            $this->form('create', 'Tambah Mata Kuliah', $d, $errors);
            return;
        }

        $this->repo->create($d);
        $this->flash('success', 'Mata kuliah berhasil ditambahkan.');
        $this->redirect('/matakuliah');
    }

    public function edit()
    {
        $row = $this->repo->find((int) ($_GET['id'] ?? 0));

        if ($row === null) {
            $this->flash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect('/matakuliah');
        }

        $this->form('edit', 'Edit Mata Kuliah', $row);
    }

    public function update()
    {
        $d = $this->input();

        if ($this->repo->find($d['id']) === null) {
            $this->flash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect('/matakuliah');
        }

        $errors = $this->validate($d);

        if ($errors) {
            $this->form('edit', 'Edit Mata Kuliah', $d, $errors);
            return;
        }

        $this->repo->update($d['id'], $d);
        $this->flash('success', 'Mata kuliah berhasil diperbarui.');
        $this->redirect('/matakuliah');
    }

    public function delete()
    {
        $this->repo->delete((int) ($_POST['id'] ?? 0));
        $this->flash('success', 'Mata kuliah berhasil dihapus.');
        $this->redirect('/matakuliah');
    }

    private function input(): array
    {
        return [
            'id'       => (int) ($_POST['id'] ?? 0),
            'kode'     => strtoupper(trim($_POST['kode'] ?? '')),
            'nama'     => trim($_POST['nama'] ?? ''),
            'sks'      => (int) ($_POST['sks'] ?? 0),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];

        if ($d['kode'] === '' || strlen($d['kode']) > 10) {
            $errors[] = 'Kode wajib diisi (maksimal 10 karakter).';
        } elseif ($this->repo->kodeExists($d['kode'], $d['id'])) {
            $errors[] = 'Kode sudah dipakai.';
        }
        if ($d['nama'] === '' || strlen($d['nama']) > 150) {
            $errors[] = 'Nama wajib diisi (maksimal 150 karakter).';
        }
        if ($d['sks'] < 1 || $d['sks'] > 6) {
            $errors[] = 'SKS harus antara 1 sampai 6.';
        }
        if ($this->prodiRepo->find($d['prodi_id']) === null) {
            $errors[] = 'Program studi wajib dipilih.';
        }

        return $errors;
    }

    private function form(string $name, string $title, array $old, array $errors = []): void
    {
        $this->view('matakuliah/' . $name, [
            'title' => $title, 'errors' => $errors, 'old' => $old,
            'prodiList' => $this->prodiRepo->all(),
        ]);
    }
}
