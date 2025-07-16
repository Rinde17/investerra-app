<?php
namespace App\Http\Controllers;

use App\Mail\PaymentFailed;
use App\Mail\PaymentReminder;
use App\Mail\SubscriptionCanceledDueToPaymentFailure;
use App\Mail\SubscriptionConfirmed;
use App\Mail\SubscriptionEnded;
use App\Mail\SubscriptionSwapped;
use App\Mail\TrialEndingSoon;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends CashierWebhookController
{
    public function handleWebhook(Request $request): Response
    {
        Log::info('Webhook reçu');

        $payload = $request->all();
        $eventType = $payload['type'] ?? null;

        return match ($eventType) {
            'invoice.payment_failed' => $this->handleCustomInvoicePaymentFailed($payload),
            'customer.subscription.trial_will_end' => $this->handleCustomSubscriptionTrialWillEnd($payload),
            default => parent::handleWebhook($request),
        };

    }

    protected function handleCustomerSubscriptionCreated($payload): Response
    {
        Log::info('Event : customer.subscription.created');

        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $data = $payload['data']['object'];

            // 🔎 On récupère le premier price_id Stripe
            $firstItem = $data['items']['data'][0] ?? null;
            $stripePriceId = $firstItem['price']['id'] ?? null;

            if ($stripePriceId) {
                // 🔁 On récupère notre plan interne lié à ce stripe_price_id
                $plan = Plan::where('stripe_price_id', $stripePriceId)->first();

                if ($plan) {
                    $user->plan_id = $plan->id;
                    $user->save();

                    Log::info("Plan associé à l'utilisateur", [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'stripe_price_id' => $stripePriceId,
                    ]);
                } else {
                    Log::warning("Aucun plan trouvé pour le stripe_price_id : $stripePriceId");
                }
            }

            $subscription = $user->subscription('default');

            if ($subscription) {
                $lastEmailSentAt = $subscription->last_subscription_created_email_sent_at;
                $gracePeriod = Carbon::now()->subMinutes(2);

                if (!$lastEmailSentAt || $lastEmailSentAt->lessThan($gracePeriod)) {
                    Mail::to($user->email)->send(new SubscriptionConfirmed($user, $subscription));
                    $subscription->update(['last_subscription_created_email_sent_at' => Carbon::now()]);
                    Log::info("Email de confirmation d'abonnement envoyé via webhook (created).");
                } else {
                    Log::info("Email de confirmation d'abonnement déjà envoyé récemment (created), ignoré.");
                }
            }

            // ⬇️ Ensuite on appelle la logique standard de Cashier
            return parent::handleCustomerSubscriptionCreated($payload);
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        Log::info('Event : customer.subscription.updated');

        $data = $payload['data']['object'];

        // --- Correction pour current_period_end ---
        if (($data['cancel_at_period_end'] ?? false) && !isset($data['current_period_end']) && isset($data['cancel_at'])) {
            $payload['data']['object']['current_period_end'] = $data['cancel_at'];
            Log::info("Patching payload: 'current_period_end' set from 'cancel_at' for Cashier parent method.");
        }

        $user = $this->getUserByStripeId($data['customer'] ?? null);

        if ($user) {
            $firstItem = $data['items']['data'][0] ?? null;
            $stripePriceId = $firstItem['price']['id'] ?? null;

            $oldStripePriceId = $payload['data']['previous_attributes']['items']['data'][0]['price']['id'] ?? null;

            $subscription = $user->subscription('default');

            if ($stripePriceId) {
                $plan = Plan::where('stripe_price_id', $stripePriceId)->first();

                if ($plan) {
                    $user->plan_id = $plan->id;
                    $user->save();

                    Log::info("Plan mis à jour pour l'utilisateur suite à l'update", [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'stripe_price_id' => $stripePriceId,
                    ]);

                    // Vérifie si le prix Stripe a réellement changé (indiquant un swap de plan)
                    if ($subscription && $oldStripePriceId && ($oldStripePriceId !== $stripePriceId)) {
                        $lastEmailSentAt = $subscription->last_plan_swapped_email_sent_at;
                        $gracePeriod = Carbon::now()->subMinutes(2);

                        if (!$lastEmailSentAt || $lastEmailSentAt->lessThan($gracePeriod)) {
                            Mail::to($user->email)->send(new SubscriptionSwapped($user, $subscription, $plan));
                            $subscription->update(['last_plan_swapped_email_sent_at' => Carbon::now()]);
                            Log::info("Email de changement de plan envoyé via webhook (updated).");
                        } else {
                            Log::info("Email de changement de plan déjà envoyé récemment (updated), ignoré.");
                        }
                    }
                } else {
                    Log::warning("Aucun plan trouvé pour le stripe_price_id (update) : $stripePriceId");
                }
            }
        } else {
            Log::warning("Utilisateur non trouvé via Stripe ID dans handleCustomerSubscriptionUpdated.");
        }

        // Appelle la logique standard de Cashier avec le payload (maintenant patché si nécessaire)
        return parent::handleCustomerSubscriptionUpdated($payload);
    }

    protected function handleCustomInvoicePaymentFailed(array $payload): Response
    {
        Log::warning('Event : invoice.payment_failed');
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            // Récupérer l'abonnement par défaut de l'utilisateur
            $subscription = $user->subscription('default');

            if ($subscription) {
                // Logique pour l'e-mail de rappel (statut 'past_due')
                if ($subscription->stripe_status === 'past_due') {
                    $lastReminderSentAt = $subscription->last_payment_reminder_email_sent_at;
                    // Période de grâce pour le rappel (ex: 24 heures)
                    $reminderGracePeriod = Carbon::now()->subHours(24);

                    if (!$lastReminderSentAt || $lastReminderSentAt->lessThan($reminderGracePeriod)) {
                        Mail::to($user->email)->send(new PaymentReminder($user, $subscription, $payload['data']['object']));
                        $subscription->update(['last_payment_reminder_email_sent_at' => Carbon::now()]);
                        Log::info("Email de rappel de paiement envoyé pour l'utilisateur {$user->id} (statut: past_due).");
                    } else {
                        Log::info("Email de rappel de paiement déjà envoyé récemment pour l'utilisateur {$user->id} (statut: past_due), ignoré.");
                    }
                }
                // Logique pour l'e-mail d'annulation suite à échec de paiement (statut 'canceled')
                // ATTENTION : Cet e-mail peut potentiellement se chevaucher avec l'e-mail envoyé par
                // handleCustomSubscriptionDeleted si l'annulation est la conséquence directe de l'échec de paiement.
                // Décidez quelle méthode est l'autorité pour l'e-mail de "fin d'abonnement".
                // Si handleCustomSubscriptionDeleted envoie un e-mail générique de fin, celui-ci est plus spécifique.
                else if ($subscription->stripe_status === 'canceled') {
                    // Pour éviter les doublons avec handleCustomSubscriptionDeleted, vous pourriez :
                    // 1. Ajouter un horodatage spécifique 'last_canceled_due_to_payment_email_sent_at'
                    // 2. Ou décider que handleCustomSubscriptionDeleted est l'unique source de l'e-mail de fin d'abonnement,
                    //    et dans ce cas, vous ne mettriez pas de logique d'envoi ici pour le statut 'canceled'.
                    // Pour l'exemple, nous l'envoyons ici, mais soyez conscient du potentiel de doublon si
                    // handleCustomSubscriptionDeleted envoie aussi un email juste après.

                    // Une approche simple pour éviter les doublons immédiats si le webhook est renvoyé plusieurs fois
                    // pour le même état final de 'canceled' (mais pas de déduplication avec 'deleted' webhook)
                    $invoiceId = $payload['data']['object']['id'] ?? null;
                    if ($invoiceId && !cache()->has('payment_failure_cancellation_email_sent_' . $invoiceId)) {
                        Mail::to($user->email)->send(new SubscriptionCanceledDueToPaymentFailure($user, $subscription, $payload['data']['object']));
                        Log::info("Email d'abonnement annulé suite à échec de paiement envoyé pour l'utilisateur {$user->id} (statut: canceled).");
                        // Marquer l'envoi pour cette facture spécifique pour une courte période
                        cache()->put('payment_failure_cancellation_email_sent_' . $invoiceId, true, now()->addMinutes(10));
                    } else if ($invoiceId) {
                        Log::info("Email d'abonnement annulé suite à échec de paiement déjà envoyé récemment pour l'utilisateur {$user->id} (statut: canceled), ignoré.");
                    }
                } else {
                    Log::info("Email d'échec de paiement non envoyé pour l'utilisateur {$user->id} car l'abonnement n'est pas en statut 'past_due' ou 'canceled' (actuel: {$subscription->stripe_status}).");
                }
            } else {
                Log::warning("Abonnement par défaut non trouvé pour l'utilisateur {$user->id} lors de l'événement invoice.payment_failed.");
            }
        } else {
            Log::warning("Utilisateur non trouvé via Stripe ID pour l'échec de paiement.");
        }
        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionDeleted($payload): Response
    {
        Log::info('Event : customer.subscription.deleted');
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $subscription = $user->subscription('default');
            // Envoyer l'email de fin d'abonnement (généralement une seule fois)
            Mail::to($user->email)->send(new SubscriptionEnded($user, $subscription));
            Log::info("Email de fin d'abonnement envoyé pour l'utilisateur {$user->id}.");
        } else {
            Log::warning("Utilisateur non trouvé via Stripe ID pour la suppression d'abonnement.");
        }

        return parent::handleCustomerSubscriptionDeleted($payload);
    }

    protected function handleCustomSubscriptionTrialWillEnd($payload): Response
    {
        Log::info('Event : customer.subscription.trial_will_end');
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $subscription = $user->subscription('default');
            if ($subscription && $subscription->onTrial()) {
                // Envoyer l'email de rappel de fin d'essai
                Mail::to($user->email)->send(new TrialEndingSoon($user, $subscription));
                Log::info("Email de rappel de fin d'essai envoyé pour l'utilisateur {$user->id}.");
            }
        } else {
            Log::warning("Utilisateur non trouvé via Stripe ID pour la fin d'essai.");
        }
        return $this->successMethod();
    }
}
