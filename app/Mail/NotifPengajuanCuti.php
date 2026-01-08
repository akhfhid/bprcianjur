<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifPengajuanCuti extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
    public function build()
    {
        return $this->subject('Notifikasi Pengajuan Cuti Baru - BPR Cianjur')
                    ->view('emails.notif_cuti'); 
    }
}