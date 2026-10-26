# API Documentation — Sistem Informasi Akademik

Dokumentasi ini disusun berdasarkan seluruh Controller yang telah dibangun:
`AuthController` (register, login) dan `MahasiswaController` (index, store, update, destroy).

## Struktur Folder

```
sistem-informasi-akademik/
├── ...(app/ dan public/)
└── docs/
    └── API_DOCUMENTATION.md
```

## Peta Router (Ringkasan Seluruh Endpoint)

| Method | Resource (URL)    | Controller                     | Auth         | Keterangan                              |
|--------|-------------------|--------------------------------|--------------|-----------------------------------------|
| POST   | `/register`       | `AuthController::register`    | Tidak        | Pendaftaran pengguna baru               |
| POST   | `/login`          | `AuthController::login`        | Tidak        | Autentikasi pengguna, menghasilkan token|
| GET    | `/mahasiswa`      | `MahasiswaController::index`   | Tidak*       | Menampilkan seluruh data mahasiswa      |
| POST   | `/mahasiswa`      | `MahasiswaController::store`   | Bearer Token | Menambah data mahasiswa                 |
| PUT    | `/mahasiswa/{id}` | `MahasiswaController::update`  | Bearer Token | Mengubah data mahasiswa                 |
| DELETE | `/mahasiswa/{id}` | `MahasiswaController::destroy` | Bearer Token | Menghapus data mahasiswa                |

\* Sesuaikan jika `index` pada proyek Anda juga memanggil `Auth::requireAuth()`.

## Format Respons Umum

```json
{ "success": true, "message": "...", "data": { } }
```

Untuk kegagalan, `success` bernilai `false` dan `message` berisi alasan kegagalan.

---

## 1. Autentikasi

### POST /register
Controller: `AuthController::register()`

**Headers:** `Content-Type: application/json`

**Request Body:**
```json
{ "username": "admin", "password": "rahasia123" }
```

**Response 201 Created:**
```json
{ "success": true, "message": "Registrasi berhasil", "data": { "id": 1, "username": "admin" } }
```

**Response 422 Unprocessable Entity:**
```json
{ "success": false, "message": "Username sudah digunakan", "data": null }
```

---

### POST /login
Controller: `AuthController::login()`

**Headers:** `Content-Type: application/json`

**Request Body:**
```json
{ "username": "admin", "password": "rahasia123" }
```

**Response 200 OK:**
```json
{ "success": true, "message": "Login berhasil", "data": { "token": "..." } }
```

**Response 401 Unauthorized:**
```json
{ "success": false, "message": "Username atau password salah", "data": null }
```

---

## 2. Mahasiswa

> Endpoint `store`, `update`, dan `destroy` memerlukan header
> `Authorization: Bearer <token>` (`Auth::requireAuth()`).
> Token diperoleh dari `POST /login`.

### GET /mahasiswa
Controller: `MahasiswaController::index()`

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Data mahasiswa berhasil diambil",
  "data": [
    { "id": 1, "nim": "230001", "nama": "Budi Santoso", "jurusan": "Informatika" }
  ]
}
```

---

### POST /mahasiswa
Controller: `MahasiswaController::store()`

**Headers:**
`Content-Type: application/json`
`Authorization: Bearer <token>`

**Request Body:**
```json
{ "nim": "230001", "nama": "Budi Santoso", "jurusan": "Informatika" }
```

**Response 201 Created:**
```json
{ "success": true, "message": "Data mahasiswa berhasil ditambahkan", "data": { "id": 1 } }
```

**Response 401 Unauthorized:**
```json
{ "success": false, "message": "Token tidak valid atau tidak ada", "data": null }
```

---

### PUT /mahasiswa/{id}
Controller: `MahasiswaController::update()`

**Headers:**
`Content-Type: application/json`
`Authorization: Bearer <token>`

**Request Body:**
```json
{ "nim": "230001", "nama": "Budi Santoso, S.Kom", "jurusan": "Informatika" }
```

**Response 200 OK:**
```json
{ "success": true, "message": "Data mahasiswa berhasil diperbarui", "data": { "id": 1 } }
```

**Response 404 Not Found:**
```json
{ "success": false, "message": "Data mahasiswa tidak ditemukan", "data": null }
```

---

### DELETE /mahasiswa/{id}
Controller: `MahasiswaController::destroy()`

**Headers:** `Authorization: Bearer <token>`

**Response 200 OK:**
```json
{ "success": true, "message": "Data mahasiswa berhasil dihapus", "data": null }
```

**Response 404 Not Found:**
```json
{ "success": false, "message": "Data mahasiswa tidak ditemukan", "data": null }
```
