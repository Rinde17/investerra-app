<x-mail::message>
# Votre abonnement sera annulé à la fin de la période 😔

Bonjour {{ $user->name }},

Nous confirmons que votre demande d'annulation d'abonnement a bien été prise en compte.

Votre abonnement restera actif jusqu'au **{{ $subscription->ends_at?->format('d/m/Y') }}**. Vous pourrez profiter de toutes les fonctionnalités jusqu'à cette date.

Si vous changez d'avis avant cette date, vous pouvez reprendre votre abonnement à tout moment :

<x-mail::button :url="route('settings.subscription')">
Reprendre mon abonnement
</x-mail::button>

Nous espérons vous revoir bientôt !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
