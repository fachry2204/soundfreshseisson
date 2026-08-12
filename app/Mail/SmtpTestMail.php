<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SmtpTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $recipient,
        public readonly string $senderName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test Email SMTP — Original Sessions');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.smtp-test');
    }
}
