<?php

namespace App\Mail;

use App\Pegawai;
use App\peraturan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifPeraturanBaru extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\peraturan
     */
    public $peraturan;

    /**
     * @var \App\Pegawai
     */
    public $pegawai;

    /**
     * @var string|null
     */
    public $cabangName;

    /**
     * Create a new message instance.
     *
     * @param  \App\peraturan  $peraturan
     * @param  \App\Pegawai  $pegawai
     * @param  string|null  $cabangName
     * @return void
     */
    public function __construct(peraturan $peraturan, Pegawai $pegawai, $cabangName = null)
    {
        $this->peraturan = $peraturan;
        $this->pegawai = $pegawai;
        $this->cabangName = $cabangName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Peraturan Baru: '.$this->peraturan->name)
            ->view('emails.notif_peraturan_baru');
    }
}
