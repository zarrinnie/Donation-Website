<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A gentle "come give again" reminder, sent once the duration a donor chose
 * (their time range, e.g. "1 Month" / "1 Year") has elapsed. This is separate
 * from the status-verification email (DonationStatusMail).
 */
class DonationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "It's time to renew your gift to ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.donation-reminder',
            with: ['donation' => $this->donation],
        );
    }
}
