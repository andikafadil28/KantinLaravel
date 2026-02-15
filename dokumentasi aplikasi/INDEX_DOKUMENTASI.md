# Index Dokumentasi Aplikasi

Halaman ini adalah pintu masuk semua dokumentasi proyek.

## Daftar Dokumen

1. `DOKUMENTASI_KODING.md`
- Panduan utama struktur kode.
- Menjelaskan route, controller, model, view, dan titik edit fitur.

2. `LOG_PERUBAHAN.md`
- Ringkasan perubahan besar yang sudah dikerjakan.
- Cocok untuk melihat histori update cepat.

3. `STRUKTUR_DATABASE.md`
- Ringkasan tabel database, kolom penting, dan relasi inti.

4. `PETA_ALUR_FITUR.md`
- Alur teknis tiap fitur (login, order, bayar, report, dashboard).

5. `CHECKLIST_TESTING.md`
- Checklist manual sebelum release/deploy.

6. `KNOWN_ISSUES.md`
- Daftar masalah umum + solusi + lokasi file terkait.

7. `LOG_TERMINAL.txt`
- Log otomatis input/output terminal (PowerShell transcript).
- Cocok untuk jejak command terbaru saat debugging.

## Rekomendasi Urutan Baca (Developer Baru)

1. `DOKUMENTASI_KODING.md`
2. `STRUKTUR_DATABASE.md`
3. `PETA_ALUR_FITUR.md`
4. `KNOWN_ISSUES.md`
5. `CHECKLIST_TESTING.md`
6. `LOG_PERUBAHAN.md`
7. `LOG_TERMINAL.txt`

## Perintah Terminal Cepat

- Lihat semua file dokumentasi:
  - `Get-ChildItem "dokumentasi aplikasi"`
- Buka index:
  - `Get-Content "dokumentasi aplikasi/INDEX_DOKUMENTASI.md"`
- Lihat log perubahan:
  - `Get-Content "dokumentasi aplikasi/LOG_PERUBAHAN.md"`
- Aktifkan log input/output terminal:
  - `.\scripts\terminal-log.ps1 -Action start`
- Hentikan log terminal:
  - `.\scripts\terminal-log.ps1 -Action stop`
- Cek status log terminal:
  - `.\scripts\terminal-log.ps1 -Action status`
- Auto refresh log saat ada command baru:
  - `.\scripts\terminal-log.ps1 -Action watch`
- Tambah catatan dari Codex/manual ke log:
  - `.\scripts\terminal-log.ps1 -Action append -Message "isi catatan"`
- Jalankan command sekaligus log output (mode Codex):
  - `.\scripts\terminal-log.ps1 -Action run -Command "php artisan test"`
