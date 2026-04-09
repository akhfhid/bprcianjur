<?php

namespace App\Helpers;

use App\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class NotificationLogHelper
{
    /**
     * Simpan log notifikasi dengan status sukses.
     *
     * @param  array  $data
     * @return void
     */
    public static function success(array $data)
    {
        self::write(array_merge($data, ['status' => 'success']));
    }

    /**
     * Simpan log notifikasi dengan status gagal.
     *
     * @param  array  $data
     * @return void
     */
    public static function error(array $data)
    {
        self::write(array_merge($data, ['status' => 'error']));
    }

    /**
     * Simpan log notifikasi.
     *
     * @param  array  $data
     * @return void
     */
    public static function write(array $data)
    {
        try {
            if (!Schema::hasTable('notification_logs')) {
                return;
            }

            NotificationLog::create([
                'actor_id' => $data['actor_id'] ?? null,
                'category' => $data['category'] ?? 'unknown',
                'channel' => $data['channel'] ?? 'unknown',
                'status' => $data['status'] ?? 'success',
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'cabang_id' => $data['cabang_id'] ?? null,
                'recipient_pegawai_id' => $data['recipient_pegawai_id'] ?? null,
                'recipient_name' => $data['recipient_name'] ?? null,
                'recipient_email' => $data['recipient_email'] ?? null,
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'subject' => $data['subject'] ?? null,
                'message' => $data['message'] ?? null,
                'error_message' => $data['error_message'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan notification_logs', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}

