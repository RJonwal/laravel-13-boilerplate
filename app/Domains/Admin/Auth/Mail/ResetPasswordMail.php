<?php

namespace App\Domains\Admin\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name,$language;
    protected $reset_password_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$reset_password_url)
    {
        $this->name = $name;
        $this->reset_password_url = $reset_password_url;
        $this->language = "en";
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Auth::emails.auth.reset-password', [
                'name' => $this->name,
                'reset_password_url' => $this->reset_password_url,
                'language' => $this->language,
            ])->subject(trans('emails.reset_password_admin_panel.subject'), [], $this->language);
    }
}
