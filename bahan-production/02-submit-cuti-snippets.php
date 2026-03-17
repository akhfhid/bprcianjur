<?php

/*
|--------------------------------------------------------------------------
| Snippet submit cuti ke atasan
|--------------------------------------------------------------------------
|
| Tempel di bawah:
| $new_cuti->save();
|
*/

try {
    $pegawai = \App\Pegawai::find($new_cuti->pegawai_id);

    if ($pegawai) {
        $jabatanPemohon = \App\Jabatan::find($pegawai->jabatan);

        if ($jabatanPemohon && $jabatanPemohon->atasan) {
            $atasan1 = \App\Pegawai::where('jabatan', $jabatanPemohon->atasan)
                ->where('status_active', 1)
                ->first();

            if ($atasan1) {
                \App\Helpers\WhatsAppHelper::sendCutiNotificationAtasan1(
                    $pegawai,
                    $new_cuti,
                    $atasan1
                );
            }
        }
    }
} catch (\Exception $e) {
    \Log::error('Gagal Notif Submit Cuti', array(
        'order_id' => $new_cuti->id,
        'error' => $e->getMessage(),
    ));
}

/*
|--------------------------------------------------------------------------
| Role yang perlu dipasang
|--------------------------------------------------------------------------
| 1. StaffController::mintacuti
| 2. SupervisorController::mintacuti
| 3. KadivController::mintacuti
| 4. PincabController::mintacuti
| 5. KepatuhanController::mintacuti
| 6. DireksiController::mintacuti
| 7. DirbisController::mintacuti
*/
