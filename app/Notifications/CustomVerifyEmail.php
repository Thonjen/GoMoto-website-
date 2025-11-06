<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Use a blade view so we can include the brand logo and full HTML formatting.
        $logoUrl = asset('storage/assets/GoMoto(Colored).png');

        return (new MailMessage)
            ->subject(Lang::get('🔑 Verify Your GoMoto Account'))
            ->view('emails.verify', [
                'verificationUrl' => $verificationUrl,
                'logo' => $logoUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
