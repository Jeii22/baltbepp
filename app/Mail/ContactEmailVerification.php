<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Verify Contact Email - Balt-Bep')
            ->view('emails.contact-email-verification')
            ->with([
                'code' => $this->code,
                'expiresMinutes' => 10,
            ]);
    }
}
