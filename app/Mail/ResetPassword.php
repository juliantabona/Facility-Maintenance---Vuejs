<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $redirectTo;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $redirectTo)
    {
        $this->user = $user;
        $this->redirectTo = $redirectTo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password!')->view('emails.reset_password');
    }
}
