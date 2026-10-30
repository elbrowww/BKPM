SISTEM INFORMASI AKADEMIK - ACARA 25
Login, Dashboard Admin, Role-Based Access Control (RBAC)

A. INSTALASI DI LARAGON
1. Ekstrak folder "sistem-informasi-akademik" ke C:\laragon\www\BKPM\Acara25\
   (boleh memakai folder lain, sesuaikan URL di browser).
2. Jalankan Laragon: Start All (Apache dan MySQL).
3. Periksa app/config/config.php. Konfigurasi default Laragon:
   db_host = localhost, db_name = si_akademik24, db_user = root, db_pass = kosong.
4. Buka URL API berikut:
   http://localhost/BKPM/Acara25/sistem-informasi-akademik/public/
   Database, tabel users dan mahasiswa, akun awal, serta data contoh dibuat otomatis.
5. Buka halaman login:
   http://localhost/BKPM/Acara25/sistem-informasi-akademik/public/views/login.html

B. AKUN PENGUJIAN
Admin: username admin, password admin123 (boleh tambah/ubah/hapus mahasiswa).
User : username user,  password user123  (hanya dapat melihat data).
Registrasi publik melalui POST /register selalu membuat role user. Untuk keamanan,
role admin tidak dapat dipilih dari form registrasi; admin dibuat melalui seed database.

C. FITUR ACARA 25
1. Auth::requireAdmin() memanggil Auth::requireAuth() untuk memvalidasi Bearer token,
   lalu memeriksa payload role harus bernilai admin. Role lain menerima HTTP 403.
2. Endpoint POST, PUT, dan DELETE mahasiswa memakai Auth::requireAdmin().
3. Login menyimpan token, role, dan username ke localStorage.
4. Dashboard memeriksa token sebelum konten ditampilkan. Tombol Logout menghapus
   token, role, dan username dari localStorage lalu mengarahkan ke login.html.
5. Dashboard menampilkan total mahasiswa. Tombol tambah/ubah/hapus disembunyikan
   jika role yang tersimpan bukan admin. Ini hanya kontrol tampilan; backend tetap
   melakukan pemeriksaan role agar endpoint tidak bisa ditembus dengan mengubah UI.

D. ENDPOINT API
POST   /register          body: {"username":"nama","password":"minimal6"}
POST   /login             body: {"username":"admin","password":"admin123"}
GET    /mahasiswa         publik
POST   /mahasiswa         admin + Bearer token
PUT    /mahasiswa/{id}    admin + Bearer token
DELETE /mahasiswa/{id}    admin + Bearer token

E. PENGUJIAN RBAC DENGAN CURL (POWERSHELL)
$base = "http://localhost/BKPM/Acara25/sistem-informasi-akademik/public"

1. Login sebagai user, salin token dari data.token:
$body = '{"username":"user","password":"user123"}'
[System.IO.File]::WriteAllText("$PWD\login-user.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/login" -H "Content-Type: application/json" --data-binary "@login-user.json"

2. Login sebagai admin, salin token dari data.token:
$body = '{"username":"admin","password":"admin123"}'
[System.IO.File]::WriteAllText("$PWD\login-admin.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/login" -H "Content-Type: application/json" --data-binary "@login-admin.json"
$token_admin = "TEMPEL_TOKEN_ADMIN_DI_SINI"
$token_user = "TEMPEL_TOKEN_USER_DI_SINI"

3. GET mahasiswa (dapat diakses tanpa login):
curl.exe -i "$base/mahasiswa"

4. Coba tambah data dengan token USER (harus HTTP 403):
$body = '{"nim":"220100199","nama":"Uji Role User","prodi":"Teknik Informatika"}'
[System.IO.File]::WriteAllText("$PWD\mhs.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/mahasiswa" -H "Content-Type: application/json" -H "Authorization: Bearer $token_user" --data-binary "@mhs.json"

5. Tambah data dengan token ADMIN (harus HTTP 201):
curl.exe -i -X POST "$base/mahasiswa" -H "Content-Type: application/json" -H "Authorization: Bearer $token_admin" --data-binary "@mhs.json"

6. Uji PUT dan DELETE dengan token admin (ganti ID sesuai hasil GET/POST):
$id = 1
$body = '{"nim":"220100101","nama":"Budi Santoso Update","prodi":"Sistem Informasi"}'
[System.IO.File]::WriteAllText("$PWD\mhs-update.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X PUT "$base/mahasiswa/$id" -H "Content-Type: application/json" -H "Authorization: Bearer $token_admin" --data-binary "@mhs-update.json"
curl.exe -i -X DELETE "$base/mahasiswa/$id" -H "Authorization: Bearer $token_admin"

7. Uji tanpa token (POST/PUT/DELETE harus HTTP 401):
curl.exe -i -X POST "$base/mahasiswa" -H "Content-Type: application/json" --data-binary "@mhs.json"

F. CATATAN
- Token berlaku sesuai token_ttl pada app/config/config.php (default 7200 detik / 2 jam).
- Ubah secret dan kredensial database sebelum dipakai di lingkungan produksi.
- Jika URL berbeda, sesuaikan nilai $base pada contoh perintah curl.
- Jika Apache tidak meneruskan Authorization, pastikan .htaccess aktif dan
  AllowOverride All di konfigurasi virtual host Apache.
