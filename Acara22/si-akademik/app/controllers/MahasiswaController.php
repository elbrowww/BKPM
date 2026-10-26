<?php
class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        $id = $_GET['id'] ?? null;
        if ($id !== null && $id !== '') {
            if (!$this->validId($id)) {
                Response::json(400, false, 'Parameter id harus berupa bilangan bulat positif.');
            }
            $data = $this->model->find((int) $id);
            if (!$data) {
                Response::json(404, false, 'Data mahasiswa tidak ditemukan.');
            }
            Response::json(200, true, 'Data berhasil diambil.', $data);
        }
        Response::json(200, true, 'Data berhasil diambil.', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAuth();
        $data = $this->readJsonBody();
        $this->validateData($data);
        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan.', ['id' => $id]);
    }

    public function update($id): void {
        Auth::requireAuth();
        if ($id === null || $id === '') {
            Response::json(400, false, 'Parameter id wajib diisi untuk memperbarui data.');
        }
        if (!$this->validId($id)) {
            Response::json(400, false, 'Parameter id harus berupa bilangan bulat positif.');
        }
        $id = (int) $id;
        if (!$this->model->find($id)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan.');
        }
        $data = $this->readJsonBody();
        $this->validateData($data);
        $this->model->update($id, $data);
        Response::json(200, true, 'Data berhasil diperbarui.', ['id' => $id]);
    }

    public function destroy($id): void {
        Auth::requireAuth();
        if ($id === null || $id === '') {
            Response::json(400, false, 'Parameter id wajib diisi untuk menghapus data.');
        }
        if (!$this->validId($id)) {
            Response::json(400, false, 'Parameter id harus berupa bilangan bulat positif.');
        }
        $id = (int) $id;
        if (!$this->model->find($id)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan.');
        }
        $this->model->delete($id);
        Response::json(200, true, 'Data berhasil dihapus.', ['id' => $id]);
    }

    private function readJsonBody(): array {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body harus berisi JSON yang valid.');
        }
        return $data;
    }

    private function validateData(array $data): void {
        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi.');
        }
        if (!is_string($data['nim']) && !is_numeric($data['nim'])) {
            Response::json(422, false, 'Format NIM tidak valid, harus 9 digit.');
        }
        if (!preg_match('/^[0-9]{9}$/', (string) $data['nim'])) {
            Response::json(422, false, 'Format NIM tidak valid, harus 9 digit.');
        }
        foreach (['nama', 'prodi'] as $field) {
            if (!is_string($data[$field]) || trim($data[$field]) === '') {
                Response::json(422, false, 'Field nama dan prodi harus berupa teks yang tidak kosong.');
            }
        }
        $data['nim'] = (string) $data['nim'];
    }

    private function validId($id): bool {
        return (is_int($id) || is_string($id)) && preg_match('/^[1-9][0-9]*$/', (string) $id) === 1;
    }
}
