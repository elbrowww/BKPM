<?php

// Controller hanya mengatur alur request/response (tanpa validasi, tanpa query).
// Semua proses yang mengubah data memakai pola PRG: POST -> proses -> redirect -> GET.
class MahasiswaController extends BaseController
{
    private MahasiswaService $service;

    public function __construct(MahasiswaService $service)
    {
        $this->service = $service;
    }

    // GET /mahasiswa -> daftar mahasiswa
    public function index()
    {
        $q = trim($_GET['q'] ?? '');

        $this->view('mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $this->service->search($q),
            'q'         => $q,
        ]);
    }

    // GET /mahasiswa/create -> form tambah
    public function create()
    {
        $this->form('create', 'Tambah Mahasiswa', [
            'id' => 0, 'nim' => '', 'nama' => '', 'email' => '', 'prodi_id' => 0,
            'angkatan' => date('Y'), 'status' => 'aktif',
        ]);
    }

    // POST /mahasiswa -> proses tambah, lalu redirect (PRG)
    public function store()
    {
        $this->verifyCsrf('/mahasiswa/create');

        $result = $this->service->create($_POST);

        if ($result['success']) {
            $this->flash('success', $result['message']);
            $this->redirect('/mahasiswa');
        }

        if ($result['message'] !== null) {
            $this->flash('danger', $result['message']);
        }

        Flash::withInput($result['input'], $result['errors']);
        $this->redirect('/mahasiswa/create');
    }

    public function show($id)
    {
        $this->redirect('/mahasiswa/edit?id=' . urlencode((string) $id));
    }

    // GET /mahasiswa/edit?id=1 -> form edit
    public function edit()
    {
        $mhs = $this->service->find((int) ($_GET['id'] ?? 0));

        if ($mhs === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        $this->form('edit', 'Edit Mahasiswa', $mhs->toArray());
    }

    // POST /mahasiswa/update -> proses ubah, lalu redirect (PRG)
    public function update()
    {
        $id = (int) ($_POST['id'] ?? 0);
        $this->verifyCsrf('/mahasiswa/edit?id=' . $id);

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

        Flash::withInput($result['input'], $result['errors']);
        $this->redirect('/mahasiswa/edit?id=' . $id);
    }

    // POST /mahasiswa/delete -> proses hapus, lalu redirect (PRG)
    public function delete()
    {
        $this->verifyCsrf('/mahasiswa');

        $result = $this->service->delete((int) ($_POST['id'] ?? 0));

        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('/mahasiswa');
    }

    // Tolak POST tanpa token CSRF yang valid
    private function verifyCsrf(string $backTo): void
    {
        if (!Csrf::verify()) {
            Logger::warning('Token CSRF tidak valid', ['uri' => $_SERVER['REQUEST_URI'] ?? '']);
            $this->flash('danger', 'Permintaan tidak valid. Silakan coba lagi.');
            $this->redirect($backTo);
        }
    }

    // Tampilkan form; isian lama + error validasi (hasil redirect PRG) menimpa nilai default
    private function form(string $name, string $title, array $default): void
    {
        $state = Flash::pullInput();

        $this->view('mahasiswa/' . $name, [
            'title'      => $title,
            'errors'     => $state['errors'],
            'old'        => $state['old'] ? array_merge($default, $state['old']) : $default,
            'prodiList'  => $this->service->prodiList(),
            'statusList' => $this->service->statusList(),
        ]);
    }
}
