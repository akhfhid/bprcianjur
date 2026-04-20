<?php

namespace App\Services;

use App\Cabang;
use App\Jabatan;
use App\Helpers\NotificationLogHelper;
use App\Helpers\WhatsAppHelper;
use App\Pegawai;
use App\peraturan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PeraturanNotificationBlastService
{
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
     * Jadwalkan blast setelah response dikirim ke browser.
     *
     * @param  \App\peraturan  $peraturan
     * @return void
     */
    public function sendAfterResponse(peraturan $peraturan)
    {
        app()->terminating(function () use ($peraturan) {
            $this->sendNow($peraturan);
        });
    }

    /**
     * Kirim notifikasi langsung tanpa queue worker.
     *
     * @param  \App\peraturan  $peraturan
     * @return void
     */
    public function sendNow(peraturan $peraturan)
    {
        try {
            if (!$this->isBlastEnabled()) {
                Log::info('Blast notif peraturan tidak dijalankan karena dinonaktifkan', [
                    'peraturan_id' => $peraturan->id,
                ]);
                return;
            }

            $recipientsByCabang = $this->getRecipientsByCabang();

            if ($recipientsByCabang->isEmpty()) {
                Log::warning('Notifikasi peraturan dilewati karena tidak ada penerima aktif', [
                    'peraturan_id' => $peraturan->id,
                ]);
                return;
            }

            $delaySeconds = $this->getDelaySeconds();
            $isFirstSend = true;
            $total = 0;

            foreach ($recipientsByCabang as $cabangId => $recipients) {
                $cabangName = optional(Cabang::find($cabangId))->name;

                foreach ($recipients as $pegawai) {
                    if (!$this->isBlastEnabled()) {
                        Log::warning('Blast notif peraturan dihentikan dari env', [
                            'peraturan_id' => $peraturan->id,
                            'total_sent_before_stop' => $total,
                        ]);
                        return;
                    }

                    if (!$isFirstSend && $delaySeconds > 0) {
                        sleep($delaySeconds);
                    }

                    $isFirstSend = false;
                    $total++;
                    $this->sendToPegawai($peraturan, $pegawai, $cabangName);
                }
            }

            Log::info('Blast notif peraturan selesai tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'total' => $total,
                'delay_seconds' => $delaySeconds,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menjalankan blast notif peraturan tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getRecipientsByCabang()
    {
        $hasNoHpColumn = Schema::hasColumn('pegawais', 'nohp');
        $hasPhoneColumn = Schema::hasColumn('pegawais', 'phone');

        if (!$hasNoHpColumn && !$hasPhoneColumn) {
            return collect();
        }

        $recipientQuery = Pegawai::query()
            ->whereNotNull('cabang')
            ->whereNotNull('jabatan')
            ->whereHas('jabatan', function ($query) {
                $query->where(function ($jabatanQuery) {
                    foreach ($this->jabatanNotifikasiPeraturan as $jabatanName) {
                        $jabatanQuery->orWhere('name', 'like', $jabatanName.'%');
                    }
                });
            });

        $recipientQuery->where(function ($query) use ($hasNoHpColumn, $hasPhoneColumn) {
            if ($hasNoHpColumn) {
                $query->where(function ($phoneQuery) {
                    $phoneQuery->whereNotNull('nohp')->where('nohp', '<>', '');
                });
            }

            if ($hasPhoneColumn) {
                if ($hasNoHpColumn) {
                    $query->orWhere(function ($phoneQuery) {
                        $phoneQuery->whereNotNull('phone')->where('phone', '<>', '');
                    });
                } else {
                    $query->where(function ($phoneQuery) {
                        $phoneQuery->whereNotNull('phone')->where('phone', '<>', '');
                    });
                }
            }
        });

        if (Schema::hasColumn('pegawais', 'status_active')) {
            $recipientQuery->where('status_active', 1);
        }

        $cabangStats = (clone $recipientQuery)
            ->select('cabang', DB::raw('COUNT(*) as recipient_count'))
            ->groupBy('cabang')
            ->orderBy('recipient_count', 'asc')
            ->orderBy('cabang', 'asc')
            ->get();

        $result = collect();

        foreach ($cabangStats as $stat) {
            $cabangId = (int) ($stat->cabang ?? 0);
            if ($cabangId <= 0) {
                continue;
            }

            $selectColumns = ['id', 'name', 'email', 'cabang', 'jabatan'];
            if (Schema::hasColumn('pegawais', 'kelamin')) {
                $selectColumns[] = 'kelamin';
            }
            if ($hasNoHpColumn) {
                $selectColumns[] = 'nohp';
            }
            if ($hasPhoneColumn) {
                $selectColumns[] = 'phone';
            }

            $recipients = (clone $recipientQuery)
                ->with(['jabatan', 'cabang'])
                ->where('cabang', $cabangId)
                ->orderBy('name', 'asc')
                ->get($selectColumns);

            if ($recipients->isNotEmpty()) {
                $result->put($cabangId, $recipients);
            }
        }

        return $result;
    }

    /**
     * @param  \App\peraturan  $peraturan
     * @param  \App\Pegawai  $pegawai
     * @param  string|null  $cabangName
     * @return void
     */
    protected function sendToPegawai(peraturan $peraturan, Pegawai $pegawai, $cabangName = null)
    {
        $jabatanId = $pegawai->getAttribute('jabatan');
        $cabangId = $pegawai->getAttribute('cabang');
        $jabatanName = optional($pegawai->getRelation('jabatan'))->name;
        $cabangName = $cabangName ?: optional($pegawai->getRelation('cabang'))->name;

        try {
            $result = WhatsAppHelper::sendPeraturanBaruNotificationToPegawai($peraturan, $pegawai);
            $recipientPhone = (string) (WhatsAppHelper::resolvePegawaiPhoneNumber($pegawai) ?? '');

            if (empty($result['success'])) {
                throw new \RuntimeException($result['message'] ?? 'Pengiriman WA gagal.');
            }

            NotificationLogHelper::success([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'wa',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $cabangId,
                'recipient_pegawai_id' => $pegawai->id,
                'recipient_name' => $pegawai->name,
                'recipient_email' => $pegawai->email,
                'recipient_phone' => $recipientPhone,
                'subject' => 'Peraturan Baru: '.$peraturan->name,
                'message' => 'Notifikasi WA berhasil dikirim.',
                'meta' => [
                    'cabang_name' => $cabangName,
                    'jabatan_name' => $jabatanName,
                    'mode' => 'after_response_without_worker',
                ],
            ]);

            Log::info('Notif peraturan WA success tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'pegawai_id' => $pegawai->id,
                'pegawai_name' => $pegawai->name,
                'cabang_id' => $cabangId,
                'cabang_name' => $cabangName,
                'jabatan_id' => $jabatanId,
                'jabatan_name' => $jabatanName,
            ]);
        } catch (\Throwable $e) {
            NotificationLogHelper::error([
                'actor_id' => $peraturan->created_by ?? null,
                'category' => 'peraturan',
                'channel' => 'wa',
                'reference_type' => 'peraturan',
                'reference_id' => $peraturan->id,
                'cabang_id' => $cabangId,
                'recipient_pegawai_id' => $pegawai->id,
                'recipient_name' => $pegawai->name,
                'recipient_email' => $pegawai->email,
                'recipient_phone' => (string) (WhatsAppHelper::resolvePegawaiPhoneNumber($pegawai) ?? ''),
                'subject' => 'Peraturan Baru: '.$peraturan->name,
                'message' => 'Notifikasi gagal dikirim.',
                'error_message' => $e->getMessage(),
                'meta' => [
                    'cabang_name' => $cabangName,
                    'jabatan_name' => $jabatanName,
                    'mode' => 'after_response_without_worker',
                ],
            ]);

            Log::warning('Gagal kirim notif peraturan tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'pegawai_id' => $pegawai->id,
                'pegawai_name' => $pegawai->name,
                'cabang_id' => $cabangId,
                'cabang_name' => $cabangName,
                'jabatan_id' => $jabatanId,
                'jabatan_name' => $jabatanName,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return int
     */
    protected function getDelaySeconds()
    {
        $delaySeconds = (int) env('WA_THROTTLE_SECONDS', 10);

        return max(0, $delaySeconds);
    }

    /**
     * @return bool
     */
    protected function isBlastEnabled()
    {
        return filter_var(env('WA_PERATURAN_BLAST_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }
}
