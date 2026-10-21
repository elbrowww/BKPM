ACARA 19 - REGISTRASI USER (MVC)
================================

STRUKTUR
- public/index.php             Front Controller
- app/core/Router.php          Route /register
- app/core/Response.php        Response JSON
- app/models/User.php          Model users
- app/controllers/AuthController.php
- db_workshop.sql              SQL mahasiswa + users

PERSIAPAN
1. Ekstrak folder si-akademik ke C:\laragon\www\sistem-informasi-akademik
   sehingga URL-nya menjadi http://localhost/sistem-informasi-akademik/public/register
2. Periksa app/config/Database.php. Database default pada file ini adalah `si-akademik`,
   username MySQL `root`, password kosong. Sesuaikan dengan konfigurasi Laragon Anda.
3. Impor db_workshop.sql melalui phpMyAdmin jika database/tabel belum tersedia.
   CATATAN: SQL membuat/memilih database `db_workshop`, sedangkan konfigurasi PHP
   default `si-akademik`. Jika menggunakan database `db_workshop`, ubah $db pada
   app/config/Database.php menjadi 'db_workshop'. Jika database si-akademik sudah
   dipakai Acara 17/18, jalankan hanya blok CREATE TABLE users di database si-akademik.
4. Pastikan Apache dan MySQL aktif di Laragon.

UJI DENGAN POWERSHELL / curl.exe
$base = "http://localhost/sistem-informasi-akademik/public"

POST registrasi:
$body = '{"username":"andi_test","email":"andi@example.com","password":"Rahasia123!"}'
[System.IO.File]::WriteAllText("$PWD\body.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/register" -H "Content-Type: application/json" --data-binary "@body.json"

POST duplikat: jalankan perintah POST yang sama lagi (422)
POST kosong:
$body = '{}'
[System.IO.File]::WriteAllText("$PWD\body.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/register" -H "Content-Type: application/json" --data-binary "@body.json"

POST email salah:
$body = '{"username":"uji_email","email":"bukan-email","password":"Rahasia123!"}'
[System.IO.File]::WriteAllText("$PWD\body.json", $body, [System.Text.UTF8Encoding]::new($false))
curl.exe -i -X POST "$base/register" -H "Content-Type: application/json" --data-binary "@body.json"

HASIL YANG DIHARAPKAN
- Registrasi berhasil: 201 Created
- JSON tidak valid / field wajib kosong: 400 Bad Request
- Email tidak valid, password terlalu pendek, username duplikat: 422 Unprocessable Content
- GET /register atau method selain POST: 405 Method Not Allowed

Password disimpan menggunakan password_hash(), bukan sebagai teks biasa.
