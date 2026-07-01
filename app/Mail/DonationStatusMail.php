<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->donation->status) {
            Donation::STATUS_SUCCESSFUL => 'Your donation to Grace Community Church is confirmed',
            Donation::STATUS_FAILED => 'There was a problem with your donation',
            default => 'Your donation to Grace Community Church is being processed',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        // Status-specific copy shown in the email body.
        $messages = [
            Donation::STATUS_SUCCESSFUL => [
                'heading' => 'Thank you — your donation was successful!',
                'body' => 'We have received and verified your generous gift. Your support helps Grace Community Church continue its mission. May God bless you.',
            ],
            Donation::STATUS_PENDING => [
                'heading' => 'Your donation is still processing',
                'body' => 'We are currently processing your payment. No action is needed on your part — we will email you again as soon as it is confirmed.',
            ],
            Donation::STATUS_FAILED => [
                'heading' => 'Your donation could not be completed',
                'body' => 'Unfortunately your donation failed and was not received. No funds were taken. Please try again, and contact us if the problem continues.',
            ],
        ];

        $copy = $messages[$this->donation->status] ?? $messages[Donation::STATUS_PENDING];

        return new Content(
            markdown: 'emails.donation-status',
            with: [
                'donation' => $this->donation,
                'heading' => $copy['heading'],
                'body' => $copy['body'],
            ],
        );
    }
}
