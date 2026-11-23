PANDUAN MENJALANKAN UTS PHP NATIVE

A. Persiapan
1. Instal dan buka XAMPP.
2. Jalankan Apache dari XAMPP Control Panel.
3. Ekstrak folder uts-php-native ke C:\xampp\htdocs\
   Pastikan hasil akhirnya:
   C:\xampp\htdocs\uts-php-native\index.php
   C:\xampp\htdocs\uts-php-native\functions.php
   C:\xampp\htdocs\uts-php-native\style.css

B. Menjalankan
1. Buka browser.
2. Ketik http://localhost/uts-php-native/
3. Halaman aplikasi kasir akan tampil.
4. Coba tambah transaksi, masukkan nama kurang dari 3 karakter untuk menguji validasi,
   lalu coba filter layanan dan pencarian nama.

C. Isi Folder
- index.php     : tampilan aplikasi, seed data, proses POST, validasi, filter GET.
- functions.php : empat fungsi modular sesuai worksheet.
- style.css     : desain responsif tanpa JavaScript.
- JAWABAN_BAGIAN_I.txt : jawaban lima soal analisis.

D. Catatan
- Aplikasi menggunakan PHP Native 8.x.
- Tidak menggunakan database/MySQL karena worksheet meminta seed data array.
- Data transaksi disimpan dalam session browser. Jika session dihapus atau browser/session
  berakhir, data kembali ke lima seed data awal.
- Tidak ada tag script, file .js, onclick, maupun onsubmit.
- Screenshot pengujian diminta worksheet dibuat dari browser setelah aplikasi dijalankan.
