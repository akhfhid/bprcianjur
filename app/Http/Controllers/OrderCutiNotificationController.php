<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationLogHelper;
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
                NotificationLogHelper::error([
                    'category' => 'cuti',
                    'channel' => 'system',
                    'reference_type' => 'ordercuti',
                    'reference_id' => $orderCutiId,
                    'message' => 'Order cuti tidak ditemukan.',
                    'error_message' => 'Data order cuti tidak tersedia.',
                ]);

                Log::error('ORDER CUTI TIDAK DITEMUKAN', ['order_id' => $orderCutiId]);
                return;
            }

            Log::info('ORDER CUTI DITEMUKAN', $order->toArray());
            $pegawai = Pegawai::find($order->pegawai_id);
            if (!$pegawai) {
                NotificationLogHelper::error([
                    'actor_id' => $order->user_id,
                    'category' => 'cuti',
                    'channel' => 'system',
                    'reference_type' => 'ordercuti',
                    'reference_id' => $order->id,
                    'cabang_id' => $order->cabang,
                    'message' => 'Pegawai pemohon tidak ditemukan.',
                    'error_message' => 'Data pegawai pemohon tidak tersedia.',
                ]);

                Log::error('PEGAWAI PEMOHON TIDAK DITEMUKAN', ['pegawai_id' => $order->pegawai_id]);
                return;
            }

            Log::info('PEGAWAI PEMOHON', $pegawai->toArray());
            $jabatanPemohon = Jabatan::find($pegawai->jabatan);
            if (!$jabatanPemohon) {
                NotificationLogHelper::error([
                    'actor_id' => $order->user_id,
                    'category' => 'cuti',
                    'channel' => 'system',
                    'reference_type' => 'ordercuti',
                    'reference_id' => $order->id,
                    'cabang_id' => $order->cabang,
                    'recipient_pegawai_id' => $pegawai->id,
                    'recipient_name' => $pegawai->name,
                    'recipient_email' => $pegawai->email,
                    'recipient_phone' => $pegawai->nohp ?? null,
                    'message' => 'Jabatan pemohon tidak ditemukan.',
                    'error_message' => 'Data jabatan pemohon kosong.',
                ]);

                Log::error('JABATAN PEMOHON TIDAK DITEMUKAN', ['jabatan_id' => $pegawai->jabatan]);
                return;
            }

            $recipients = [];
            $addRecipient = function ($p, $source) use (&$recipients, $order) {
                if (empty($p->email)) {
                    NotificationLogHelper::error([
                        'actor_id' => $order->user_id,
                        'category' => 'cuti',
                        'channel' => 'email',
                        'reference_type' => 'ordercuti',
                        'reference_id' => $order->id,
                        'cabang_id' => $order->cabang,
                        'recipient_pegawai_id' => $p->id,
                        'recipient_name' => $p->name,
                        'recipient_phone' => $p->nohp ?? null,
                        'subject' => 'Notifikasi Pengajuan Cuti',
                        'message' => 'Penerima dilewati karena email kosong.',
                        'error_message' => 'Email tidak tersedia.',
                        'meta' => ['source' => $source],
                    ]);
                    return;
                }

                if ((int) $p->status_active !== 1) {
                    NotificationLogHelper::error([
                        'actor_id' => $order->user_id,
                        'category' => 'cuti',
                        'channel' => 'email',
                        'reference_type' => 'ordercuti',
                        'reference_id' => $order->id,
                        'cabang_id' => $order->cabang,
                        'recipient_pegawai_id' => $p->id,
                        'recipient_name' => $p->name,
                        'recipient_email' => $p->email,
                        'recipient_phone' => $p->nohp ?? null,
                        'subject' => 'Notifikasi Pengajuan Cuti',
                        'message' => 'Penerima dilewati karena status tidak aktif.',
                        'error_message' => 'status_active != 1',
                        'meta' => ['source' => $source],
                    ]);
                    return;
                }

                $email = strtolower(trim($p->email));
                if (!isset($recipients[$email])) {
                    $recipients[$email] = [
                        'id' => $p->id,
                        'name' => $p->name,
                        'email' => $p->email,
                        'nohp' => $p->nohp ?? null,
                        'source' => [$source],
                    ];
                } else {
                    $recipients[$email]['source'][] = $source;
                }
            };

            if ($jabatanPemohon->atasan) {
                $semuaAtasan = Pegawai::where('jabatan', $jabatanPemohon->atasan)
                    ->whereNotNull('email')
                    ->get();

                foreach ($semuaAtasan as $p) {
                    $addRecipient($p, 'atasan_langsung');
                }
            }
            if ($jabatanPemohon->atasan) {
                $jabatanAtasan = Jabatan::find($jabatanPemohon->atasan);
                if ($jabatanAtasan && $jabatanAtasan->atasan) {
                    $semuaDiket = Pegawai::where('jabatan', $jabatanAtasan->atasan)
                        ->whereNotNull('email')
                        ->get();

                    foreach ($semuaDiket as $p) {
                        $addRecipient($p, 'atasan_diketahui');
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
                    $addRecipient($p, 'sdm');
                }
            }

            $recipientList = array_values($recipients);
            Log::info('EMAIL FINAL TUJUAN', array_map(function ($r) {
                return $r['email'];
            }, $recipientList));

            if (count($recipientList) === 0) {
                NotificationLogHelper::error([
                    'actor_id' => $order->user_id,
                    'category' => 'cuti',
                    'channel' => 'email',
                    'reference_type' => 'ordercuti',
                    'reference_id' => $order->id,
                    'cabang_id' => $order->cabang,
                    'subject' => 'Notifikasi Pengajuan Cuti',
                    'message' => 'Notifikasi dibatalkan karena daftar penerima kosong.',
                    'error_message' => 'Tidak ada email penerima valid/aktif.',
                ]);

                Log::error('EMAIL KOSONG - NOTIF DIBATALKAN');
                return;
            }

            foreach ($recipientList as $recipient) {
                try {
                    Mail::to($recipient['email'])->send(new NotifPengajuanCuti($order));

                    NotificationLogHelper::success([
                        'actor_id' => $order->user_id,
                        'category' => 'cuti',
                        'channel' => 'email',
                        'reference_type' => 'ordercuti',
                        'reference_id' => $order->id,
                        'cabang_id' => $order->cabang,
                        'recipient_pegawai_id' => $recipient['id'],
                        'recipient_name' => $recipient['name'],
                        'recipient_email' => $recipient['email'],
                        'recipient_phone' => $recipient['nohp'],
                        'subject' => 'Notifikasi Pengajuan Cuti',
                        'message' => 'Email notifikasi cuti berhasil dikirim.',
                        'meta' => [
                            'sources' => $recipient['source'],
                            'jeniscuti' => $order->jeniscuti,
                        ],
                    ]);
                } catch (\Throwable $recipientError) {
                    NotificationLogHelper::error([
                        'actor_id' => $order->user_id,
                        'category' => 'cuti',
                        'channel' => 'email',
                        'reference_type' => 'ordercuti',
                        'reference_id' => $order->id,
                        'cabang_id' => $order->cabang,
                        'recipient_pegawai_id' => $recipient['id'],
                        'recipient_name' => $recipient['name'],
                        'recipient_email' => $recipient['email'],
                        'recipient_phone' => $recipient['nohp'],
                        'subject' => 'Notifikasi Pengajuan Cuti',
                        'message' => 'Email notifikasi cuti gagal dikirim.',
                        'error_message' => $recipientError->getMessage(),
                        'meta' => [
                            'sources' => $recipient['source'],
                            'jeniscuti' => $order->jeniscuti,
                        ],
                    ]);

                    Log::warning('GAGAL KIRIM EMAIL NOTIF CUTI PER PENERIMA', [
                        'order_id' => $order->id,
                        'recipient_email' => $recipient['email'],
                        'message' => $recipientError->getMessage(),
                    ]);
                }
            }

            Log::info('EMAIL NOTIF CUTI SELESAI DIPROSES');

        } catch (\Throwable $e) {
            NotificationLogHelper::error([
                'category' => 'cuti',
                'channel' => 'system',
                'reference_type' => 'ordercuti',
                'reference_id' => $orderCutiId,
                'message' => 'Terjadi error fatal saat proses notifikasi cuti.',
                'error_message' => $e->getMessage(),
                'meta' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ]);

            Log::critical('ERROR FATAL NOTIF CUTI', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
        }
        Log::info('=== NOTIF CUTI END ===');
    }
}
