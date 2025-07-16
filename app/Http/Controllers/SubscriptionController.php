<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionConfirmed;
use App\Mail\SubscriptionResumed;
use App\Mail\SubscriptionSwapped;
use App\Models\Plan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $user = $this->user();

        $subscription = $user->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            try {
                $subscription->resume();
                Mail::to($user->email)->send(new SubscriptionResumed($user, $subscription));
                Log::info("Email de confirmation de reprise d'abonnement envoyé par le contrôleur.");
                return Redirect::back()->with('success', 'Votre abonnement a été repris avec succès ! Il est de nouveau actif et récurrent.');
            } catch (\Exception $e) {
                Log::error("Erreur lors de la reprise de l'abonnement pour l'utilisateur {$user->id}: " . $e->getMessage());
                return Redirect::back()->with('error', 'Une erreur est survenue lors de la tentative de reprise de votre abonnement. Veuillez réessayer ou contacter le support.');
            }
        }

        return Redirect::route('pricing.index')->with('error', 'Impossible de reprendre l\'abonnement. Soit il n\'existe pas, soit il n\'est pas éligible à une reprise.');
    }

    public function showSubscriptionSettings(): Response
    {
        $user = $this->user();

        // Récupère l'abonnement l'utilisateur
        $subscription = $user->subscription();

        $plan = $subscription
            ? Plan::where('stripe_price_id', $subscription->stripe_price)->first()
            : null;

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
            $subscription = $user->subscription('default');

            $subscription->cancel();

            Mail::to($user->email)->send(new SubscriptionCancelled($user, $subscription));

            Log::info("Email de confirmation d'annulation envoyé par le contrôleur.");

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
            $subscription = $user->subscription('default')->swap($newPlan->stripe_price_id);

            if ($subscription) {
                Mail::to($user->email)->send(new SubscriptionSwapped($user, $subscription, $newPlan));
                $subscription->update(['last_plan_swapped_email_sent_at' => Carbon::now()]);
                Log::info("Email de changement de plan envoyé par le contrôleur.");
            }

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

        $user = $this->user();

        $subscription = $user->subscription('default');

        if ($subscription) {
            $lastEmailSentAt = $subscription->last_subscription_created_email_sent_at;
            $gracePeriod = Carbon::now()->subMinutes(2);

            if (!$lastEmailSentAt || $lastEmailSentAt->lessThan($gracePeriod)) {
                Mail::to($user->email)->send(new SubscriptionConfirmed($user, $subscription));
                $subscription->update(['last_subscription_created_email_sent_at' => Carbon::now()]);
                Log::info("Email de confirmation d'abonnement envoyé via controller.");
            } else {
                Log::info("Email de confirmation d'abonnement déjà envoyé récemment (created), ignoré.");
            }
        }

        return Redirect::route('settings.subscription')->with('info', 'Votre abonnement à bien été pris en compte !');
    }

    public function cancel(): RedirectResponse
    {
        Log::warning("Checkout canceled");
        return Redirect::route('pricing.index')->with('info', "La demande d'abonnement à été annulée !");
    }
}
