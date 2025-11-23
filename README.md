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

## Backward Chaining

Selain forward chaining, sistem ini juga menyediakan metode diagnosa menggunakan backward chaining. Berikut penjelasan singkat dan cara menggunakannya:

1. Pengguna memilih penyakit yang ingin diperiksa pada halaman **Backward Chaining – Pilih Penyakit** (`backward_fc_list.php`).
2. Sistem kemudian akan menampilkan serangkaian pertanyaan gejala yang berhubungan dengan penyakit tersebut.
3. Pada setiap pertanyaan, pengguna menjawab dengan memilih "iyaa" jika mengalami gejala tersebut atau "tidak" jika tidak.
4. Setelah menjawab semua pertanyaan, sistem akan menampilkan hasil:
   - Jika semua gejala yang ditanyakan cocok (jawaban "iyaa"), penyakit tersebut dikonfirmasi sesuai.
   - Jika tidak semua cocok, sistem akan menampilkan penyakit dengan tingkat kecocokan terbaik berdasarkan persentase kecocokan gejala yang dijawab "iyaa".
   - Hasil juga menunjukkan gejala yang cocok dan gejala yang kurang sebagai referensi.
5. Pengguna dapat kembali memilih penyakit lain untuk diperiksa kembali jika diinginkan.

Backward chaining ini melengkapi metode forward chaining dengan pendekatan diagnostik berbasis tujuan (goal-driven) yang berfokus pada pengecekan penyakit tertentu dan gejala terkaitnya secara berurutan.
