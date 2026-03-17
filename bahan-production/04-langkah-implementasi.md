# Langkah Implementasi Production

## 1. Backup file production

Backup file berikut sebelum diubah:

- `app/Helpers/WhatsAppHelper.php`
- controller cuti tiap role
- `.env`

## 2. Tambahkan env WhatsApp

Tambahkan ke `.env` production:

```env
WA_API_URL=https://wa.bprcianjur.co.id/api/send-message
WA_API_CODE=ISI_KODE_API_PRODUCTION
```

## 3. Update helper production

Salin method dari file:

- `01-WhatsAppHelper-production.php`

Jika helper production sudah ada, cukup tambahkan method yang belum ada:

- `convertPhoneNumber`
- `getTimeGreeting`
- `sendMessage`
- `sendCutiNotificationAtasan1`
- `sendCutiNotificationAtasan2`
- `sendCutiApprovalFinalNotification`
- `sendCutiRejectedNotification`

## 4. Implementasi notifikasi submit

Untuk semua method submit cuti:

- `StaffController::mintacuti`
- `SupervisorController::mintacuti`
- `KadivController::mintacuti`
- `PincabController::mintacuti`
- `KepatuhanController::mintacuti`
- `DireksiController::mintacuti`
- `DirbisController::mintacuti`

Tempel snippet dari:

- `02-submit-cuti-snippets.php`

Posisi tempel:

```php
$new_cuti->save();
// tempel snippet di sini
return redirect(...);
```

## 5. Implementasi approve tahap 1, approve final, dan reject

Untuk method approval tahap 1:

- `SupervisorController::setuju`

Pakai snippet:

- `03-approval-reject-snippets.php`

Bagian yang dipakai:

- `Snippet approval tahap 1 ke atasan berikutnya`

Untuk semua method approval final:

- `KadivController::setuju`
- `PincabController::setuju`
- `KepatuhanController::setuju`
- `DireksiController::setuju`
- `DirbisController::setuju`
- `ordercutiController::setuju`

Untuk semua method reject:

- `SupervisorController::tolak`
- `KadivController::tolak`
- `PincabController::tolak`
- `KepatuhanController::tolak`
- `DireksiController::tolak`
- `DirbisController::tolak`
- `ordercutiController::tolak`

Tempel snippet dari:

- `03-approval-reject-snippets.php`

Bagian yang dipakai:

- `Snippet approval final`
- `Snippet reject`

## 6. Clear cache

Jalankan:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## 7. Testing

Lakukan testing bertahap:

1. User submit cuti -> cek WA masuk ke atasan.
2. Supervisor approve -> cek WA masuk ke Kadiv / atasan berikutnya.
3. Atasan final approve -> cek WA final masuk ke pengaju.
4. Atasan reject -> cek WA reject masuk ke pengaju.

## 8. Catatan penting

- Karena production masih `PHP 7 / Laravel 7`, gunakan `catch (\Exception $e)` agar aman.
- Helper production ini memakai `Illuminate\Support\Facades\Http`, bukan `curl`.
- Jangan hardcode API code di helper jika bisa diambil dari `.env`.
- Jika ada role yang struktur method-nya sedikit beda, logika tetap sama:
  simpan data dulu, baru kirim WA.
