# Known Issues & Solusi

Dokumen masalah yang pernah muncul + cara penanganannya.

## 1) Warning DataTable muncul berulang

Gejala:
- popup warning DataTable,
- tabel terasa di-init lebih dari sekali.

Akar masalah umum:
- elemen tabel sudah di-init tapi di-init ulang,
- markup `tbody` tidak konsisten (misal `colspan` pada table DataTable).

Solusi yang dipakai:
- cek `DataTable.isDataTable()` sebelum init,
- hindari `colspan` di `tbody` untuk tabel dengan `.js-datatable`,
- arahkan errMode DataTable ke console agar tidak mengganggu user.

Lokasi terkait:
- `resources/views/app/layouts/main.blade.php`

## 2) Error pembayaran: "jumlah bayar tidak cukup" padahal nominal benar

Gejala:
- nominal bayar format rupiah tidak terbaca benar.

Akar masalah:
- parsing string angka (titik/koma/spasi) belum dinormalisasi.

Solusi:
- normalisasi input nominal bayar dan diskon di controller pembayaran.

Lokasi terkait:
- `app/Http/Controllers/KantinOrderController.php`

## 3) Sidebar mobile terasa sulit ditutup

Gejala:
- setelah buka sidebar di mobile, user bingung tutup.

Solusi:
- tambah backdrop,
- klik di luar sidebar menutup sidebar,
- transisi buka/tutup dibuat lebih halus.

Lokasi terkait:
- `resources/views/app/layouts/main.blade.php`

## 4) Inkonstensi style antar halaman

Gejala:
- modal, badge, tombol aksi tabel beda-beda style.

Solusi:
- standarisasi komponen:
  - modal: `.app-modal`
  - badge: `.app-badge`
  - action group: `.table-actions`

Lokasi terkait:
- `resources/views/app/layouts/main.blade.php`
- beberapa blade modul `order/menu/user/kios/report`

## 5) Ketidakcocokan report legacy vs Laravel

Gejala:
- menu report belum lengkap atau route lama masih ke legacy.

Solusi:
- semua report legacy dipindah ke Laravel native,
- redirect route legacy diarahkan ke route report Laravel,
- menu sidebar report disinkronkan.

Lokasi terkait:
- `routes/web.php`
- `app/Http/Controllers/KantinReportController.php`
- `resources/views/app/report/*.blade.php`
- `resources/views/app/layouts/main.blade.php`

---

## Template Tambah Isu Baru

### Judul Isu:
- Gejala:
- Akar Masalah:
- Solusi:
- File/Path Terkait:

