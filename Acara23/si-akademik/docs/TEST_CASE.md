# Laporan Pengujian API — Sistem Informasi Akademik

Pengujian dilakukan menyeluruh terhadap seluruh Controller yang dibangun sejak acara 17 hingga 20 (`AuthController` dan `MahasiswaController`). Metode: pengujian manual menggunakan Postman dengan teknik **Equivalence Partitioning**.

## 1. Ruang Lingkup Pengujian

Endpoint yang diuji berdasarkan peta Router:

| No | Method | Endpoint     | Controller                     |
|----|--------|--------------|--------------------------------|
| 1  | POST   | `/register`  | `AuthController::register`     |
| 2  | POST   | `/login`     | `AuthController::login`        |
| 3  | GET    | `/mahasiswa` | `MahasiswaController::index`   |
| 4  | POST   | `/mahasiswa` | `MahasiswaController::store`   |
| 5  | PUT    | `/mahasiswa` | `MahasiswaController::update`  |
| 6  | DELETE | `/mahasiswa` | `MahasiswaController::destroy` |

## 2. Rancangan Test Case

> Kolom **Hasil Aktual** dan **Status** diisi setelah test case dijalankan di Postman.
> Status: **Pass** bila hasil aktual sama dengan hasil diharapkan, selain itu **Fail**.

### 2.1 Test Case Dasar (store dan autentikasi)

| ID    | Skenario                                          | Data Uji           | Hasil Diharapkan   | Hasil Aktual | Status |
|-------|---------------------------------------------------|--------------------|--------------------|--------------|--------|
| TC-01 | NIM valid dan lengkap (`MahasiswaController::store`) | nim=220100123 (+ nama, jurusan) | 201 Created |201 Created | Pass |
| TC-02 | NIM kosong (partisi tidak valid)                  | nim=""                          | 400/422   |400 Bad Request|Pass|
| TC-03 | NIM duplikat (partisi tidak valid)                | nim milik data lain             | 422 Unprocessable|201 Created|Fail|
| TC-04 | Login kredensial benar (`AuthController::login`)  | username & password benar       | 200 OK, token diterima |200 OK|Pass|
| TC-05 | Login kredensial salah                            | username & password salah   | 401 Unauthorized|401 Unauthorized|Pass|
| TC-06 | Akses store tanpa token (`Auth::requireAuth`)     | tanpa header Authorization  |401 Unauthorized|401 Unauthorized|Pass|

### 2.2 Test Case Tambahan (update dan destroy) — Tugas Mandiri 1

Prasyarat: sudah login dan memiliki token valid; data mahasiswa dengan `id=1` ada, `id=9999` tidak ada.

| ID    | Skenario                         | Partisi | Data Uji                  | Hasil Diharapkan  | Hasil Aktual | Status |
|-------|----------------------------------|---------|---------------------------|-------------------|--------------|--------|
| TC-07| Update data valid dengan token (`::update`)     | Valid| id=1, nama baru, token valid| 200 OK|          |        |
| TC-08| Update dengan ID tidak ada                   | Tidak valid    | id=9999, token valid| 404 Not Found      |        |
| TC-09| Update dengan NIM kosong                     | Tidak valid    | id=1, nim="", token valid| 400/422 |       |        |
| TC-10| Update tanpa token                    | Tidak valid    | id=1, tanpa header Authorization |401 Unauthorized|    |        |
| TC-11| Update dengan NIM milik mahasiswa lain (duplikat)| Tidak valid | id=1, nim milik id lain, token valid|422 Unprocessable|    |    |
| TC-12| Hapus data valid dengan token (`::destroy`)  | Valid  | id=1, token valid | 200 OK|              |        |
| TC-13| Hapus dengan ID tidak ada        | Tidak valid    | id=9999, token valid | 404 Not Found|      |        |
| TC-14| Hapus tanpa token          | Tidak valid    | id=1, tanpa header Authorization  | 401 Unauthorized |        |        |
| TC-15| Hapus dengan ID bukan angka | Tidak valid    | id=abc, token valid | 400/422 |              |        |

## 3. Eksekusi Pengujian Manual

1. Jalankan setiap test case di Postman sesuai tabel.
2. Catat hasil aktual beserta kode status HTTP yang benar-benar diterima ke kolom **Hasil Aktual**.
3. Isi kolom **Status** (Pass/Fail).

## 4. Ringkasan Hasil

| Total Test Case | Pass | Fail |
|-----------------|------|------|
| 15              |      |      |

## 5. Perbaikan dan Retest — Tugas Mandiri 2

Isi bagian ini bila ada test case berstatus Fail.

| ID TC | Masalah yang Ditemukan | Lapisan (Model/Controller) | Perbaikan yang Dilakukan | Hasil Retest | Status Akhir |
|-------|------------------------|----------------------------|--------------------------|--------------|--------------|
|       |                        |                            |                          |              |              |
