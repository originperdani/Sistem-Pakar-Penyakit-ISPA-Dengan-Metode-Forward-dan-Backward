# Sistem Pakar Diagnosa ISPA (Forward Chaining)

Proyek ini menggunakan PHP (PDO) dan MySQL.

## Struktur

- `schema.sql` — skema dan seed data penyakit, gejala, dan rules.
- `config.php` — koneksi database (ubah user/password sesuai XAMPP/Laragon).
- `index.php` — form pilih gejala.
- `diagnose.php` — mesin forward chaining dan hasil.
- `style.css` — gaya UI sederhana.
- `demo.html` — demo client-side untuk preview tanpa server.

## Setup

1. Jalankan MySQL (XAMPP/Laragon), buka phpMyAdmin.
2. Import `schema.sql` untuk membuat database `ispa_db`.
3. Pastikan `config.php` sesuai:
   - Host: `localhost`
   - DB: `ispa_db`
   - User: `root`
   - Password: kosong (default XAMPP) atau isi sesuai konfigurasi.
4. Jalankan server PHP di folder `ISPA-Expert` (contoh: XAMPP `htdocs`):
   - Salin folder `ISPA-Expert` ke `C:/xampp/htdocs/ISPA-Expert`.
   - Akses `http://localhost/ISPA-Expert/index.php`.

## Cara Pakai

1. Centang gejala yang dialami.
2. Klik `Diagnosa`.
3. Jika gejala cocok penuh dengan suatu aturan, penyakit ditampilkan.
4. Jika belum cocok penuh, ditampilkan 3 kemungkinan teratas dengan persentase coverage dan gejala yang masih kurang.

## Catatan

Hasil diagnosa adalah alat bantu dan bukan pengganti pemeriksaan dokter.