# Dokumentasi API — Sistem Informasi Akademik

Base URL produksi: `https://elbrowww.my.id`

Semua respons berformat JSON:

```json
{ "success": true, "message": "teks pesan", "data": null }
```

Autentikasi memakai token Bearer (HMAC-SHA256, berlaku 2 jam). Kirim header `Authorization: Bearer <token>` pada endpoint yang bertanda **Admin**.

## Ringkasan Endpoint

| Method | Endpoint | Akses | Keterangan |
|--------|----------|-------|------------|
| GET | `/` | Publik | Cek API berjalan |
| POST | `/login` | Publik | Login, menghasilkan token |
| POST | `/register` | Publik | Registrasi akun (role selalu `user`) |
| GET | `/mahasiswa` | Publik | Daftar mahasiswa |
| GET | `/mahasiswa?id=N` | Publik | Satu data mahasiswa |
| POST | `/mahasiswa` | Admin | Tambah mahasiswa (multipart/form-data) |
| PUT | `/mahasiswa/{id}` | Admin | Ubah mahasiswa. Dari browser: `POST /mahasiswa/{id}` dengan field `_method=PUT` |
| DELETE | `/mahasiswa/{id}` | Admin | Hapus mahasiswa beserta fotonya |

## Kode Status

| Kode | Arti |
|------|------|
| 200 | Berhasil |
| 201 | Data dibuat |
| 401 | Token tidak ada, tidak valid, kedaluwarsa, atau login salah |
| 403 | Bukan admin |
| 404 | Data atau route tidak ditemukan |
| 409 | Username sudah dipakai (register) |
| 422 | Validasi gagal (field kosong, NIM ganda, id bukan bilangan bulat positif, foto tidak valid) |
| 500 | Kesalahan server (mis. koneksi database) |

## POST /login

Body JSON:

```json
{ "username": "admin", "password": "********" }
```

| Status | Message |
|--------|---------|
| 200 | `Login berhasil` — `data`: `token`, `role`, `username` |
| 401 | `Username atau password salah` |
| 422 | `Username dan password wajib diisi` |

## POST /register

Body JSON: `username` (wajib), `password` (minimal 6 karakter).

| Status | Message |
|--------|---------|
| 201 | `Registrasi berhasil` |
| 409 | `Username sudah dipakai` |
| 422 | `Username wajib diisi dan password minimal 6 karakter` |

## GET /mahasiswa

Tanpa parameter mengembalikan daftar, diurutkan menurut NIM. Dengan `?id=N` mengembalikan satu data.

Contoh `data` (daftar):

```json
[{ "id": 1, "nim": "220100101", "nama": "Nama", "prodi": "Teknik Informatika", "foto": "abc123.jpg" }]
```

| Status | Message |
|--------|---------|
| 200 | `Data mahasiswa` |
| 404 | `Data tidak ditemukan` (hanya dengan `?id=N`) |

Foto diakses di `https://elbrowww.my.id/uploads/<nama-file>`. Nilai `foto` bisa `null`.

## POST /mahasiswa (Admin)

Body `multipart/form-data`: `nim`, `nama`, `prodi` (wajib), `foto` (opsional, JPG/PNG, maksimal 2 MB). Jangan set header `Content-Type` secara manual.

| Status | Message |
|--------|---------|
| 201 | `Mahasiswa ditambahkan` — `data`: `id`, `foto` |
| 401 | Token tidak ada atau tidak valid |
| 403 | `Akses ditolak, hanya admin yang diizinkan` |
| 422 | `NIM, nama, dan prodi wajib diisi`, `NIM sudah terdaftar`, `Ukuran berkas maksimal 2MB`, `Berkas harus berupa gambar JPG/PNG yang valid` |

## PUT /mahasiswa/{id} (Admin)

Field sama seperti tambah. Bila `foto` tidak dikirim, foto lama dipertahankan. Foto lama dihapus setelah data baru tersimpan. Dari browser, kirim `POST /mahasiswa/{id}` dengan field tambahan `_method=PUT` agar PHP mengisi `$_FILES`.

| Status | Message |
|--------|---------|
| 200 | `Mahasiswa diperbarui` — `data`: `foto` |
| 401, 403 | Sama seperti POST |
| 404 | `Data tidak ditemukan` |
| 422 | `Parameter id harus berupa bilangan bulat positif`, `NIM, nama, dan prodi wajib diisi`, `NIM sudah dipakai mahasiswa lain`, error foto |

## DELETE /mahasiswa/{id} (Admin)

| Status | Message |
|--------|---------|
| 200 | `Mahasiswa dihapus` |
| 401, 403 | Sama seperti POST |
| 404 | `Data tidak ditemukan` |
| 422 | `Parameter id harus berupa bilangan bulat positif` |

## Contoh curl (PowerShell)

```powershell
$base = "https://elbrowww.my.id"

# Login
curl.exe -i -X POST "$base/login" -H "Content-Type: application/json" --data-binary "@login.json"

# Daftar mahasiswa
curl.exe -i "$base/mahasiswa"

# Tambah mahasiswa
curl.exe -i -X POST "$base/mahasiswa" -H "Authorization: Bearer $token" -F "nim=220100199" -F "nama=Contoh" -F "prodi=Teknik Informatika"

# Hapus mahasiswa
curl.exe -i -X DELETE "$base/mahasiswa/ID" -H "Authorization: Bearer $token"
```
