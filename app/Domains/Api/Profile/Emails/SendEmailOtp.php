<?php

namespace App\Domains\Api\Profile\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $otp;
    public $language="en";
    public $expireMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $otp, $expireMinutes)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->expireMinutes = $expireMinutes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->markdown('Auth::emails.send-email-otp', [
            'email' => $this->email ,
            'otp' => $this->otp ,
            'expireMinutes' => $this->expireMinutes,
            'language' => $this->language
        ])->subject(trans('emails.profile_verify_email_otp.subject', [], $this->language));
    }
}
