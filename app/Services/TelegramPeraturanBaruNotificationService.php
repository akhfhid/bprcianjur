<?php

namespace App\Services;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Helpers\TelegramHelper;
use App\peraturan;
use App\Pegawai;
use Illuminate\Support\Facades\Log;

class TelegramPeraturanBaruNotificationService
{
    /**
     * Kirim notifikasi "peraturan baru" ke satu pegawai via Telegram.
     *
     * @param  \App\peraturan  $peraturan
     * @param  \App\Pegawai    $pegawai
     * @param  string|null     $cabangName
     * @return void
     */
    public function notify(peraturan $peraturan, Pegawai $pegawai, $cabangName = null)
    {
        try {
            if (!TelegramHelper::isEnabled()) {
                return;
            }

            $chatIds = TelegramHelper::resolvePegawaiChatId($pegawai);

            if (empty($chatIds)) {
                return;
            }

            $content = $this->buildMessage($peraturan, $pegawai, $cabangName);
            $title = 'Peraturan Baru';
            $result = TelegramHelper::sendBulk($chatIds, $title, $content);

            NotificationLogHelper::write([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'telegram',
                'status' => !empty($result['success']) ? 'success' : 'error',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $pegawai->cabang,
                'recipient_pegawai_id' => $pegawai->id,
                'recipient_name' => $pegawai->name,
                'recipient_email' => $pegawai->email,
                'subject' => 'Peraturan Baru: ' . $peraturan->name,
                'message' => $result['message'] ?? null,
                'error_message' => !empty($result['success']) ? null : ($result['message'] ?? 'Gagal kirim telegram'),
                'meta' => [
                    'cabang_name' => $cabangName,
                    'jabatan_name' => optional($pegawai->jabatan)->name ?? null,
                    'mode' => 'after_response_without_worker',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal notifikasi telegram peraturan baru', [
                'peraturan_id' => $peraturan->id ?? null,
                'pegawai_id' => $pegawai->id ?? null,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bangun isi pesan.
     *
     * @param  \App\peraturan  $peraturan
     * @param  \App\Pegawai    $pegawai
     * @param  string|null     $cabangName
     * @return string
     */
    protected function buildMessage(peraturan $peraturan, Pegawai $pegawai, $cabangName = null)
    {
        $cabangName = $cabangName ?: optional(Cabang::find($pegawai->cabang))->name;

        $lines = [
            "\xF0\x9F\x93\x9A <b>PERATURAN BARU</b>",
            '',
            '<b>Nama</b>: ' . e($peraturan->name),
            '<b>Nomor</b>: ' . e($peraturan->nosk ?? '-'),
            '<b>Jenis</b>: ' . e($peraturan->jenis_surat ?? '-'),
            '<b>Tanggal SK</b>: ' . e($peraturan->tglsk ?? '-'),
            '<b>Berlaku</b>: ' . e($peraturan->tgllaku ?? '-'),
        ];

        if (!empty($peraturan->uraian)) {
            $lines[] = '<b>Uraian</b>: ' . e($peraturan->uraian);
        }

        if ($cabangName) {
            $lines[] = '<b>Cabang</b>: ' . e($cabangName);
        }

        $lines[] = '';
        $lines[] = 'Silakan buka Aplikasi SIKAP BPR Cianjur untuk melihat detail peraturan.';

        return implode("\n", $lines);
    }
}
