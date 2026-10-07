<?php

// Controller hanya mengatur alur request/response (tanpa validasi, tanpa query).
class MahasiswaController extends BaseController
{
    private MahasiswaService $service;

    public function __construct(MahasiswaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $q = trim($_GET['q'] ?? '');

        $this->view('mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $this->service->search($q),
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
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $this->flash('success', $result['message']);
            $this->redirect('/mahasiswa');
        }

        if ($result['message'] !== null) {
            $this->flash('danger', $result['message']);
        }

        $this->form('create', 'Tambah Mahasiswa', $result['input'], $result['errors']);
    }

    public function show($id)
    {
        $this->redirect('/mahasiswa/edit?nim=' . urlencode((string) $id));
    }

    public function edit()
    {
        $mhs = $this->service->find($_GET['nim'] ?? '');

        if ($mhs === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $this->form('edit', 'Edit Mahasiswa', $mhs->toArray());
    }

    public function update()
    {
        $result = $this->service->update($_POST);

        if ($result['success']) {
            $this->flash('success', $result['message']);
            $this->redirect('/mahasiswa');
        }

        if ($result['message'] !== null) {
            $this->flash('danger', $result['message']);
        }

        if ($result['not_found']) {
            $this->redirect('/mahasiswa');
        }

        $this->form('edit', 'Edit Mahasiswa', $result['input'], $result['errors']);
    }

    public function delete()
    {
        if ($this->service->delete($_GET['nim'] ?? '')) {
            $this->flash('success', 'Data mahasiswa berhasil dihapus.');
        } else {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
        }

        $this->redirect('/mahasiswa');
    }

    private function form(string $name, string $title, array $old, array $errors = []): void
    {
        $this->view('mahasiswa/' . $name, [
            'title'      => $title,
            'errors'     => $errors,
            'old'        => $old,
            'prodiList'  => $this->service->prodiList(),
            'statusList' => $this->service->statusList(),
        ]);
    }
}