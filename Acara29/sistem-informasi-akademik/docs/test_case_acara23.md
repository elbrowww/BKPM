# Dokumen Test Case — API Sistem Informasi Akademik

Pengujian dilakukan terhadap `AuthController` dan `MahasiswaController` pada project Acara 27 (basis untuk deployment Acara 28). Metode: pengujian manual black-box dengan teknik Equivalence Partitioning. Hasil di bawah adalah hasil retest setelah perbaikan.

## 1. Ruang Lingkup

| No | Method | Endpoint | Controller |
|----|--------|----------|------------|
| 1 | POST | `/register` | `AuthController::register` |
| 2 | POST | `/login` | `AuthController::login` |
| 3 | GET | `/mahasiswa` | `MahasiswaController::index` |
| 4 | POST | `/mahasiswa` | `MahasiswaController::store` |
| 5 | PUT | `/mahasiswa/{id}` | `MahasiswaController::update` |
| 6 | DELETE | `/mahasiswa/{id}` | `MahasiswaController::destroy` |

## 2. Prasyarat

- Apache dan MySQL berjalan, database `si_akademik27` terbuat otomatis saat API pertama dipanggil.
- Akun admin tersedia (dibuat otomatis dari konfigurasi seed). Login admin untuk mendapatkan token.
- Data mahasiswa dengan `id=1` ada, `id=9999` tidak ada.
- NIM `220100102` dimiliki mahasiswa lain (untuk uji duplikat).
- Header untuk request terproteksi: `Authorization: Bearer <token>`.

## 3. Test Case Dasar (store dan autentikasi)

| ID | Skenario | Partisi | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|----|----------|---------|----------|------------------|--------------|--------|
| TC-01 | Store NIM valid dan lengkap | Valid | `nim=220100123`, nama, prodi, token admin | 201 Created | 201 Created | Pass |
| TC-02 | Store NIM kosong | Tidak valid | `nim=""` | 400/422 | 422 Unprocessable Entity | Pass |
| TC-03 | Store NIM duplikat | Tidak valid | `nim=220100102` (milik data lain) | 422 | 422 Unprocessable Entity | Pass |
| TC-04 | Login kredensial benar | Valid | username dan password benar | 200 OK, token diterima | 200 OK, token diterima | Pass |
| TC-05 | Login kredensial salah | Tidak valid | username benar, password salah | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-06 | Store tanpa token | Tidak valid | tanpa header `Authorization` | 401 Unauthorized | 401 Unauthorized | Pass |

## 4. Test Case Tambahan (update dan destroy)

| ID | Skenario | Partisi | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|----|----------|---------|----------|------------------|--------------|--------|
| TC-07 | Update data valid dengan token | Valid | `PUT /mahasiswa/1`, nama baru, token valid | 200 OK | 200 OK | Pass |
| TC-08 | Update dengan ID tidak ada | Tidak valid | `PUT /mahasiswa/9999`, token valid | 404 Not Found | 404 Not Found | Pass |
| TC-09 | Update dengan NIM kosong | Tidak valid | `PUT /mahasiswa/1`, `nim=""`, token valid | 400/422 | 422 Unprocessable Entity | Pass |
| TC-10 | Update tanpa token | Tidak valid | `PUT /mahasiswa/1`, tanpa header | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-11 | Update dengan NIM milik mahasiswa lain | Tidak valid | `PUT /mahasiswa/1`, `nim=220100102`, token valid | 422 | 422 Unprocessable Entity | Pass |
| TC-12 | Hapus data valid dengan token | Valid | `DELETE /mahasiswa/1`, token valid | 200 OK | 200 OK | Pass |
| TC-13 | Hapus dengan ID tidak ada | Tidak valid | `DELETE /mahasiswa/9999`, token valid | 404 Not Found | 404 Not Found | Pass |
| TC-14 | Hapus tanpa token | Tidak valid | `DELETE /mahasiswa/1`, tanpa header | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-15 | Hapus dengan ID bukan angka | Tidak valid | `DELETE /mahasiswa/abc`, token valid | 400/422 | 422 Unprocessable Entity | Pass |

Catatan urutan: jalankan TC-12 paling akhir karena menghapus data `id=1` yang dipakai TC-07, TC-09, TC-10, dan TC-11.

## 5. Ringkasan Hasil

| Total Test Case | Pass | Fail |
|-----------------|------|------|
| 15 | 15 | 0 |

## 6. Perbaikan dan Retest

Pengujian awal menemukan 3 kegagalan. Hasil awal di bawah mengacu pada project Acara 27 sebelum diperbaiki.

| ID TC | Masalah yang Ditemukan | Lapisan | Perbaikan yang Dilakukan | Hasil Retest | Status Akhir |
|-------|------------------------|---------|--------------------------|--------------|--------------|
| TC-03 | Store menerima NIM duplikat dengan respons 409, bukan 422 | Controller | Respons duplikat NIM pada `store` diubah menjadi 422 | 422 | Pass |
| TC-08 | Update dengan ID tidak ada belum mengembalikan 404 (laporan awal: 400) | Controller | Controller `update` memeriksa data lewat `find()` dan mengembalikan 404 | 404 | Pass |
| TC-11 | Update dengan NIM milik mahasiswa lain menghasilkan 409, bukan 422 | Controller | Respons duplikat NIM pada `update` diubah menjadi 422 | 422 | Pass |
| TC-15 | ID bukan angka diubah menjadi 0 lalu menghasilkan 404 | Controller | Method `validasiId()` pada `update` dan `destroy`: ID harus bilangan bulat positif, selain itu 422 | 422 | Pass |

Pada project Acara 27 ini, TC-08 sudah menghasilkan 404 tanpa perubahan kode. Kegagalan di laporan awal berasal dari versi kode Acara 17 sampai 20.

## 7. Checklist Pra-Deployment

| No | Item | Status |
|----|------|--------|
| 1 | Seluruh test case Acara 23 berstatus Pass (15/15) | Selesai |
| 2 | Tidak ada kredensial sensitif ter-hardcode di luar `app/config/` | Selesai |

Detail langkah 2:

- Password seed `admin123` dan `user123` dipindah dari `app/core/Database.php` ke `app/config/config.php`, dibaca dari environment variable `SEED_ADMIN_PASS` dan `SEED_USER_PASS`.
- Kredensial database dan kunci token dibaca dari `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, dan `APP_SECRET`.
- Hint akun pengujian pada `public/views/login.html` dihapus.
- Pesan error koneksi database tidak lagi menampilkan detail exception ke klien.

Tindakan wajib di server produksi: set `APP_SECRET` dan password seed sebelum API pertama kali dipanggil. Nilai default hanya untuk development lokal.

## 8. Bukti Pengujian

Sertakan screenshot Postman untuk tiap test case (TC-01 sampai TC-15) pada laporan. Skrip `uji_tc01-15.sh` dapat dipakai sebagai verifikasi cepat lewat `curl`.
