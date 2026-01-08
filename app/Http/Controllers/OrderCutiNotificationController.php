<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\ordercuti;
use App\Pegawai;
use App\Jabatan;
use App\Mail\NotifPengajuanCuti;

class OrderCutiNotificationController extends Controller
{
    public function send($orderCutiId)
    {
        Log::info('=== NOTIF CUTI START ===', [
            'order_id' => $orderCutiId
        ]);

        try {
            $order = ordercuti::find($orderCutiId);
            if (!$order) {
                Log::error('ORDER CUTI TIDAK DITEMUKAN', ['order_id' => $orderCutiId]);
                return;
            }

            Log::info('ORDER CUTI DITEMUKAN', $order->toArray());
            $pegawai = Pegawai::find($order->pegawai_id);
            if (!$pegawai) {
                Log::error('PEGAWAI PEMOHON TIDAK DITEMUKAN', ['pegawai_id' => $order->pegawai_id]);
                return;
            }

            Log::info('PEGAWAI PEMOHON', $pegawai->toArray());
            $jabatanPemohon = Jabatan::find($pegawai->jabatan);
            if (!$jabatanPemohon) {
                Log::error('JABATAN PEMOHON TIDAK DITEMUKAN', ['jabatan_id' => $pegawai->jabatan]);
                return;
            }

            $emails = [];
            if ($jabatanPemohon->atasan) {
                $semuaAtasan = Pegawai::where('jabatan', $jabatanPemohon->atasan)
                    ->whereNotNull('email')
                    ->get();

                foreach ($semuaAtasan as $p) {
                    if ($p->status_active == 1) {
                        $emails[] = $p->email;
                    } else {
                        Log::warning('OTO ATASAN DIABAIKAN (TIDAK AKTIF)', [
                            'nama' => $p->name,
                            'email' => $p->email,
                            'status_active' => $p->status_active
                        ]);
                    }
                }
            }
            if ($jabatanPemohon->atasan) {
                $jabatanAtasan = Jabatan::find($jabatanPemohon->atasan);
                if ($jabatanAtasan && $jabatanAtasan->atasan) {
                    $semuaDiket = Pegawai::where('jabatan', $jabatanAtasan->atasan)
                        ->whereNotNull('email')
                        ->get();

                    foreach ($semuaDiket as $p) {
                        if ($p->status_active == 1) {
                            $emails[] = $p->email;
                        } else {
                            Log::warning('DIKET ATASAN DIABAIKAN (TIDAK AKTIF)', [
                                'nama' => $p->name,
                                'email' => $p->email,
                                'status_active' => $p->status_active
                            ]);
                        }
                    }
                }
            }
            if (trim($order->jeniscuti) === 'Cuti Wajib') {
                $semuaSdm = Pegawai::whereIn('id', function ($q) {
                        $q->select('pegawai_id')
                          ->from('users')
                          ->whereIn('roles', ['ADMIN_SDM', 'STAFF_SDM']);
                    })
                    ->whereNotNull('email')
                    ->get();

                foreach ($semuaSdm as $p) {
                    if ($p->status_active == 1) {
                        $emails[] = $p->email;
                    } else {
                        Log::warning('SDM DIABAIKAN (TIDAK AKTIF)', [
                            'nama' => $p->name,
                            'email' => $p->email,
                            'status_active' => $p->status_active
                        ]);
                    }
                }
            }

            $emails = array_values(array_unique($emails));
            Log::info('EMAIL FINAL TUJUAN', $emails);

            if (count($emails) === 0) {
                Log::error('EMAIL KOSONG - NOTIF DIBATALKAN');
                return;
            }

            Mail::to($emails)->send(new NotifPengajuanCuti($order));
            Log::info('EMAIL NOTIF CUTI BERHASIL TERKIRIM');

        } catch (\Throwable $e) {
            Log::critical('ERROR FATAL NOTIF CUTI', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
        }
        Log::info('=== NOTIF CUTI END ===');
    }
}