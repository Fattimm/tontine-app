<?php

namespace App\Mail;

use App\Models\Tour;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TirageEffectue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Tour $tour) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vous bénéficiez du prochain versement — ' . $this->tour->tontine->nom,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tirage_effectue',
        );
    }
}
