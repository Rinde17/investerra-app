<x-mail::message>
# Problème avec votre paiement d'abonnement ! 🚨

Bonjour {{ $user->name }},

Nous avons rencontré un problème lors du traitement de votre dernier paiement pour votre abonnement.

Votre abonnement est actuellement en statut **{{ $subscription->stripe_status }}**. Pour éviter toute interruption de service, veuillez mettre à jour vos informations de paiement dès que possible.

<x-mail::button :url="$user->redirectToBillingPortal()">
Mettre à jour mes informations de paiement
</x-mail::button>

Si vous avez déjà corrigé ce problème, veuillez ignorer cet e-mail.

Merci de votre compréhension,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
