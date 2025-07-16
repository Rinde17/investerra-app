<x-mail::message>
# Votre abonnement est terminé 🙁

Bonjour {{ $user->name }},

Nous vous informons que votre abonnement est maintenant terminé.

Votre accès aux fonctionnalités de votre plan a été interrompu. Nous sommes désolés de vous voir partir !

Si vous souhaitez reprendre l'accès à nos services, vous pouvez vous réabonner à tout moment :

<x-mail::button :url="route('pricing.index')">
Découvrir nos plans
</x-mail::button>

N'hésitez pas à nous contacter si vous avez des questions.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
