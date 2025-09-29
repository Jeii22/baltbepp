<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $ticketPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $ticketPath = null)
    {
        $this->booking = $booking;
        $this->ticketPath = $ticketPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed - Your Ferry Ticket is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->ticketPath && file_exists($this->ticketPath)) {
            $attachments[] = Attachment::fromPath($this->ticketPath)
                ->as('ferry-ticket.pdf')
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
