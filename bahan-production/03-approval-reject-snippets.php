<?php

/*
|--------------------------------------------------------------------------
| Snippet approval tahap 1 ke atasan berikutnya
|--------------------------------------------------------------------------
|
| Tempel di method setuju($id) khusus role approval tahap 1
| seperti Supervisor, setelah save selesai.
|
*/

try {
    $approver = \App\Pegawai::find(\Auth::user()->pegawai_id);

    if ($pegawai && $approver) {
        $jabatanAtasan1 = \App\Jabatan::find($approver->jabatan);

        if ($jabatanAtasan1 && $jabatanAtasan1->atasan) {
            $atasan2 = \App\Pegawai::where('jabatan', $jabatanAtasan1->atasan)
                ->where('status_active', 1)
                ->first();

            if ($atasan2) {
                \App\Helpers\WhatsAppHelper::sendCutiNotificationAtasan2(
                    $pegawai,
                    $approver,
                    $atasan2,
                    $ordercuti
                );
            }
        }
    }
} catch (\Exception $e) {
    \Log::error('Gagal Notif Approval ke Atasan 2', array(
        'order_id' => $ordercuti->id,
        'error' => $e->getMessage(),
    ));
}

/*
|--------------------------------------------------------------------------
| Snippet approval final
|--------------------------------------------------------------------------
|
| Tempel di method setuju($id), setelah save selesai.
| Pakai hanya saat status final sudah DISETUJUI.
|
*/

try {
    $approver = \App\Pegawai::find(\Auth::user()->pegawai_id);

    if ($pegawai && $approver && $ordercuti->status === 'DISETUJUI') {
        \App\Helpers\WhatsAppHelper::sendCutiApprovalFinalNotification(
            $pegawai,
            $ordercuti,
            $approver
        );
    }
} catch (\Exception $e) {
    \Log::error('Gagal Notif Approval Final Cuti', array(
        'order_id' => $ordercuti->id,
        'error' => $e->getMessage(),
    ));
}

/*
|--------------------------------------------------------------------------
| Snippet reject
|--------------------------------------------------------------------------
|
| Tempel di method tolak($id), setelah save selesai.
|
*/

try {
    $pegawai = \App\Pegawai::find($ordercuti->pegawai_id);
    $rejectedBy = \App\Pegawai::find(\Auth::user()->pegawai_id);

    if ($pegawai && $rejectedBy) {
        \App\Helpers\WhatsAppHelper::sendCutiRejectedNotification(
            $pegawai,
            $ordercuti,
            $rejectedBy->name
        );
    }
} catch (\Exception $e) {
    \Log::error('Gagal Notif Reject Cuti', array(
        'order_id' => $ordercuti->id,
        'error' => $e->getMessage(),
    ));
}

/*
|--------------------------------------------------------------------------
| Mapping role dan method
|--------------------------------------------------------------------------
|
| SupervisorController
| - setuju($id) -> pakai snippet approval tahap 1 ke atasan berikutnya
| - tolak($id)
|
| KadivController
| - setuju($id) -> pakai snippet approval final
| - tolak($id)
|
| PincabController
| - setuju($id) -> pakai snippet approval final
| - tolak($id)
|
| KepatuhanController
| - setuju($id) -> pakai snippet approval final
| - tolak($id)
|
| DireksiController
| - setuju($id) -> pakai snippet approval final
| - tolak($id)
|
| DirbisController
| - setuju($id) -> pakai snippet approval final
| - tolak($id)
|
| ordercutiController
| - setuju($id)
| - tolak($id)
|
*/
