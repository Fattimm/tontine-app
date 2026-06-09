<?php

namespace App\Mail;

use App\Models\Cotisation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotisationEnregistree extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Cotisation $cotisation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de cotisation — ' . $this->cotisation->tontine->nom,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cotisation_enregistree',
        );
    }
}
