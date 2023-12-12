<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class AccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name  = $this->data['name'];
        $email  = $this->data['email'];
        $password  = $this->data['password'];

        return $this->markdown('vendor.mail.html.accountcreated')
        ->with('slot','1')
        ->with('name', $name)
        ->with('email', $email)
        ->with('password', $password);
    }

}
