<?php

namespace App\Http\Controllers;

use App\ordercuti;
use App\Pegawai;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifPengajuanCuti;

class OrderCutiNotificationController extends Controller
{
  public function send($orderCutiId)
{
    \Log::info('Notif cuti DIPANGGIL', ['order_id' => $orderCutiId]);

    $order = \App\ordercuti::find($orderCutiId);
    if (!$order) {
        \Log::warning('Order cuti tidak ditemukan');
        return;
    }

    $pegawai = \App\Pegawai::find($order->pegawai_id);
    if (!$pegawai) {
        \Log::warning('Pegawai pemohon tidak ditemukan');
        return;
    }

    // jabatan pemohon
    $jabatanPemohon = \App\Jabatan::find($pegawai->jabatan);
    if (!$jabatanPemohon) {
        \Log::warning('Jabatan pemohon tidak ditemukan');
        return;
    }

    $emails = [];

    /**
     * =========================
     * OTO ATASAN
     * =========================
     */
    if ($jabatanPemohon->atasan) {
        $otoAtasanPegawai = \App\Pegawai::where('jabatan', $jabatanPemohon->atasan)
            ->whereNotNull('email')
            ->get();

        foreach ($otoAtasanPegawai as $p) {
            $emails[] = $p->email;
        }
    }

    /**
     * =========================
     * DIKET ATASAN
     * =========================
     */
    if ($jabatanPemohon->atasan) {
        $jabatanAtasan = \App\Jabatan::find($jabatanPemohon->atasan);

        if ($jabatanAtasan && $jabatanAtasan->atasan) {
            $diketAtasanPegawai = \App\Pegawai::where('jabatan', $jabatanAtasan->atasan)
                ->whereNotNull('email')
                ->get();

            foreach ($diketAtasanPegawai as $p) {
                $emails[] = $p->email;
            }
        }
    }

    /**
     * =========================
     * SDM (KHUSUS CUTI WAJIB)
     * =========================
     */
    if ($order->jeniscuti === 'Cuti Wajib') {
        $sdmPegawai = \App\Pegawai::whereIn('id', function ($q) {
                $q->select('pegawai_id')
                  ->from('users')
                  ->whereIn('roles', ['ADMIN_SDM', 'STAFF_SDM']);
            })
            ->whereNotNull('email')
            ->get();

        foreach ($sdmPegawai as $p) {
            $emails[] = $p->email;
        }
    }

    // bersihkan email duplikat
    $emails = array_values(array_unique($emails));

    \Log::info('EMAIL TUJUAN NOTIF CUTI', $emails);

    if (count($emails) === 0) {
        \Log::warning('EMAIL KOSONG - CEK DATA JABATAN / PEGAWAI');
        return;
    }

    \Mail::to($emails)->send(new \App\Mail\NotifPengajuanCuti($order));
}

}
