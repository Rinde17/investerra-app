<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Les données du formulaire de contact.
     *
     * @var array
     */
    public array $contactData;

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Obtenez l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->contactData['email'], $this->contactData['name']), // L'expéditeur est l'utilisateur
            subject: 'Nouveau message de contact : ' . $this->contactData['subject'], // Le sujet de l'email
        );
    }

    /**
     * Obtenez la définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.message', // Vue Blade Markdown pour le contenu de l'email
            with: [
                'contactData' => $this->contactData,
            ],
        );
    }

    /**
     * Obtenez les pièces jointes du message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return []; // Pas de pièces jointes pour l'instant
    }
}
