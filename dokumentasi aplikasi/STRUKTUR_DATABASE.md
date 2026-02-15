# Struktur Database (Ringkas)

Dokumen ini menjelaskan tabel inti yang dipakai aplikasi Laravel hasil migrasi.

## 1. `user`

Fungsi: data akun login aplikasi.

Kolom penting:
- `id` (PK)
- `username`
- `password`
- `level` (umumnya `1=admin`, `3=kasir`)
- `Kios` (nama kios default user)

Dipakai di:
- login/logout
- otorisasi menu admin
- pencatatan kasir pada order

## 2. `tb_kios`

Fungsi: master kios/toko.

Kolom penting:
- `id` (PK)
- `nama`

Dipakai di:
- form user
- form menu
- filter order/report

## 3. `tb_menu`

Fungsi: master menu makanan/minuman.

Kolom penting:
- `id` (PK)
- `nama`
- `keterangan`
- `kategori`
- `nama_toko`
- `harga`
- `pajak`
- `foto`

Relasi:
- 1 menu bisa muncul di banyak `tb_list_order`.

## 4. `tb_order`

Fungsi: header transaksi order.

Kolom penting:
- `id_order` (PK)
- `pelanggan`
- `meja`
- `kasir` (FK ke `user.id`)
- `nama_kios`
- `waktu_order`
- `catatan`

Relasi:
- 1 order punya banyak item di `tb_list_order`.
- 1 order punya 0/1 pembayaran di `tb_bayar`.

## 5. `tb_list_order`

Fungsi: detail item per order.

Kolom penting:
- `id_list_order` (PK)
- `kode_order` (FK ke `tb_order.id_order`)
- `menu` (FK ke `tb_menu.id`)
- `jumlah`
- `catatan_order`
- `status`

## 6. `tb_bayar`

Fungsi: data pembayaran order.

Kolom penting:
- `id_bayar` (PK/FK ke `tb_order.id_order`)
- `jumlah_bayar`
- `diskon`
- `nominal_toko`
- `nominal_rs`

Catatan:
- status order dianggap "dibayar" jika record di `tb_bayar` ada.

## 7. `tb_kategori_menu` (opsional sesuai data)

Fungsi: master kategori menu.

Kolom umum:
- `id_kategori`
- `kategori_menu`

Dipakai untuk dropdown kategori saat input/edit menu.

## Relasi Cepat

- `user (1) -> (n) tb_order` via `tb_order.kasir`
- `tb_order (1) -> (n) tb_list_order` via `tb_list_order.kode_order`
- `tb_menu (1) -> (n) tb_list_order` via `tb_list_order.menu`
- `tb_order (1) -> (1) tb_bayar` via `tb_bayar.id_bayar`

