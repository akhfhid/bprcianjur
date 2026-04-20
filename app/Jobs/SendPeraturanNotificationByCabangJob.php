<?php

namespace App\Jobs;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Helpers\WhatsAppHelper;
use App\Pegawai;
use App\peraturan;
use App\Jobs\SendPeraturanNotificationToPegawaiJob;
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
     * Daftar jabatan penerima notifikasi peraturan.
     *
     * @var array
     */
    protected $jabatanNotifikasiPeraturan = [
        'Pimpinan Cabang',
        'Kepala Bagian',
        'Kepala Divisi',
        'Kepala Seksi Kredit & Dana',
        'Kepala Seksi Umum & Akunting',
        'Kepala SKAI',
        'Kepala Kantor Kas',
    ];

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

        // 1 pegawai = 1 job. Job ini hanya menjadwalkan job per pegawai (tanpa sleep).
        // Rate limiting dilakukan di job per pegawai via Cache timestamp.
        $isSyncQueue = config('queue.default') === 'sync';

        foreach ($recipients->values() as $pegawai) {
            $payload = [
                'id' => (int) $pegawai->id,
                'name' => (string) ($pegawai->name ?? ''),
                'email' => (string) ($pegawai->email ?? ''),
                'cabang' => (int) ($pegawai->cabang ?? 0),
                'nohp' => $pegawai->nohp ?? null,
                'phone' => $pegawai->phone ?? null,
            ];

            if ($isSyncQueue) {
                // Queue sync tidak mendukung delay, jadi akan dieksekusi setelah response tanpa jeda.
                SendPeraturanNotificationToPegawaiJob::dispatchAfterResponse(
                    $peraturan->id,
                    $payload
                );
                continue;
            }

            SendPeraturanNotificationToPegawaiJob::dispatch(
                $peraturan->id,
                $payload
            );
        }

        Log::info('Selesai menjadwalkan notif peraturan per cabang', [
            'peraturan_id' => $this->peraturanId,
            'cabang_id' => $this->cabangId,
            'cabang_name' => $cabangName,
            'total' => $recipients->count(),
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
            ->where('cabang', $this->cabangId)
            ->whereNotNull('jabatan')
            ->whereHas('jabatan', function ($query) {
                $query->where(function ($jabatanQuery) {
                    foreach ($this->jabatanNotifikasiPeraturan as $jabatanName) {
                        $jabatanQuery->orWhere('name', 'like', $jabatanName.'%');
                    }
                });
            });

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

        $selectColumns = ['id', 'name', 'email', 'cabang', 'jabatan'];
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
