<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule
            ->call(function () {
                $tahunSekarang = now()->year;

                $pegawais = \App\Pegawai::all();

                foreach ($pegawais as $pegawai) {
                    $pegawai->scuti = 12;

                    $cutiTahunIni = \App\ordercuti::where('pegawai_id', $pegawai->id)->where('status', 'DISETUJUI')->whereYear('tglawal', $tahunSekarang)->sum('jmlcuti');

                    $pegawai->scuti -= $cutiTahunIni;

                    if ($pegawai->scuti < 0) {
                        $pegawai->scuti = 0;
                    }

                    $pegawai->save();
                }
            })
            ->yearlyOn(1, 1, '00:01');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
