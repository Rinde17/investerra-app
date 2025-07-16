<x-mail::message>
# Votre essai gratuit se termine bientôt ! ⏳

Bonjour {{ $user->name }},

Juste un petit rappel : votre essai gratuit se terminera le **{{ $subscription->trial_ends_at?->format('d/m/Y') }}**.

Pour continuer à profiter de nos services sans interruption, votre abonnement commencera automatiquement à cette date.

Si vous ne souhaitez pas continuer, vous pouvez annuler votre essai à tout moment via vos paramètres d'abonnement :

<x-mail::button :url="route('settings.subscription')">
Gérer mon essai
</x-mail::button>

Nous espérons que vous avez apprécié votre essai !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
