# Test Case API Sistem Informasi Akademik

Pengujian terhadap `AuthController` dan `MahasiswaController` (Acara 17 sampai 20).
Metode: pengujian manual dengan Postman, teknik Equivalence Partitioning (blackbox).

## 1. Ruang Lingkup

| No | Method | Endpoint | Controller |
|----|--------|----------|------------|
| 1 | POST | `/register` | `AuthController::register` |
| 2 | POST | `/login` | `AuthController::login` |
| 3 | GET | `/mahasiswa` | `MahasiswaController::index` |
| 4 | POST | `/mahasiswa` | `MahasiswaController::store` |
| 5 | PUT | `/mahasiswa` | `MahasiswaController::update` |
| 6 | DELETE | `/mahasiswa` | `MahasiswaController::destroy` |

## 2. Prasyarat

- Server berjalan dan variabel Postman `{{base_url}}` terisi.
- Login lewat TC-04 untuk mendapat token. Token berlaku 5 menit, login ulang jika respons 401.
- Data mahasiswa dengan `id=1` ada, `id=9999` tidak ada.
- Untuk TC-03 dan TC-11, ada data lain yang memakai NIM `220100123`.
- Jalankan TC-12 paling akhir karena menghapus data.

## 3. Test Case Dasar (store dan autentikasi)

| ID | Skenario | Data Uji | Hasil Diharapkan | Hasil Aktual (awal) | Status Awal | Hasil Retest | Status Akhir |
|----|----------|----------|------------------|---------------------|-------------|--------------|--------------|
| TC-01 | NIM valid dan lengkap (`store`) | `nim=220100123`, nama, prodi | 201 Created | 201 Created | Pass | - | Pass |
| TC-02 | NIM kosong | `nim=""` | 400/422 | 400 Bad Request | Pass | - | Pass |
| TC-03 | NIM duplikat | NIM milik data lain | 422 Unprocessable | 201 Created | **Fail** | 422 Unprocessable Entity, `NIM sudah terdaftar.` | Pass |
| TC-04 | Login kredensial benar (`login`) | username dan password benar | 200 OK, token diterima | 200 OK | Pass | - | Pass |
| TC-05 | Login kredensial salah | username dan password salah | 401 Unauthorized | 401 Unauthorized | Pass | - | Pass |
| TC-06 | Akses `store` tanpa token | tanpa header `Authorization` | 401 Unauthorized | 401 Unauthorized | Pass | - | Pass |

## 4. Test Case Tambahan (update dan destroy)

| ID | Skenario | Partisi | Data Uji | Hasil Diharapkan | Hasil Aktual (awal) | Status Awal | Hasil Retest | Status Akhir |
|----|----------|---------|----------|------------------|---------------------|-------------|--------------|--------------|
| TC-07 | Update data valid dengan token | Valid | `id=1`, nama baru, token valid | 200 OK | 200 OK | Pass | - | Pass |
| TC-08 | Update dengan ID tidak ada | Tidak valid | `id=9999`, token valid | 404 Not Found | 400 Bad Request | **Fail** | 404 Not Found, `Data mahasiswa tidak ditemukan.` | Pass |
| TC-09 | Update dengan NIM kosong | Tidak valid | `id=1`, `nim=""`, token valid | 400/422 | 400 Bad Request | Pass | - | Pass |
| TC-10 | Update tanpa token | Tidak valid | `id=1`, tanpa header `Authorization` | 401 Unauthorized | 401 Unauthorized | Pass | - | Pass |
| TC-11 | Update dengan NIM milik mahasiswa lain | Tidak valid | `id=1`, NIM milik id lain, token valid | 422 Unprocessable | 400 Bad Request | **Fail** | 422 Unprocessable Entity | Pass |
| TC-12 | Hapus data valid dengan token | Valid | `id=1`, token valid | 200 OK | 200 OK | Pass | - | Pass |
| TC-13 | Hapus dengan ID tidak ada | Tidak valid | `id=9999`, token valid | 404 Not Found | 404 Not Found | Pass | - | Pass |
| TC-14 | Hapus tanpa token | Tidak valid | `id=1`, tanpa header `Authorization` | 401 Unauthorized | 401 Unauthorized | Pass | - | Pass |
| TC-15 | Hapus dengan ID bukan angka | Tidak valid | `id=abc`, token valid | 400/422 | 400 Bad Request | Pass | - | Pass |

## 5. Perbaikan dan Retest

| ID TC | Masalah | Lapisan | Perbaikan | Hasil Retest | Status Akhir |
|-------|---------|---------|-----------|--------------|--------------|
| TC-03 | `store` menerima NIM duplikat | Model | Tambah `Mahasiswa::nimExists()`. `store()` membalas 422 jika NIM sudah terdaftar. | 422 Unprocessable Entity | Pass |
| TC-08 | Update dengan ID tidak ada belum mengembalikan 404 | Controller | `update()` membaca id dari query string atau body JSON. ID tidak ada dibalas 404 sebelum validasi data. | 404 Not Found | Pass |
| TC-11 | Update menerima NIM milik mahasiswa lain | Model | `update()` memakai `nimExists($nim, $id)`. NIM milik data lain dibalas 422, data sendiri dikecualikan. | 422 Unprocessable Entity | Pass |

## 6. Ringkasan Hasil

| | Total | Pass | Fail |
|---|-------|------|------|
| Pengujian awal | 15 | 12 | 3 |
| Setelah retest | 15 | 15 | 0 |
