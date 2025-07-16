<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Plan; // Assurez-vous d'importer votre modèle Plan
use Laravel\Cashier\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionSwapped extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Subscription $subscription;
    public Plan $newPlan;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription, Plan $newPlan)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->newPlan = $newPlan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre abonnement a été mis à jour',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriptions.swapped',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'newPlan' => $this->newPlan,
            ],
        );
    }
}
