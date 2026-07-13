<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body style="font-family: sans-serif; padding: 24px;"><h2>Test Email</h2><p>This is a test email from <strong>' . e(config('app.name')) . '</strong>.</p><p>If you received this, your SMTP configuration is working correctly.</p><hr><p style="color: #888; font-size: 12px;">Sent at ' . now()->format('Y-m-d H:i:s') . '</p></body></html>',
        );
    }
}
