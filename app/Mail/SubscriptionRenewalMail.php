<?php

namespace App\Mail;

use App\Models\DonationSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The monthly "it's time to give again" email for a recurring donor. Carries
 * a signed link to the payment review page — distinct from DonationReminderMail
 * (the one-off, non-recurring "come give again" nudge for a single gift's
 * chosen support period).
 */
class SubscriptionRenewalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DonationSubscription $subscription,
        public string $reviewUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "It's time to renew your monthly gift to ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription-renewal',
            with: [
                'subscription' => $this->subscription,
                'reviewUrl' => $this->reviewUrl,
            ],
        );
    }
}
