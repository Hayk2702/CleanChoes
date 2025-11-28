<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $email = $this->subject('New Contact Request')
            ->view('emails.contact')
            ->with('data', $this->data);

        // If photo exists and you want it attached to email
        if (!empty($this->data['photo']) && file_exists(public_path($this->data['photo']))) {
            $email->attach(public_path($this->data['photo']));
        }

        return $email;
    }
}
