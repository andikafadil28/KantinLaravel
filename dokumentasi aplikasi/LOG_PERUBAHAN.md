# Log Perubahan Aplikasi

Dokumen ini berisi ringkasan perubahan penting selama migrasi dan perapihan UI.

## 2026-02-15

### 1. Migrasi laporan legacy ke Laravel native
- Menambahkan halaman laporan native:
  - Rekap RS
  - Rekap Kantin
  - Rekap Keuangan (detail)
  - Rekap Keuangan Menu
- Menambahkan route report baru di `routes/web.php`.
- Mengarahkan route legacy report ke route Laravel.
- Menghapus blok menu "Laporan Legacy Mode" dari sidebar dan mengganti semua ke menu Laravel.

### 2. Perbaikan pembayaran order
- Memperbaiki validasi nominal bayar agar format angka rupiah tidak salah baca.
- Memastikan kasus diskon `0` tidak memicu error "jumlah bayar tidak cukup".
- Menambahkan form bayar cepat yang lebih jelas di list order.

### 3. Migrasi fitur grafik Home
- Memigrasikan chart "Menu Terlaris Hari Ini" dan "Menu Terlaris Minggu Ini" dari legacy.
- Menambahkan query chart di `KantinHomeController`.
- Menambahkan rendering Chart.js di `resources/views/app/home.blade.php`.

### 4. Penyamaan dan polishing UI
- Sidebar:
  - tambah minimize/maximize,
  - tambah backdrop mobile,
  - animasi buka tutup lebih smooth,
  - indikator active submenu (dot).
- Navbar:
  - layout desktop/mobile lebih rapi,
  - user chip + dropdown lebih konsisten.
- Home:
  - hero + CTA + feature cards,
  - tema warna food (orange) disesuaikan.
- Order:
  - halaman detail order item dipoles (meta card + section card),
  - halaman list order dipoles (grid form + aksi lebih jelas).

### 5. Konsistensi komponen
- Standarisasi modal edit lintas modul (`app-modal`) pada:
  - order, menu, user, kios.
- Standarisasi action button tabel (`table-actions`) lintas halaman.
- Standarisasi badge (`app-badge`) untuk:
  - status bayar order,
  - level user,
  - grand total beberapa laporan.

### 6. Perbaikan DataTable warning
- Mencegah re-inisialisasi DataTable pada elemen yang sama.
- Menormalkan markup tabel agar kompatibel DataTable (hindari `colspan` di `tbody` untuk tabel DataTable).
- Mengubah mode error DataTable supaya tidak popup mengganggu.

## Catatan Operasional

Setiap selesai perubahan, jalankan:
1. `php artisan view:clear`
2. `php artisan view:cache`
3. `php artisan test`

Update file ini tiap ada perubahan besar agar tracking tetap jelas.

