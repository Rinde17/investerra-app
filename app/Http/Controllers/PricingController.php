<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PricingController extends Controller
{

    public function index(): Response
    {
        $plans = Plan::all();

        // Initialise les variables à null par défaut
        $userSubscription = null;
        $paymentIntent = null;

        // Vérifie si l'utilisateur est connecté
        if (Auth::check()) { // Utilise Auth::check() pour vérifier la connexion avant d'appeler $this->user()
            $user = $this->user(); // Utilise ta méthode personnalisée

            // Récupère l'abonnement par défaut de l'utilisateur s'il en a un
            $userSubscription = $user->subscription('default');

            if ($userSubscription) {
                $userSubscriptionPlanId = null;
                // Si tu as la colonne 'plan_id' dans ta table 'subscriptions' ou 'subscription_items'
                // ou si tu peux récupérer le Price ID de Stripe à partir de l'abonnement.
                // Cashier stocke généralement le stripe_price dans subscription_items
                $userSubscriptionPriceId = $userSubscription->stripe_price; // Ceci est l'ID du prix Stripe du plan souscrit
                // Ou si tu as lié un plan_id à ton abonnement dans ta DB:
                // $userSubscriptionPlanId = $userSubscription->plan_id; // Si tu as une colonne plan_id sur ta table subscriptions
            }
        }

        return Inertia::render('Pricing/Index', [
            'plans' => $plans,
            // Passe l'abonnement de l'utilisateur ou null s'il n'est pas connecté ou n'a pas d'abonnement
            'subscription' => $userSubscription ? [
                'stripe_status' => $userSubscription->stripe_status,
                'ends_at' => $userSubscription->ends_at?->toISOString(), // Convertir en ISO string pour JS
                'stripe_price_id' => $userSubscriptionPriceId,
            ] : null,
        ]);
    }

}
