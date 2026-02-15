# Peta Alur Fitur

Tujuan: cepat tahu alur teknis saat debugging atau menambah fitur.

## 1. Login

Alur:
1. `GET /app/login` -> form login (`KantinAuthController@showLogin`).
2. `POST /app/login` -> validasi kredensial (`KantinAuthController@login`).
3. Simpan session (`kantin_user_id`, `username_kantin`, `level_kantin`).
4. Redirect ke `/app/home`.

File terkait:
- `routes/web.php`
- `app/Http/Controllers/KantinAuthController.php`
- `resources/views/app/auth/*.blade.php`

## 2. Buat Order

Alur:
1. `GET /app/orders` tampil list + form tambah order.
2. `POST /app/orders` simpan header order ke `tb_order`.
3. User masuk ke detail order untuk tambah item.

File terkait:
- `app/Http/Controllers/KantinOrderController.php`
- `resources/views/app/order/index.blade.php`

## 3. Kelola Item Order

Alur:
1. `GET /app/orders/{id}` tampil detail order + item.
2. `POST /app/orders/{id}/items` tambah item.
3. `POST /app/orders/{id}/items/{itemId}` edit item.
4. `DELETE /app/orders/{id}/items/{itemId}` hapus item.

File terkait:
- `KantinOrderController` (method `show`, `addItem`, `updateItem`, `deleteItem`)
- `resources/views/app/order/show.blade.php`

## 4. Pembayaran

Alur:
1. User isi diskon + nominal bayar.
2. `POST /app/orders/{id}/pay` hitung total + validasi.
3. Simpan ke `tb_bayar`.
4. Status order jadi dibayar (record `tb_bayar` ada).

Catatan:
- Parsing angka rupiah ditangani di controller untuk mencegah error "jumlah bayar tidak cukup".

## 5. Laporan

Alur umum:
1. user buka report (route `/app/reports/...`)
2. controller baca filter (`start_date`, `end_date`, `kios_filter`)
3. query agregasi sesuai report
4. kirim ke blade
5. optional export CSV dari route `/export`

File utama:
- `app/Http/Controllers/KantinReportController.php`
- `resources/views/app/report/*.blade.php`
- `routes/web.php`

Report aktif:
- pendapatan detail
- pendapatan kantin detail
- pendapatan toko detail
- rekap toko
- rekap rs
- rekap kantin
- rekap keuangan detail
- rekap keuangan menu

## 6. Home Dashboard + Chart

Alur:
1. `GET /app/home`
2. controller ambil KPI dan data chart (harian + mingguan)
3. view render Chart.js

File terkait:
- `app/Http/Controllers/KantinHomeController.php`
- `resources/views/app/home.blade.php`

## 7. User & Kios (Admin)

Alur:
- CRUD user:
  - list/tambah/edit/hapus di `/app/users`
- CRUD kios:
  - list/tambah/edit/hapus di `/app/kios`

File terkait:
- `app/Http/Controllers/KantinAdminController.php`
- `resources/views/app/admin/users.blade.php`
- `resources/views/app/admin/kios.blade.php`

