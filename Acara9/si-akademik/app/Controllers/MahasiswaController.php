<?php

class MahasiswaController extends Controller
{
    private MahasiswaRepository $repo;
    private ProdiRepository $prodiRepo;

    // Dependency di-inject lewat constructor (controller tidak membuat koneksi database sendiri)
    public function __construct(MahasiswaRepository $repo, ProdiRepository $prodiRepo)
    {
        $this->repo      = $repo;
        $this->prodiRepo = $prodiRepo;
    }

    // Daftar + pencarian (?q=nama atau NIM)
    public function index()
    {
        $q = trim($_GET['q'] ?? '');

        $this->view('mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $this->repo->search($q),
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
        $input = $this->input();
        [$mhs, $errors] = $this->fill(new Mahasiswa(), $input);

        if (empty($errors) && $this->repo->find($mhs->getNim()) !== null) {
            $errors[] = 'NIM sudah terdaftar.';
        }

        if ($errors) {
            $this->form('create', 'Tambah Mahasiswa', $input, $errors);
            return;
        }

        $this->repo->create($mhs);
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

        $this->form('edit', 'Edit Mahasiswa', $mhs->toArray());
    }

    public function update()
    {
        $input = $this->input();

        // Ambil object yang ada, lalu ubah lewat setter (NIM tidak diubah)
        $mhs = $this->repo->find($input['nim']);

        if ($mhs === null) {
            $this->flash('danger', 'Data mahasiswa tidak ditemukan.');
            $this->redirect('/mahasiswa');
        }

        unset($input['nim']);
        [$mhs, $errors] = $this->fill($mhs, $input);

        if ($errors) {
            $input['nim'] = $mhs->getNim();
            $this->form('edit', 'Edit Mahasiswa', $input, $errors);
            return;
        }

        $this->repo->update($mhs);
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

    // Isi object Mahasiswa lewat setter. Setter yang gagal validasi dikumpulkan sebagai pesan error.
    private function fill(Mahasiswa $mhs, array $input): array
    {
        $errors = [];

        foreach ($input as $field => $value) {
            $setter = 'set' . str_replace('_', '', ucwords($field, '_'));   // prodi_id => setProdiId

            try {
                $mhs->$setter($value);
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Pastikan prodi yang dipilih benar-benar ada
        if (isset($input['prodi_id']) && $input['prodi_id'] > 0 && $this->prodiRepo->find($input['prodi_id']) === null) {
            $errors[] = 'Program studi wajib dipilih.';
        }

        return [$mhs, $errors];
    }

    private function form(string $name, string $title, array $old, array $errors = []): void
    {
        $this->view('mahasiswa/' . $name, [
            'title'      => $title,
            'errors'     => $errors,
            'old'        => $old,
            'prodiList'  => $this->prodiRepo->all(),
            'statusList' => Mahasiswa::STATUS,
        ]);
    }
}
