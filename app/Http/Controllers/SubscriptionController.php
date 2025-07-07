<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Exception\InvalidArgumentException;

class SubscriptionController extends Controller
{

    /**
     * @throws Exception
     */
    public function subscriptionCheckout(Request $request, string $stripePriceId)
    {
        $user = $request->user();

        // Si l'utilisateur n'a pas encore d'ID Stripe, crée-le
        if (is_null($user->stripe_id) || $user->stripe_id === '') {
            try {
                $user->createAsStripeCustomer();
            } catch (InvalidArgumentException $e) {
                // Gérer l'erreur si la création du client Stripe échoue
                // Par exemple, rediriger avec un message d'erreur
                return redirect()->back()->with('error', 'Impossible de créer le client Stripe : ' . $e->getMessage());
            }
        }
        // Retourne directement la redirection vers Stripe
        return $request->user()
            ->newSubscription('default', $stripePriceId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('checkout-success'),
                'cancel_url' => route('checkout-cancel'),
            ]);
    }

    public function billingPortal(): RedirectResponse
    {
        return $this->user()->redirectToBillingPortal();
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(): RedirectResponse
    {
        $user = $this->user(); // Supposons que l'utilisateur est authentifié

        // Récupère l'abonnement 'default' de l'utilisateur.
        $subscription = $user->subscription('default');

        // Condition pour la reprise : l'abonnement existe et est en période de grâce.
        // La méthode onGracePeriod() implique qu'il a été annulé mais n'est pas encore terminé.
        if ($subscription && $subscription->onGracePeriod()) {
            try {
                $subscription->resume(); // Appel de la méthode resume() de Cashier

                // Redirige avec un message de succès après la reprise
                return Redirect::back()->with('success', 'Votre abonnement a été repris avec succès ! Il est de nouveau actif et récurrent.');
            } catch (\Exception $e) {
                // Capture toute exception lors du processus de reprise (par exemple, erreur de l'API Stripe)
                // Log l'erreur pour le débogage.
                Log::error("Erreur lors de la reprise de l'abonnement pour l'utilisateur {$user->id}: " . $e->getMessage());
                return Redirect::back()->with('error', 'Une erreur est survenue lors de la tentative de reprise de votre abonnement. Veuillez réessayer ou contacter le support.');
            }
        }

        // Si aucun abonnement n'est trouvé, ou s'il n'est pas en période de grâce
        // (par exemple, déjà terminé, jamais annulé, ou statut différent), redirige avec une erreur.
        return Redirect::route('pricing.index')->with('error', 'Impossible de reprendre l\'abonnement. Soit il n\'existe pas, soit il n\'est pas éligible à une reprise.');
    }

    public function showSubscriptionSettings(): Response
    {
        $user = $this->user();

        // Récupère l'abonnement l'utilisateur
        $subscription = $user->subscription();

        $plan = Plan::where('stripe_price_id', $subscription->stripe_price)->first();

        $invoices = [];
        if ($user->hasStripeId()) {
            $invoices = $user->invoicesIncludingPending()->map(function ($invoice) {
                // Pour Cashier 12, on ne peut pas générer d'URL signée ici directement.
                // On se contente de l'ID et du statut pour déterminer si on peut télécharger.
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->toFormattedDateString(),
                    'total' => $invoice->total(),
                    'status' => $invoice->status,
                ];
            })->toArray();
        }

        return Inertia::render('settings/SubscriptionSettings', [
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'stripe_status' => $subscription->stripe_status,
                'on_grace_period' => $subscription->onGracePeriod(),
                'ends_at' => $subscription->ends_at?->format('Y-m-d'),
                'plan' => $plan,
            ] : null,
            'invoices' => $invoices,
            'plans' => Plan::all(),
        ]);
    }

    /**
     * Cancel the user's subscription at the end of the current period.
     */
    public function cancelSubscription(): RedirectResponse
    {
        $user = $this->user();
        try {
            $user->subscription('default')->cancel();
            return Redirect::back()->with('success', 'Votre abonnement sera annulé à la fin de la période actuelle. Vous pouvez le reprendre à tout moment avant cette date.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erreur lors de l\'annulation de votre abonnement : ' . $e->getMessage());
        }
    }

    /**
     * Swap the user's subscription to a new plan.
     */
    public function swapSubscription(Request $request): RedirectResponse
    {
        $request->validate([
            'new_plan_id' => ['required', 'exists:plans,id'],
        ]);

        $user = $this->user();
        $newPlan = Plan::find($request->new_plan_id);

        if (!$newPlan) {
            return Redirect::back()->with('error', 'Le plan sélectionné n\'existe pas.');
        }

        try {
            $user->subscription('default')->swap($newPlan->stripe_price_id);
            return Redirect::back()->with('success', 'Votre abonnement a été mis à jour avec succès vers le plan ' . $newPlan->name . ' !');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erreur lors du changement de plan : ' . $e->getMessage());
        }
    }

    public function downloadUserInvoice(Request $request, string $invoiceId)
    {
        // On récupère la facture pour obtenir sa date et construire le nom de fichier
        $invoice = $request->user()->findInvoice($invoiceId);

        if (!$invoice) {
            abort(404, 'Facture non trouvée.');
        }

        // Construire le nom de fichier dynamique
        $filename = 'invoice-' . $invoice->date()->format('Y-m') . '-' . $invoiceId; // ex: invoice-2025-07-inv_XYZ.pdf

        $vendorDetails = [
            'vendor' => config('app.name'),
            'product' => 'Abonnement',
            // 'street' => 'Ta rue', // Exemple
            // 'location' => 'Ta ville, Ton pays', // Exemple
            // 'email' => 'support@tonapp.com', // Exemple
        ];

        return $request->user()->downloadInvoice($invoiceId, $vendorDetails, $filename);
    }

    public function success(): RedirectResponse
    {
        Log::info("Checkout success");
        return Redirect::route('settings.subscription')->with('info', 'Votre abonnement à bien été pris en compte !');
    }

    public function cancel(): RedirectResponse
    {
        Log::warning("Checkout canceled");
        return Redirect::route('pricing.index')->with('info', "La demande d'abonnement à été annulée !");
    }
}
