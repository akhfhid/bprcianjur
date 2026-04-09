<?php

namespace App\Jobs;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Mail\NotifPeraturanBaru;
use App\Pegawai;
use App\peraturan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $apiUrl = trim((string) config('services.peraturan_notif.url'));
        $fallbackEmail = (bool) config('services.peraturan_notif.fallback_email', false);
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $pegawai) {
            try {
                if ($apiUrl !== '') {
                    try {
                        $this->sendViaApi($apiUrl, $pegawai, $peraturan, $cabangName);

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
                    } catch (\Throwable $waException) {
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
                            'message' => 'Pengiriman WA gagal.',
                            'error_message' => $waException->getMessage(),
                            'meta' => [
                                'cabang_name' => $cabangName,
                                'fallback' => 'email',
                            ],
                        ]);

                        if ($fallbackEmail && !empty($pegawai->email)) {
                            Mail::to($pegawai->email)->send(new NotifPeraturanBaru($peraturan, $pegawai, $cabangName));

                            NotificationLogHelper::success([
                                'actor_id' => $peraturan->created_by ?? null,
                                'category' => 'peraturan',
                                'channel' => 'email',
                                'reference_type' => 'peraturan',
                                'reference_id' => $peraturan->id,
                                'cabang_id' => $this->cabangId,
                                'recipient_pegawai_id' => $pegawai->id,
                                'recipient_name' => $pegawai->name,
                                'recipient_email' => $pegawai->email,
                                'recipient_phone' => $this->resolveWaTarget($pegawai),
                                'subject' => 'Peraturan Baru: '.$peraturan->name,
                                'message' => 'Fallback email berhasil dikirim setelah WA gagal.',
                                'meta' => [
                                    'cabang_name' => $cabangName,
                                ],
                            ]);
                        } else {
                            throw $waException;
                        }
                    }
                } else {
                    Mail::to($pegawai->email)->send(new NotifPeraturanBaru($peraturan, $pegawai, $cabangName));

                    NotificationLogHelper::success([
                        'actor_id' => $peraturan->created_by ?? null,
                        'category' => 'peraturan',
                        'channel' => 'email',
                        'reference_type' => 'peraturan',
                        'reference_id' => $peraturan->id,
                        'cabang_id' => $this->cabangId,
                        'recipient_pegawai_id' => $pegawai->id,
                        'recipient_name' => $pegawai->name,
                        'recipient_email' => $pegawai->email,
                        'recipient_phone' => $this->resolveWaTarget($pegawai),
                        'subject' => 'Peraturan Baru: '.$peraturan->name,
                        'message' => 'Notifikasi email berhasil dikirim.',
                        'meta' => [
                            'cabang_name' => $cabangName,
                        ],
                    ]);
                }
                $sent++;
            } catch (\Throwable $e) {
                $failed++;

                NotificationLogHelper::error([
                    'actor_id' => $peraturan->created_by ?? null,
                    'category' => 'peraturan',
                    'channel' => $apiUrl !== '' ? 'wa' : 'email',
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
        $useApi = trim((string) config('services.peraturan_notif.url')) !== '';
        $hasNoHpColumn = Schema::hasColumn('pegawais', 'nohp');
        $hasPhoneColumn = Schema::hasColumn('pegawais', 'phone');

        $query = Pegawai::query()
            ->where('cabang', $this->cabangId);

        if (Schema::hasColumn('pegawais', 'status_active')) {
            $query->where('status_active', 1);
        }

        if ($useApi && ($hasNoHpColumn || $hasPhoneColumn)) {
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
        } else {
            $query->whereNotNull('email')->where('email', '<>', '');
        }

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
     * Kirim ke API eksternal jika URL API diisi.
     *
     * @param  string  $apiUrl
     * @param  \App\Pegawai  $pegawai
     * @param  \App\peraturan  $peraturan
     * @param  string|null  $cabangName
     * @return void
     */
    protected function sendViaApi($apiUrl, Pegawai $pegawai, peraturan $peraturan, $cabangName = null)
    {
        $timeout = (int) config('services.peraturan_notif.timeout', 20);
        $apiCode = (string) config('services.peraturan_notif.code', '');
        $target = $this->resolveWaTarget($pegawai);

        if ($target === '') {
            throw new \RuntimeException('Nomor WA penerima tidak tersedia');
        }

        $message = $this->buildWaMessage($pegawai, $peraturan, $cabangName);

        $request = Http::timeout($timeout)->retry(2, 1000)->asForm();

        $payload = [
            'api' => $apiCode,
            'code' => $apiCode,
            'target' => $target,
            'message' => $message,
            'event' => 'peraturan_baru',
            'pegawai_id' => $pegawai->id,
            'email' => $pegawai->email,
            'cabang_id' => $this->cabangId,
            'cabang_name' => $cabangName,
            'peraturan_id' => $peraturan->id,
            'peraturan_name' => $peraturan->name,
            'nosk' => $peraturan->nosk,
            'tglsk' => $peraturan->tglsk,
            'tgllaku' => $peraturan->tgllaku,
        ];

        $response = $request->post($apiUrl, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('API notification failed with status '.$response->status());
        }
    }

    /**
     * @param  \App\Pegawai  $pegawai
     * @return string
     */
    protected function resolveWaTarget(Pegawai $pegawai)
    {
        $candidates = [
            $pegawai->nohp ?? '',
            $pegawai->phone ?? '',
        ];

        foreach ($candidates as $candidate) {
            $raw = trim((string) $candidate);
            if ($raw === '') {
                continue;
            }

            $normalized = preg_replace('/[^0-9]/', '', $raw);
            if ($normalized === '') {
                continue;
            }

            if (strpos($normalized, '0') === 0) {
                $normalized = '62'.substr($normalized, 1);
            }

            if (strpos($normalized, '62') !== 0) {
                $normalized = '62'.$normalized;
            }

            return $normalized;
        }

        return '';
    }

    /**
     * @param  \App\Pegawai  $pegawai
     * @param  \App\peraturan  $peraturan
     * @param  string|null  $cabangName
     * @return string
     */
    protected function buildWaMessage(Pegawai $pegawai, peraturan $peraturan, $cabangName = null)
    {
        $lines = [
            'Halo '.$pegawai->name.',',
            'Ada peraturan baru yang telah dipublikasikan.',
            'Nama: '.$peraturan->name,
            'No SK: '.$peraturan->nosk,
            'Tgl SK: '.$peraturan->tglsk,
            'Tgl Berlaku: '.$peraturan->tgllaku,
        ];

        if (!empty($cabangName)) {
            $lines[] = 'Cabang: '.$cabangName;
        }

        $lines[] = 'Silakan login ke aplikasi SIKAP untuk melihat detail dokumen.';

        return implode("\n", $lines);
    }
}
