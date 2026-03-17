# Bahan Production WhatsApp Cuti

Folder ini berisi bahan implementasi notifikasi WhatsApp untuk environment production yang masih memakai `PHP 7` dan `Laravel 7`.

## Isi folder

- `01-WhatsAppHelper-production.php`
  Helper WhatsApp versi production.
- `02-submit-cuti-snippets.php`
  Snippet notifikasi saat pengaju submit cuti ke atasan.
- `03-approval-reject-snippets.php`
  Snippet notifikasi approve dan reject untuk semua role approval.
- `04-langkah-implementasi.md`
  Urutan langkah implementasi di server production.

## Cara pakai singkat

1. Salin method dari `01-WhatsAppHelper-production.php` ke helper production kamu.
2. Buka controller production sesuai role.
3. Tempel snippet dari file `02` untuk method submit cuti (`mintacuti`).
4. Tempel snippet dari file `03` untuk method `setuju($id)` dan `tolak($id)`.
5. Sesuaikan jika nama route atau model di production berbeda.
6. Jalankan clear cache Laravel.

## Role yang sudah disiapkan

- Staff
- Supervisor
- Kadiv
- Pincab
- Kepatuhan
- Direksi
- Dirbis
- Admin SDM (`ordercutiController`)
