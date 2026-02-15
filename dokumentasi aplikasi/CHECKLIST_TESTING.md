# Checklist Testing Manual

Jalankan ini setiap selesai perubahan besar sebelum dianggap aman.

## A. Smoke Check Dasar

- [ ] `php artisan view:clear`
- [ ] `php artisan view:cache`
- [ ] `php artisan test` (minimal semua test existing pass)
- [ ] login admin berhasil
- [ ] login kasir berhasil
- [ ] logout berhasil

## B. Order Flow

- [ ] tambah order baru berhasil
- [ ] edit order berhasil
- [ ] hapus order berhasil
- [ ] masuk halaman detail order berhasil
- [ ] tambah item order berhasil
- [ ] edit item order berhasil
- [ ] hapus item order berhasil
- [ ] hitung total item sesuai

## C. Pembayaran

- [ ] bayar dengan nominal pas berhasil
- [ ] bayar dengan diskon `0` berhasil
- [ ] input nominal format rupiah (`1.000.000`) terbaca benar
- [ ] jika nominal kurang, validasi muncul
- [ ] setelah bayar, status jadi "Dibayar"

## D. Laporan

Untuk setiap report:
- [ ] data tampil
- [ ] filter tanggal bekerja
- [ ] filter kios bekerja (termasuk opsi `all`)
- [ ] tidak ada warning DataTable
- [ ] export CSV berhasil download

Report yang dicek:
- [ ] Pendapatan Detail
- [ ] Pendapatan Kantin Detail
- [ ] Pendapatan Toko Detail
- [ ] Rekap Toko
- [ ] Rekap RS
- [ ] Rekap Kantin
- [ ] Rekap Keuangan
- [ ] Rekap Keuangan Menu

## E. UI/UX

- [ ] sidebar minimize/maximize bekerja
- [ ] sidebar mobile buka/tutup + backdrop bekerja
- [ ] navbar rapi di desktop dan mobile
- [ ] modal edit tampil konsisten
- [ ] tombol aksi tabel rapi dan konsisten
- [ ] badge status tampil konsisten

## F. Home Dashboard

- [ ] card KPI tampil benar
- [ ] chart harian tampil (atau fallback empty state tampil benar)
- [ ] chart mingguan tampil (atau fallback empty state tampil benar)
- [ ] tombol quick action berjalan

## G. Regression Cepat Legacy Bridge

- [ ] route lama utama redirect ke route Laravel yang benar
- [ ] asset legacy yang masih dipakai tetap bisa di-load

