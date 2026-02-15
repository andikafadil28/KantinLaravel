# KantinApp Migration Notes

## Status Saat Ini
- Root project sudah menjadi struktur Laravel 12.
- Aplikasi PHP lama dipindahkan ke `legacy_app` (di luar web root).
- Fondasi native Laravel sudah dibuat:
  - Model Eloquent untuk tabel inti (`user`, `tb_kios`, `tb_kategori_menu`, `tb_menu`, `tb_order`, `tb_list_order`, `tb_bayar`).
  - Auth native Laravel berbasis tabel `user` lama (`/app/login`, `/app/home`, `/app/logout`).
  - Modul `menu` native Laravel tersedia (`/app/menu`) dengan CRUD + upload foto.
  - Modul `order` + `order item` + `pembayaran` native tersedia (`/app/orders`).
  - Modul `user` dan `kios` native tersedia (`/app/users`, `/app/kios`).
  - Modul laporan utama native tersedia:
    - `/app/reports/orders` (laporan detail)
    - `/app/reports/rs` (pendapatan RS)
    - `/app/reports/toko` (pendapatan toko)
    - `/app/reports/menu` (penjualan menu)
  - Export laporan native tersedia (`CSV`):
    - `/app/reports/orders/export`
    - `/app/reports/rs/export`
    - `/app/reports/toko/export`
    - `/app/reports/menu/export`
- Routing lama sudah dimigrasikan ke Laravel melalui `LegacyController`:
  - `/?x=...` tetap didukung.
  - URL lama seperti `/home`, `/menu`, `/order`, `/login`, `/logout` otomatis diarahkan ke `/legacy/...`.
  - URL `/legacy/*` (termasuk `validate/*.php`, `proses/*.php`, `excel_export/*.php`, dan aset) sekarang diproses via Laravel.
- Koneksi DB Laravel diset ke MySQL database `sakinakantin`.

## Cara Menjalankan
- Arahkan document root web server ke folder `public`.
- Jalankan lokal: `php artisan serve`.
- Akses aplikasi dari:
  - `http://localhost/` (login native Laravel)
  - dashboard native: `http://localhost/app/home`
  - fallback legacy: `http://localhost/legacy/login`
  - auth native Laravel: `http://localhost/app/login`

## Mapping URL Lama ke Native
- `/home` -> `/app/home`
- `/login` -> `/app/login`
- `/logout` -> logout native
- `/menu` -> `/app/menu`
- `/order` -> `/app/orders`
- `/orderitem?kode_order=...` -> `/app/orders/{kode_order}`
- `/user` -> `/app/users`
- `/kios` -> `/app/kios`
- `/laporan` -> `/app/reports/orders`
- `/laporanrs` -> `/app/reports/rs`
- `/laporantoko` -> `/app/reports/toko`
- `/history` -> `/app/reports/menu`
- `/rekaprs`, `/rekapmenurs`, `/rekapkeuangan`, `/rekapkeuanganmenu` -> diarahkan ke laporan native terdekat

## Langkah Migrasi Lanjutan (Agar Full Native Laravel)
1. Migrasi login/logout dari `legacy_app/validate/*` ke auth Laravel.
2. Buat `Controller` + `Blade` native untuk modul `home`, `menu`, `order`, `order_item`, `user`, `kios`, laporan.
3. Pindahkan query dari `legacy_app/Database/Query/*` ke `Model` + Query Builder/Eloquent.
4. Pindahkan logic `validate/*` dan `proses/*` ke `Form Request` + service layer.
5. Setelah setiap modul stabil, hapus modul terkait di `legacy_app`.
