<?php

namespace App\Domains\Api\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user,$token, $language , $expiretime;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$token, $expiretime)
    {
        $this->user = $user;
        $this->token = $token;
        $this->language = 'en';
        $this->expiretime = $expiretime;
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Auth::emails.auth.forgot_password_otp', [
            'user' => $this->user ,
            'token' => $this->token ,
            'expiretime' => $this->expiretime])->subject(trans('emails.reset_password_mobile_app.subject', [], $this->language));
    }
}
