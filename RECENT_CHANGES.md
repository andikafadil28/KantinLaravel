# Recent Changes Log

File ini dibuat supaya riwayat perubahan bisa dilihat kapan saja dari terminal/editor, tanpa bergantung ke scroll chat.

## 2026-02-15 - Laravel Migration Progress

### Laporan & Rekap (Native Laravel)
- Menambahkan laporan native:
  - `Rekap RS`
  - `Rekap Kantin`
  - `Rekap Keuangan`
  - `Rekap Keuangan Menu`
- Route legacy report diarahkan ke route Laravel native.
- Sidebar legacy report mode dihapus, semua menu laporan masuk ke Laravel.

### Pembayaran & Order
- Memperbaiki parsing nominal pembayaran agar format rupiah tidak memicu validasi "jumlah bayar tidak cukup".
- Menambahkan/merapikan `Bayar Cepat` pada list order.
- Memperbaiki potensi warning DataTable dengan:
  - menghindari re-init table yang sudah ter-inisialisasi,
  - menormalkan markup `tbody` tanpa `colspan` untuk table DataTable,
  - mengarahkan warning DataTable ke `console`.

### UI/UX
- Sidebar:
  - tombol minimize/maximize,
  - mobile backdrop,
  - animasi buka/tutup lebih halus,
  - submenu active indicator (dot).
- Navbar:
  - topbar lebih rapih desktop/mobile,
  - user chip dan dropdown lebih konsisten.
- Home:
  - migrasi chart menu terlaris harian/mingguan dari legacy,
  - hero + CTA + feature cards,
  - tema warna di-tuning ke orange yang lebih tegas namun nyaman.
- Order pages:
  - halaman detail order (`order item`) dipoles (meta panel, sectioned cards),
  - halaman list order dipoles (form grid, action grouping, quick pay panel).

### Konsistensi Komponen
- Standarisasi modal edit lintas halaman (`app-modal`) untuk:
  - order, menu, user, kios.
- Standarisasi action button tabel (`table-actions`) lintas halaman.
- Standarisasi badge status (`app-badge`) pada:
  - status order (dibayar/belum),
  - level user (admin/kasir),
  - ringkasan grand total beberapa laporan.

## Cara Cek Cepat dari Terminal
- Lihat file ini: `Get-Content RECENT_CHANGES.md`
- Lihat file yang berubah (tracked): `git diff --name-only`
- Lihat detail diff satu file: `git diff -- resources/views/app/order/show.blade.php`

