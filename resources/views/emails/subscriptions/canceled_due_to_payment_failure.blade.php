<x-mail::message>
# Votre abonnement a été annulé suite à un échec de paiement 😔

Bonjour {{ $user->name }},

Nous vous informons que votre abonnement a été annulé car nous n'avons pas pu traiter votre paiement.

Votre accès aux fonctionnalités de votre plan a été interrompu. Nous sommes désolés de vous voir partir !

Si vous souhaitez réactiver votre abonnement, vous pouvez le faire à tout moment en visitant notre page d'abonnement :

<x-mail::button :url="route('pricing.index')">
Réactiver mon abonnement
</x-mail::button>

N'hésitez pas à nous contacter si vous avez des questions.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
