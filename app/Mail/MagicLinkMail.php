<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable;

    public $staffName;
    public $magicLinkUrl;
    public $expiresAt;
    public $checkInDate;
    public $requestedAt;
    public $locationName;
    public $geofenceRadius;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $staffName,
        string $magicLinkUrl,
        string $expiresAt,
        string $checkInDate,
        string $requestedAt,
        string $locationName,
        int $geofenceRadius
    ) {
        $this->staffName = $staffName;
        $this->magicLinkUrl = $magicLinkUrl;
        $this->expiresAt = $expiresAt;
        $this->checkInDate = $checkInDate;
        $this->requestedAt = $requestedAt;
        $this->locationName = $locationName;
        $this->geofenceRadius = $geofenceRadius;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Attendance Check-In Link',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'attendance.emails.magic-link',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
