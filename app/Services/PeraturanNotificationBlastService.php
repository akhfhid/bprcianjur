<?php

namespace App\Services;

use App\Cabang;
use App\Jabatan;
use App\Helpers\NotificationLogHelper;
use App\Helpers\WhatsAppHelper;
use App\Services\TelegramPeraturanBaruNotificationService;
use App\Pegawai;
use App\peraturan;
use App\WaSetting;
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
    /**
     * Cache ID pegawai penerima notifikasi peraturan (kanal Telegram).
     *
     * @var array|null
     */
    protected $telegramRecipientIds = null;

    /**
     * Penghitung batch Telegram global lintas cabang.
     * Dipakai agar batas pesan/detik tidak di-reset saat pindah cabang.
     *
     * @var int|null
     */
    protected static $telegramBatchCounter = null;

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
            $telegramByCabang = $this->getTelegramRecipientsByCabang();

            if ($recipientsByCabang->isEmpty() && $telegramByCabang->isEmpty()) {
                Log::warning('Notifikasi peraturan dilewati karena tidak ada penerima aktif', [
                    'peraturan_id' => $peraturan->id,
                ]);
                return;
            }

            $delaySeconds = $this->getDelaySeconds();
            $delayPerCabang = $this->getDelayPerCabang();
            $isFirstSend = true;
            $isFirstCabang = true;
            $total = 0;

            // Urutan cabang mengikuti urutan pada WaSetting (sama seperti kanal WA),
            // lalu cabang yang hanya punya penerima Telegram ditambahkan di akhir.
            $allCabangIds = $recipientsByCabang->keys()
                ->merge($telegramByCabang->keys())
                ->unique()
                ->values();

            $orderedIds = $this->getOrderedCabangIds();
            if (!empty($orderedIds)) {
                $orderMap = array_flip($orderedIds);
                $allCabangIds = $allCabangIds
                    ->sortBy(function ($id) use ($orderMap) {
                        return $orderMap[(int) $id] ?? PHP_INT_MAX;
                    })
                    ->values();
            }

            foreach ($allCabangIds as $cabangId) {
                $recipients = $recipientsByCabang->get($cabangId, collect());
                $cabangName = optional(Cabang::find($cabangId))->name;

                // Delay antar-cabang (setelah cabang pertama)
                if (!$isFirstCabang && $delayPerCabang > 0) {
                    sleep($delayPerCabang);
                }
                $isFirstCabang = false;

                // ----------------------------------------------------------
                // Kanal WhatsApp (perilaku lama: jeda per orang)
                // ----------------------------------------------------------
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
                    $this->sendWaToPegawai($peraturan, $pegawai, $cabangName);
                }

                // ----------------------------------------------------------
                // Kanal Telegram (per cabang, 15 pesan/detik + jeda 1 detik)
                // ----------------------------------------------------------
                $this->sendTelegramPerCabang($peraturan, $cabangId, $cabangName);
            }

            Log::info('Blast notif peraturan selesai tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'total_wa' => $total,
                'delay_per_person' => $delaySeconds,
                'delay_per_cabang' => $delayPerCabang,
                'telegram_batch_size' => $this->getTelegramBatchSize(),
                'telegram_batch_delay' => $this->getTelegramBatchDelay(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menjalankan blast notif peraturan tanpa queue worker', [
                'peraturan_id' => $peraturan->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim notifikasi Telegram untuk satu cabang dengan aturan batching:
     * maksimal N pesan dalam satu detik, lalu jeda M detik.
     *
     * Default N=15, M=1 — tetap aman di bawah batas Telegram 20 pesan/detik.
     *
     * @param  \App\peraturan  $peraturan
     * @param  int            $cabangId
     * @param  string|null    $cabangName
     * @return void
     */
    protected function sendTelegramPerCabang(peraturan $peraturan, $cabangId, $cabangName = null)
    {
        $batchSize  = $this->getTelegramBatchSize();
        $batchDelay = $this->getTelegramBatchDelay();

        $recipients = Pegawai::query()
            ->where('cabang', $cabangId)
            ->where('status_active', 1)
            ->whereNotNull('tg_chat_id')
            ->where('tg_chat_id', '<>', '')
            ->whereIn('id', $this->telegramRecipientIds())
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'cabang', 'jabatan', 'tg_chat_id']);

        if ($recipients->isEmpty()) {
            Log::info('Blast telegram peraturan: tidak ada penerima terhubung di cabang ini', [
                'peraturan_id' => $peraturan->id,
                'cabang_id' => $cabangId,
                'cabang_name' => $cabangName,
            ]);
            return;
        }

        $service = app(TelegramPeraturanBaruNotificationService::class);
        $sent = 0;
        $total = $recipients->count();

        // Penghitung batch GLOBAL lintas cabang (static) agar batas
        // 15 pesan/detik tidak di-reset saat berpindah cabang.
        if (self::$telegramBatchCounter === null) {
            self::$telegramBatchCounter = 0;
        }

        foreach ($recipients as $pegawai) {
            $service->notify($peraturan, $pegawai, $cabangName);
            $sent++;
            self::$telegramBatchCounter++;

            // Jeda setiap kelipatan batchSize pesan (global, lintas cabang).
            if ((self::$telegramBatchCounter % $batchSize) === 0) {
                sleep($batchDelay);
            }
        }

        Log::info('Blast telegram peraturan per cabang selesai', [
            'peraturan_id' => $peraturan->id,
            'cabang_id' => $cabangId,
            'cabang_name' => $cabangName,
            'total_penerima' => $total,
            'batch_size' => $batchSize,
            'batch_delay' => $batchDelay,
        ]);
    }

    /**
     * ID pegawai penerima notifikasi peraturan Telegram: SEMUA pegawai aktif.
     * Di-cache per instance agar tidak query berulang tiap cabang.
     *
     * @return array
     */
    protected function telegramRecipientIds()
    {
        if ($this->telegramRecipientIds !== null) {
            return $this->telegramRecipientIds;
        }

        // Cakupan: SEMUA pegawai aktif (bukan hanya jabatan tertentu).
        // Yang belum menghubungkan Telegram akan dilewati otomatis.
        $ids = Pegawai::query()
            ->where('status_active', 1)
            ->pluck('id')
            ->all();

        return $this->telegramRecipientIds = $ids;
    }

    /**
     * Ukuran batch Telegram per detik (dibatasi maksimal 19 agar aman).
     *
     * @return int
     */
    protected function getTelegramBatchSize()
    {
        $size = (int) config('telegram_pulse.blast_batch_size', 15);

        if ($size < 1) {
            $size = 15;
        }
        if ($size > 19) {
            $size = 19;
        }

        return $size;
    }

    /**
     * Jeda antar batch Telegram (detik), minimal 1.
     *
     * @return int
     */
    protected function getTelegramBatchDelay()
    {
        $delay = (int) config('telegram_pulse.blast_batch_delay', 1);

        return $delay < 1 ? 1 : $delay;
    }
    /**
     * Urutan cabang dari pengaturan WA (dipakai juga untuk Telegram).
     *
     * @return array
     */
    protected function getOrderedCabangIds()
    {
        try {
            $setting = WaSetting::getSetting();
            $ordered = $setting->getCabangOrderArray();

            return is_array($ordered) ? array_map('intval', $ordered) : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Penerima notifikasi Telegram per cabang.
     * Cakupan: semua pegawai aktif, hanya yang sudah menghubungkan Telegram.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTelegramRecipientsByCabang()
    {
        $ids = $this->telegramRecipientIds();

        if (empty($ids)) {
            return collect();
        }

        // Semua pegawai aktif yang sudah menghubungkan Telegram.
        $rows = Pegawai::query()
            ->whereIn('id', $ids)
            ->where('status_active', 1)
            ->whereNotNull('cabang')
            ->where('cabang', '>', 0)
            ->whereNotNull('tg_chat_id')
            ->where('tg_chat_id', '<>', '')
            ->get(['id', 'name', 'email', 'cabang', 'jabatan', 'tg_chat_id']);

        return $rows->groupBy('cabang');
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

        // Ambil urutan cabang dari WaSetting
        $setting = WaSetting::getSetting();
        $orderedIds = $setting->getCabangOrderArray();

        // Urutkan cabangStats berdasarkan urutan yang tersimpan, fallback ke count asc
        if (!empty($orderedIds)) {
            $orderMap = array_flip($orderedIds); // id => position
            $cabangStats = $cabangStats->sortBy(function ($stat) use ($orderMap) {
                $id = (int) ($stat->cabang ?? 0);
                return $orderMap[$id] ?? PHP_INT_MAX;
            })->values();
        }

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
    protected function sendWaToPegawai(peraturan $peraturan, Pegawai $pegawai, $cabangName = null)
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
        try {
            $setting = WaSetting::getSetting();
            return max(0, (int) $setting->delay_per_person);
        } catch (\Throwable $e) {
            return max(0, (int) env('WA_THROTTLE_SECONDS', 10));
        }
    }

    /**
     * @return int
     */
    protected function getDelayPerCabang()
    {
        try {
            $setting = WaSetting::getSetting();
            return max(0, (int) $setting->delay_per_cabang);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * @return bool
     */
    protected function isBlastEnabled()
    {
        return filter_var(env('WA_PERATURAN_BLAST_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }
}
