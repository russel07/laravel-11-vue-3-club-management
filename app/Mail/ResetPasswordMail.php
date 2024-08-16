<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.reset_password')
                    ->with([
                        'url' => URL::temporarySignedRoute(
                            'password.reset',
                            now()->addMinutes(60),
                            ['token' => $this->token]
                        )
                    ]);
    }
}
