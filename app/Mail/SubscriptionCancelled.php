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

class SubscriptionCancelled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Subscription $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation d\'annulation de votre abonnement',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriptions.cancelled',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
            ],
        );
    }
}
