<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordCodeNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $expiredMinutes;

    /**
     * @var string
     */
    private $systemName;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @param string $code
     * @param int $expiredMinutes
     * @param string $systemName
     * @param string $companyName
     */
    public function __construct($code, $expiredMinutes = 3, $systemName = 'Sistem Kepegawaian dan Peraturan', $companyName = 'BPR CIANJUR JABAR')
    {
        $this->code = $code;
        $this->expiredMinutes = $expiredMinutes;
        $this->systemName = $systemName;
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $viewData = [
            'namaUser' => $notifiable->name ? $notifiable->name : 'Pengguna',
            'code' => $this->code,
            'expiredMinutes' => $this->expiredMinutes,
            'systemName' => $this->systemName,
            'companyName' => $this->companyName,
            'supportEmail' => config('mail.from.address', '-'),
        ];

        return (new MailMessage)
            ->subject('Kode Reset Password - ' . $this->companyName)
            ->view(
                ['emails.auth.reset-password-code', 'emails.auth.reset-password-code-plain'],
                $viewData
            );
    }
}
