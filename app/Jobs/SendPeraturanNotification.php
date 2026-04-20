<?php

namespace App\Jobs;

use App\peraturan;
use App\Pegawai;
use App\Helpers\WhatsAppHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPeraturanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $peraturan;

    /**
     * Create a new job instance.
     */
    public function __construct($peraturan)
    {
        $this->peraturan = $peraturan;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $delayPerPegawaiSeconds = 10;

        $selectColumns = ['id', 'name', 'nohp', 'jabatan'];
        if (\Schema::hasColumn('pegawais', 'kelamin')) {
            $selectColumns[] = 'kelamin';
        }

        $query = Pegawai::query()
            ->select($selectColumns)
            ->with(['jabatan'])
            ->whereNotNull('nohp')
            ->where('nohp', '<>', '');

        if (\Schema::hasColumn('pegawais', 'status_active')) {
            $query->where('status_active', 1);
        }

        $pegawais = $query->orderBy('name')->get();

        $sentPhones = [];

        foreach ($pegawais as $pegawai) {
            $normalizedPhone = WhatsAppHelper::convertPhoneNumber($pegawai->nohp);

            if (!$normalizedPhone || isset($sentPhones[$normalizedPhone])) {
                continue;
            }

            $sentPhones[$normalizedPhone] = true;

            WhatsAppHelper::sendPeraturanBaruNotificationToPegawai($this->peraturan, $pegawai);

            sleep($delayPerPegawaiSeconds);
        }
    }
}
