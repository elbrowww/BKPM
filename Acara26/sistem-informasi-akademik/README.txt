SISTEM INFORMASI AKADEMIK - ACARA 26
Upload Foto Mahasiswa, Validasi Berkas, dan Backup/Restore Database

A. INSTALASI DI LARAGON
1. Ekstrak folder Acara26 ke C:\laragon\www\BKPM\Acara26\
   Hasilnya: C:\laragon\www\BKPM\Acara26\sistem-informasi-akademik\
2. Jalankan Laragon: Start All (Apache dan MySQL).
3. Buka app/config/config.php. Konfigurasi default memakai database si_akademik24,
   user root, dan password kosong. Database yang sama mempertahankan akun dan data
   Acara 25. Jika ingin database terpisah, ubah db_name sebelum membuka API.
4. Saat API pertama kali dipanggil, aplikasi otomatis membuat kolom foto jika belum
   tersedia. Kolom foto bertipe VARCHAR(255) dan boleh NULL.
5. Buka halaman login:
   http://localhost/BKPM/Acara26/sistem-informasi-akademik/public/views/login.html
6. Login admin: admin / admin123. Login user: user / user123.

B. FITUR ACARA 26
1. Kolom mahasiswa.foto menyimpan nama file foto, bukan isi file gambar.
2. File gambar disimpan pada public/uploads/ dan dapat diakses browser.
3. Format yang diterima hanya gambar JPG/JPEG dan PNG yang lolos pemeriksaan gambar.
4. Ukuran maksimal foto 2 MB. File yang tidak sesuai ditolak dengan HTTP 422.
5. Form tambah/ubah menggunakan FormData (multipart/form-data), bukan JSON.
6. Saat update tanpa memilih foto baru, foto lama tetap digunakan.
7. Saat update dengan foto baru, file lama dihapus menggunakan unlink() setelah
   perubahan database berhasil. Foto juga dihapus ketika data mahasiswa dihapus.
8. Endpoint POST, PUT, dan DELETE tetap dilindungi Auth::requireAdmin().
9. Direktori uploads memiliki .htaccess untuk mencegah file PHP dieksekusi.

C. ENDPOINT API
GET    /mahasiswa          Melihat daftar mahasiswa dan nama file foto
POST   /mahasiswa          Tambah mahasiswa (multipart/form-data, admin)
POST   /mahasiswa/{id}     Update mahasiswa dengan field _method=PUT (admin)
DELETE /mahasiswa/{id}     Hapus mahasiswa dan fotonya (admin)

D. PENGUJIAN UPLOAD DARI DASHBOARD
1. Login dengan akun admin.
2. Klik + Tambah Mahasiswa.
3. Isi NIM, Nama, Program Studi, pilih foto JPG/PNG maksimal 2MB, lalu Simpan.
4. Foto akan tampil di kolom Foto. Periksa nama file pada database mahasiswa.foto.
5. Klik Ubah, pilih foto lain, lalu Simpan. Pastikan foto baru tampil dan file foto
   lama sudah tidak ada di public/uploads.
6. Ubah data tanpa memilih foto baru. Pastikan foto lama tetap tampil.
7. Login sebagai user. User hanya dapat melihat data, tidak dapat menambah/mengubah/
   menghapus data.

E. PENGUJIAN CURL (POWERSHELL)
$base = "http://localhost/BKPM/Acara26/sistem-informasi-akademik/public"

1. Login admin dan salin data.token dari respons:
$body = '{"username":"admin","password":"admin123"}'
[System.IO.File]::WriteAllText("$PWD\login-admin.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/login" -H "Content-Type: application/json" --data-binary "@login-admin.json"
$token_admin = "TEMPEL_TOKEN_ADMIN_DI_SINI"

2. Upload foto + data mahasiswa (ganti path foto):
curl.exe -i -X POST "$base/mahasiswa" -H "Authorization: Bearer $token_admin" -F "nim=220100199" -F "nama=Mahasiswa Upload" -F "prodi=Teknik Informatika" -F "foto=@C:\path\foto.jpg"

3. Lihat data dan nama file foto:
curl.exe -i "$base/mahasiswa"

4. Update data tanpa mengganti foto (multipart POST + _method=PUT):
curl.exe -i -X POST "$base/mahasiswa/ID" -H "Authorization: Bearer $token_admin" -F "_method=PUT" -F "nim=220100199" -F "nama=Nama Diperbarui" -F "prodi=Sistem Informasi"

5. Update sekaligus mengganti foto:
curl.exe -i -X POST "$base/mahasiswa/ID" -H "Authorization: Bearer $token_admin" -F "_method=PUT" -F "nim=220100199" -F "nama=Nama Diperbarui" -F "prodi=Sistem Informasi" -F "foto=@C:\path\foto-baru.png"

6. Uji file terlalu besar / bukan gambar. Harus ditolak HTTP 422.
7. Uji token user pada POST atau update. Harus ditolak HTTP 403.

Catatan: ganti ID dengan id mahasiswa yang benar. Jangan menambahkan header
Content-Type secara manual ketika memakai curl -F; curl membuat boundary multipart.

F. BACKUP DAN RESTORE DATABASE (TUGAS LANGKAH 6)
BACKUP:
1. Buka http://localhost/phpmyadmin.
2. Pilih database si_akademik24 (atau nama db_name pada config.php).
3. Klik Export, pilih Quick dan format SQL, lalu Go/Export.
4. Simpan file, misalnya backup_si_akademik_acara26.sql. Simpan juga folder
   sistem-informasi-akademik/public/uploads agar file foto ikut memiliki cadangan.

RESTORE:
1. Buat database percobaan baru melalui tab Databases di phpMyAdmin.
2. Pilih database percobaan, klik Import, pilih backup SQL, lalu Go.
3. Periksa tabel users dan mahasiswa serta beberapa record.
4. Salin folder uploads dari backup ke public/uploads pada aplikasi yang diuji.
5. Pastikan kolom mahasiswa.foto dan file foto yang dirujuk tersedia.

G. CATATAN KONFIGURASI UPLOAD
Jika file 2MB gagal sebelum mencapai controller, periksa php.ini Laragon:
upload_max_filesize = 4M
post_max_size = 8M
Lalu restart Apache. Nilai server dibuat lebih besar dari batas aplikasi 2MB agar
validasi ukuran dapat dijalankan oleh controller.
