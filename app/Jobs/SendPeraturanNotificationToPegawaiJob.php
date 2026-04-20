<?php

namespace App\Jobs;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Helpers\WhatsAppHelper;
use App\peraturan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendPeraturanNotificationToPegawaiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Batas maksimal eksekusi job (detik).
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * Jumlah retry saat job gagal.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * @var int
     */
    protected $peraturanId;

    /**
     * Payload pegawai agar job tidak query ulang DB.
     * Field minimal: id, name, cabang, nohp/phone, email (optional)
     *
     * @var array
     */
    protected $pegawai;

    /**
     * @param  int  $peraturanId
     * @param  array  $pegawai
     * @return void
     */
    public function __construct($peraturanId, array $pegawai)
    {
        $this->peraturanId = (int) $peraturanId;
        $this->pegawai = $pegawai;
    }

    /**
     * @return void
     */
    public function handle()
    {
        // Global throttle: 1 request / N detik (default 30) tanpa sleep.
        // Implementasi sederhana untuk kompatibilitas Laravel versi lama (tanpa RateLimiter facade):
        // simpan timestamp terakhir kirim di cache, lalu release job dengan sisa waktu jika masih dalam window.
        $throttleSeconds = (int) (env('WA_THROTTLE_SECONDS', 30));
        if ($throttleSeconds < 1) {
            $throttleSeconds = 30;
        }

        $cacheKey = 'wa:send:last_ts';
        $lockKey = 'wa:send:lock';
        $now = time();

        $lock = null;
        $lockAcquired = false;
        try {
            // Gunakan lock bila tersedia untuk mengurangi race antar worker (opsional, tergantung cache driver).
            if (method_exists(Cache::getFacadeRoot(), 'lock')) {
                $lock = Cache::lock($lockKey, 10);
                $lockAcquired = (bool) $lock->get();
            }

            $lastTs = (int) Cache::get($cacheKey, 0);
            $elapsed = $lastTs > 0 ? ($now - $lastTs) : PHP_INT_MAX;

            if ($elapsed < $throttleSeconds) {
                $remaining = $throttleSeconds - $elapsed;
                // Jitter kecil untuk menghindari thundering herd ketika beberapa job release bersamaan.
                $delay = max(1, (int) $remaining) + random_int(0, 2);

                Log::info('WA throttled, releasing job', [
                    'peraturan_id' => $this->peraturanId,
                    'pegawai_id' => $this->pegawai['id'] ?? null,
                    'attempt' => method_exists($this, 'attempts') ? $this->attempts() : null,
                    'delay_seconds' => $delay,
                ]);

                $this->release($delay);
                return;
            }

            // Ambil slot throttle untuk request ini.
            // TTL dibuat lebih panjang dari window agar key tidak cepat hilang di beberapa driver.
            Cache::put($cacheKey, $now, $throttleSeconds * 5);
        } finally {
            if ($lockAcquired && $lock) {
                $lock->release();
            }
        }

        $peraturan = peraturan::find($this->peraturanId);
        if (!$peraturan) {
            NotificationLogHelper::error([
                'category' => 'peraturan',
                'channel' => 'system',
                'reference_type' => 'peraturan',
                'reference_id' => $this->peraturanId,
                'message' => 'Peraturan tidak ditemukan saat proses notifikasi.',
                'error_message' => 'Data peraturan tidak tersedia.',
            ]);

            Log::warning('Peraturan tidak ditemukan saat proses notif pegawai', [
                'peraturan_id' => $this->peraturanId,
                'pegawai_id' => $this->pegawai['id'] ?? null,
                'cabang_id' => $this->pegawai['cabang'] ?? null,
            ]);
            return;
        }

        $pegawaiId = (int) ($this->pegawai['id'] ?? 0);
        $pegawaiName = (string) ($this->pegawai['name'] ?? '');
        $pegawaiEmail = (string) ($this->pegawai['email'] ?? '');
        $pegawaiCabang = (int) ($this->pegawai['cabang'] ?? 0);
        $pegawaiNoHp = $this->pegawai['nohp'] ?? null;
        $pegawaiPhone = $this->pegawai['phone'] ?? null;

        if ($pegawaiId <= 0 || $pegawaiCabang <= 0) {
            NotificationLogHelper::error([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'system',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $pegawaiCabang ?: null,
                'recipient_pegawai_id' => $pegawaiId ?: null,
                'message' => 'Payload pegawai tidak valid untuk pengiriman notifikasi.',
                'error_message' => 'pegawai_id atau cabang kosong.',
            ]);
            return;
        }

        // Buat object ringan agar kompatibel dengan resolver nomor WA yang sudah ada.
        $pegawaiObj = (object) [
            'id' => $pegawaiId,
            'name' => $pegawaiName,
            'email' => $pegawaiEmail,
            'cabang' => $pegawaiCabang,
            'nohp' => $pegawaiNoHp,
            'phone' => $pegawaiPhone,
        ];

        $cabangName = optional(Cabang::find($pegawaiCabang))->name;

        try {
            $result = WhatsAppHelper::sendPeraturanBaruNotificationToPegawai($peraturan, $pegawaiObj);

            if (empty($result['success'])) {
                $httpCode = $result['http_code'] ?? null;
                $message = $result['message'] ?? 'Pengiriman WA gagal.';

                // Jika API mengembalikan 429, lakukan retry dengan backoff lebih panjang.
                if ((int) $httpCode === 429) {
                    $attempt = method_exists($this, 'attempts') ? $this->attempts() : 1;
                    $backoff = min(900, (int) (pow(2, max(0, $attempt - 1)) * 60)); // 60s, 120s, 240s, ...
                    $jitter = random_int(0, 10);
                    $delay = $backoff + $jitter;

                    Log::warning('WA 429 rate limited, releasing job', [
                        'peraturan_id' => $this->peraturanId,
                        'pegawai_id' => $pegawaiId,
                        'attempt' => $attempt,
                        'delay_seconds' => $delay,
                        'message' => $message,
                    ]);

                    $this->release($delay);
                    return;
                }

                throw new \RuntimeException($message);
            }

            NotificationLogHelper::success([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'wa',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $pegawaiCabang,
                'recipient_pegawai_id' => $pegawaiId,
                'recipient_name' => $pegawaiName,
                'recipient_email' => $pegawaiEmail,
                'recipient_phone' => (string) (WhatsAppHelper::resolvePegawaiPhoneNumber($pegawaiObj) ?? ''),
                'subject' => 'Peraturan Baru: '.$peraturan->name,
                'message' => 'Notifikasi WA berhasil dikirim.',
                'meta' => [
                    'cabang_name' => $cabangName,
                    'attempt' => method_exists($this, 'attempts') ? $this->attempts() : null,
                ],
            ]);

            Log::info('Notif peraturan WA success', [
                'peraturan_id' => $this->peraturanId,
                'pegawai_id' => $pegawaiId,
                'cabang_id' => $pegawaiCabang,
                'attempt' => method_exists($this, 'attempts') ? $this->attempts() : null,
            ]);
        } catch (\Throwable $e) {
            NotificationLogHelper::error([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'wa',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $pegawaiCabang,
                'recipient_pegawai_id' => $pegawaiId,
                'recipient_name' => $pegawaiName,
                'recipient_email' => $pegawaiEmail,
                'recipient_phone' => (string) (WhatsAppHelper::resolvePegawaiPhoneNumber($pegawaiObj) ?? ''),
                'subject' => 'Peraturan Baru: '.$peraturan->name,
                'message' => 'Notifikasi gagal dikirim.',
                'error_message' => $e->getMessage(),
                'meta' => [
                    'cabang_name' => $cabangName,
                    'attempt' => method_exists($this, 'attempts') ? $this->attempts() : null,
                ],
            ]);

            Log::warning('Gagal kirim notif peraturan ke pegawai', [
                'peraturan_id' => $this->peraturanId,
                'pegawai_id' => $pegawaiId,
                'cabang_id' => $pegawaiCabang,
                'email' => $pegawaiEmail,
                'nohp' => $pegawaiNoHp,
                'message' => $e->getMessage(),
                'attempt' => method_exists($this, 'attempts') ? $this->attempts() : null,
            ]);

            // Lempar exception agar mekanisme retry queue jalan (network error, timeout, dsb).
            throw $e;
        }
    }

    /**
     * Exponential backoff untuk failure umum (timeout/network), max 3 attempts.
     * 429 ditangani khusus via release() agar bisa lebih panjang.
     *
     * @return array
     */
    public function backoff()
    {
        return [30, 120, 300];
    }

    /**
     * Logging ketika job gagal permanen (tries habis).
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed($exception)
    {
        Log::error('Notif peraturan WA final failure', [
            'peraturan_id' => $this->peraturanId,
            'pegawai_id' => $this->pegawai['id'] ?? null,
            'cabang_id' => $this->pegawai['cabang'] ?? null,
            'message' => $exception ? $exception->getMessage() : null,
        ]);
    }
}
