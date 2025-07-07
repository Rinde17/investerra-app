<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends CashierWebhookController
{
    public function handleWebhook(Request $request): Response
    {
        Log::info('Webhook reçu');
        return parent::handleWebhook($request);
    }

    public function handleCustomerSubscriptionCreated($payload): Response
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

            // ⬇️ Ensuite on appelle la logique standard de Cashier
            return parent::handleCustomerSubscriptionCreated($payload);
        }

        return $this->successMethod();
    }

    public function handleCustomerSubscriptionUpdated(array $payload): Response
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
}
