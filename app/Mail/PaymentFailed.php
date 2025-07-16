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

class PaymentFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Subscription $subscription;
    public array $invoicePayload; // Pour passer les détails de la facture

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription, array $invoicePayload)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->invoicePayload = $invoicePayload;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action requise : Problème avec votre paiement d\'abonnement',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriptions.payment_failed',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'invoice' => $this->invoicePayload,
            ],
        );
    }
}
