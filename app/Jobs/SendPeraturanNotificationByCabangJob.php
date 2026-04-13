<?php

namespace App\Jobs;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Helpers\WhatsAppHelper;
use App\Pegawai;
use App\peraturan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendPeraturanNotificationByCabangJob implements ShouldQueue
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
    public $tries = 3;

    /**
     * @var int
     */
    protected $peraturanId;

    /**
     * @var int
     */
    protected $cabangId;

    /**
     * Delay awal (detik), dipakai saat queue connection = sync.
     *
     * @var int
     */
    protected $syncDelaySeconds;

    /**
     * Create a new job instance.
     *
     * @param  int  $peraturanId
     * @param  int  $cabangId
     * @param  int  $syncDelaySeconds
     * @return void
     */
    public function __construct($peraturanId, $cabangId, $syncDelaySeconds = 0)
    {
        $this->peraturanId = (int) $peraturanId;
        $this->cabangId = (int) $cabangId;
        $this->syncDelaySeconds = (int) $syncDelaySeconds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->syncDelaySeconds > 0 && config('queue.default') === 'sync') {
            sleep($this->syncDelaySeconds);
        }

        $peraturan = peraturan::find($this->peraturanId);
        if (!$peraturan) {
            NotificationLogHelper::error([
                'category' => 'peraturan',
                'channel' => 'system',
                'reference_type' => 'peraturan',
                'reference_id' => $this->peraturanId,
                'cabang_id' => $this->cabangId,
                'message' => 'Peraturan tidak ditemukan saat proses notifikasi.',
                'error_message' => 'Data peraturan tidak tersedia.',
            ]);

            Log::warning('Peraturan tidak ditemukan saat proses notif cabang', [
                'peraturan_id' => $this->peraturanId,
                'cabang_id' => $this->cabangId,
            ]);
            return;
        }

        $cabangName = optional(Cabang::find($this->cabangId))->name;
        $recipients = $this->getRecipients();
        if ($recipients->isEmpty()) {
            NotificationLogHelper::error([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'system',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $this->cabangId,
                'message' => 'Tidak ada penerima notifikasi di cabang ini.',
                'error_message' => 'Semua penerima kosong/tidak aktif.',
                'meta' => [
                    'cabang_name' => $cabangName,
                ],
            ]);

            Log::info('Tidak ada penerima notif pada cabang ini', [
                'peraturan_id' => $this->peraturanId,
                'cabang_id' => $this->cabangId,
                'cabang_name' => $cabangName,
            ]);
            return;
        }

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $pegawai) {
            try {
                $result = WhatsAppHelper::sendPeraturanBaruNotificationToPegawai($peraturan, $pegawai);

                if (empty($result['success'])) {
                    throw new \RuntimeException($result['message'] ?? 'Pengiriman WA gagal.');
                }

                NotificationLogHelper::success([
                    'actor_id' => $peraturan->created_by ?? null,
                    'category' => 'peraturan',
                    'channel' => 'wa',
                    'reference_type' => 'peraturan',
                    'reference_id' => $peraturan->id,
                    'cabang_id' => $this->cabangId,
                    'recipient_pegawai_id' => $pegawai->id,
                    'recipient_name' => $pegawai->name,
                    'recipient_email' => $pegawai->email,
                    'recipient_phone' => $this->resolveWaTarget($pegawai),
                    'subject' => 'Peraturan Baru: '.$peraturan->name,
                    'message' => 'Notifikasi WA berhasil dikirim.',
                    'meta' => [
                        'cabang_name' => $cabangName,
                    ],
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $failed++;

                NotificationLogHelper::error([
                    'actor_id' => $peraturan->created_by ?? null,
                    'category' => 'peraturan',
                    'channel' => 'wa',
                    'reference_type' => 'peraturan',
                    'reference_id' => $peraturan->id,
                    'cabang_id' => $this->cabangId,
                    'recipient_pegawai_id' => $pegawai->id,
                    'recipient_name' => $pegawai->name,
                    'recipient_email' => $pegawai->email,
                    'recipient_phone' => $this->resolveWaTarget($pegawai),
                    'subject' => 'Peraturan Baru: '.$peraturan->name,
                    'message' => 'Notifikasi gagal dikirim.',
                    'error_message' => $e->getMessage(),
                    'meta' => [
                        'cabang_name' => $cabangName,
                    ],
                ]);

                Log::warning('Gagal kirim notif peraturan ke penerima', [
                    'peraturan_id' => $this->peraturanId,
                    'cabang_id' => $this->cabangId,
                    'pegawai_id' => $pegawai->id,
                    'email' => $pegawai->email,
                    'nohp' => $pegawai->nohp ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Selesai proses notif peraturan per cabang', [
            'peraturan_id' => $this->peraturanId,
            'cabang_id' => $this->cabangId,
            'cabang_name' => $cabangName,
            'total' => $recipients->count(),
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getRecipients()
    {
        $hasNoHpColumn = Schema::hasColumn('pegawais', 'nohp');
        $hasPhoneColumn = Schema::hasColumn('pegawais', 'phone');

        if (!$hasNoHpColumn && !$hasPhoneColumn) {
            return collect();
        }

        $query = Pegawai::query()
            ->where('cabang', $this->cabangId);

        if (Schema::hasColumn('pegawais', 'status_active')) {
            $query->where('status_active', 1);
        }

        $query->where(function ($receiverQuery) use ($hasNoHpColumn, $hasPhoneColumn) {
            if ($hasNoHpColumn) {
                $receiverQuery->where(function ($q) {
                    $q->whereNotNull('nohp')->where('nohp', '<>', '');
                });
            }

            if ($hasPhoneColumn) {
                if ($hasNoHpColumn) {
                    $receiverQuery->orWhere(function ($q) {
                        $q->whereNotNull('phone')->where('phone', '<>', '');
                    });
                } else {
                    $receiverQuery->where(function ($q) {
                        $q->whereNotNull('phone')->where('phone', '<>', '');
                    });
                }
            }
        });

        $selectColumns = ['id', 'name', 'email', 'cabang'];
        if ($hasNoHpColumn) {
            $selectColumns[] = 'nohp';
        }
        if ($hasPhoneColumn) {
            $selectColumns[] = 'phone';
        }

        return $query->get($selectColumns);
    }

    /**
     * @param  \App\Pegawai  $pegawai
     * @return string
     */
    protected function resolveWaTarget(Pegawai $pegawai)
    {
        return (string) (WhatsAppHelper::resolvePegawaiPhoneNumber($pegawai) ?? '');
    }
}
