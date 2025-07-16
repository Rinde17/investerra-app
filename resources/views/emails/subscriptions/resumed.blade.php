<x-mail::message>
# Votre abonnement est de nouveau actif ! ✅

Bonjour {{ $user->name }},

Nous confirmons que votre abonnement a été repris avec succès et est maintenant actif.

Vous pouvez continuer à profiter de toutes les fonctionnalités de votre plan. Votre prochain paiement aura lieu le **{{ $subscription->nextPaymentAttempt()?->format('d/m/Y') ?? 'à la date habituelle' }}**.

<x-mail::button :url="route('settings.subscription')">
Gérer mon abonnement
</x-mail::button>

Merci de votre fidélité !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
