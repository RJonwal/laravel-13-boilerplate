<?php

namespace App\Domains\Api\Profile\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $user;
    public $otp;
    public $subjectText;
    public $expireMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $otp, $subjectText, $expireMinutes)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->subjectText = $subjectText;
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
            'expireMinutes' => $this->expireMinutes])->subject($this->subjectText);
    }
}
