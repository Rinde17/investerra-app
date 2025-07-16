<?php

namespace App\Mail;

use App\Models\User;
use Laravel\Cashier\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Subscription $subscription;
    public array $invoicePayload; // Pour passer les détails de la facture si besoin

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct(User $user, Subscription $subscription, array $invoicePayload)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->invoicePayload = $invoicePayload;
    }

    /**
     * Récupère l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action requise : Problème avec votre paiement d\'abonnement',
        );
    }

    /**
     * Récupère la définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriptions.payment_reminder',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'invoice' => $this->invoicePayload, // Passez le payload de la facture
            ],
        );
    }
}
