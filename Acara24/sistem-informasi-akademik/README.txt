SISTEM INFORMASI AKADEMIK - Acara 24 + 25 (lengkap)

CARA PAKAI (Laragon)
1. Ekstrak folder "sistem-informasi-akademik" ke C:\laragon\www\ (atau subfolder, mis. BKPM\Acara24\).
2. Start All di Laragon (Apache + MySQL). Pastikan mod_rewrite aktif (default aktif).
3. Cek konfigurasi database di app/config/config.php (default Laragon: root, tanpa password).
4. Buka: http://localhost/<path>/sistem-informasi-akademik/public/
   -> harus muncul JSON "API Sistem Informasi Akademik berjalan".
   Database "sia_akademik", tabel, dan data awal DIBUAT OTOMATIS saat pertama kali API dipanggil.
5. Acara 24: .../public/views/index.html
   Acara 25: .../public/views/login.html

AKUN AWAL
  admin / admin123  (role admin: boleh tambah/ubah/hapus)
  user  / user123   (role user : hanya lihat)

ENDPOINT
  GET    /mahasiswa        publik
  POST   /mahasiswa        admin
  PUT    /mahasiswa/{id}   admin
  DELETE /mahasiswa/{id}   admin
  POST   /login            {username, password}
  POST   /register         {username, password}  -> role user

JIKA ERROR 404 PADA /mahasiswa (bukan di index.php): pastikan .htaccess di folder public/ terbaca
(AllowOverride All). Jika memakai Nginx, tambahkan: try_files $uri $uri/ /index.php?$query_string;
