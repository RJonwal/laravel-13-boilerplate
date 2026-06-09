<?php

namespace App\Domains\Api\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $language;

    public function __construct($user)
    {
        $this->user = $user;
        $this->language = 'en';
    }

    public function build()
    {
        return $this->markdown('Layouts::emails.auth.welcome_customer', [
                'user' => $this->user,
                'language' => $this->language,
            ])->subject(trans('emails.user_register_welcome_mail_customer.subject', [], $this->language ?? 'en'));
    }
}
