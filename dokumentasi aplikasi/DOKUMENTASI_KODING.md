# Dokumentasi Koding Aplikasi (Laravel)

Dokumen ini dibuat sebagai panduan cepat untuk mengetahui:
- fungsi ada di file mana,
- alur data dari route -> controller -> view,
- titik edit paling aman saat ingin mengubah fitur.

## 1. Struktur Utama

- `routes/web.php`
  Tempat semua route aplikasi.
- `app/Http/Controllers/`
  Logika utama per modul.
- `app/Models/`
  Model tabel database.
- `resources/views/app/`
  Tampilan Blade (UI utama Laravel).
- `resources/views/app/layouts/main.blade.php`
  Layout global: sidebar, navbar, style global, inisialisasi DataTable/Select2.

## 2. Peta Route Penting

File: `routes/web.php`

- Auth:
  - `GET /app/login` -> `KantinAuthController@showLogin`
  - `POST /app/login` -> `KantinAuthController@login`
  - `POST /app/logout` -> `KantinAuthController@logout`
- Home:
  - `GET /app/home` -> `KantinHomeController@index`
- Menu:
  - `GET /app/menu` -> `KantinMenuController@index`
  - `POST /app/menu` -> `KantinMenuController@store`
  - `POST /app/menu/{id}` -> `KantinMenuController@update`
  - `DELETE /app/menu/{id}` -> `KantinMenuController@destroy`
- Order:
  - `GET /app/orders` -> `KantinOrderController@index`
  - `POST /app/orders` -> `KantinOrderController@store`
  - `GET /app/orders/{id}` -> `KantinOrderController@show`
  - `POST /app/orders/{id}` -> `KantinOrderController@update`
  - `DELETE /app/orders/{id}` -> `KantinOrderController@destroy`
  - `POST /app/orders/{id}/items` -> `KantinOrderController@addItem`
  - `POST /app/orders/{id}/items/{itemId}` -> `KantinOrderController@updateItem`
  - `DELETE /app/orders/{id}/items/{itemId}` -> `KantinOrderController@deleteItem`
  - `POST /app/orders/{id}/pay` -> `KantinOrderController@pay`
- Report:
  - `orders`, `rs`, `toko`, `menu`, `rekap-rs`, `rekap-menu-rs`, `finance-detail`, `finance-menu`
  - Export CSV tersedia untuk semua report utama.
- Admin:
  - `GET/POST/DELETE /app/users...` -> `KantinAdminController`
  - `GET/POST/DELETE /app/kios...` -> `KantinAdminController`

## 3. Controller per Fungsi

### A. `KantinHomeController.php`
- Fungsi: dashboard home.
- Menyediakan:
  - info user login,
  - jumlah order hari ini,
  - data chart menu terlaris harian dan mingguan.

Edit di sini jika mau ubah:
- logic KPI home,
- query chart home,
- range waktu statistik.

### B. `KantinOrderController.php`
- Fungsi: transaksi order, detail item order, pembayaran.
- Titik penting:
  - validasi input order,
  - hitung total + PPN + diskon,
  - validasi nominal bayar.

Edit di sini jika mau ubah:
- aturan perhitungan pembayaran,
- format input bayar/diskon,
- status order dibayar/belum.

### C. `KantinReportController.php`
- Fungsi: semua laporan dan export CSV.
- Sudah mencakup:
  - pendapatan detail,
  - pendapatan kantin detail,
  - pendapatan toko detail,
  - rekap toko,
  - rekap rs,
  - rekap kantin,
  - rekap keuangan detail,
  - rekap keuangan menu.

Edit di sini jika mau ubah:
- kolom report,
- rumus agregasi report,
- filter tanggal/kios,
- isi export CSV.

### D. `KantinMenuController.php`
- Fungsi: CRUD menu makanan/minuman.

### E. `KantinAdminController.php`
- Fungsi: CRUD user dan kios.

### F. `KantinAuthController.php`
- Fungsi: login/logout + session user.

## 4. Model Database yang Sering Dipakai

Folder: `app/Models/`

- `KantinOrder` -> `tb_order`
- `KantinListOrder` -> `tb_list_order`
- `KantinMenu` -> `tb_menu`
- `KantinBayar` -> `tb_bayar`
- `KantinUser` -> `user`
- `KantinKios` -> `tb_kios`

Jika mau tambah kolom baru dari DB, biasanya edit:
- model `fillable`,
- query di controller,
- form input di blade.

## 5. View (Blade) per Halaman

### Layout Global
- `resources/views/app/layouts/main.blade.php`
  - sidebar + navbar,
  - style global,
  - inisialisasi DataTable dan Select2,
  - behavior sidebar mobile/desktop.

### Home
- `resources/views/app/home.blade.php`

### Menu
- `resources/views/app/menu/index.blade.php`

### Order
- list order: `resources/views/app/order/index.blade.php`
- detail order item: `resources/views/app/order/show.blade.php`

### Report
Folder: `resources/views/app/report/`
- `orders.blade.php`
- `rs.blade.php`
- `toko.blade.php`
- `menu.blade.php`
- `rekap_rs.blade.php`
- `rekap_menu_rs.blade.php`
- `finance_detail.blade.php`
- `finance_menu.blade.php`

### Admin
- user: `resources/views/app/admin/users.blade.php`
- kios: `resources/views/app/admin/kios.blade.php`

## 6. Panduan Edit Cepat (Use Case)

### Ubah warna/tema global aplikasi
- Edit: `resources/views/app/layouts/main.blade.php` (bagian CSS).

### Ubah menu sidebar/navbar
- Edit: `resources/views/app/layouts/main.blade.php`.

### Tambah report baru
1. Tambah method controller di `KantinReportController.php`.
2. Tambah route di `routes/web.php`.
3. Tambah view baru di `resources/views/app/report/`.
4. Tambah menu sidebar di `main.blade.php`.

### Ubah rumus pembayaran
- Edit di `KantinOrderController.php` (method pembayaran).

### Hilangkan warning DataTable
- Cek:
  - jangan re-init table yang sama,
  - hindari `colspan` pada `tbody` table DataTable,
  - inisialisasi global ada di `main.blade.php`.

## 7. Catatan Implementasi UI Saat Ini

- Tabel aksi sudah distandarkan (`table-actions`).
- Badge status sudah distandarkan (`app-badge`).
- Modal edit lintas modul sudah distandarkan (`app-modal`).
- Sidebar mobile sudah ada backdrop dan animasi halus.
- Home sudah punya chart menu terlaris dan tema food.

## 8. Checklist Setelah Edit

Jalankan:
1. `php artisan view:clear`
2. `php artisan view:cache`
3. `php artisan test`

Jika route diubah:
1. `php artisan route:list`

