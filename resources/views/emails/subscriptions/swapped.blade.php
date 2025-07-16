<x-mail::message>
# Votre abonnement a été mis à jour ! 🎉

Bonjour {{ $user->name }},

Votre abonnement a bien été mis à jour vers le plan **{{ $newPlan->name }}**.

Votre prochain paiement sera ajusté en conséquence.

<x-mail::button :url="route('settings.subscription')">
Gérer mon abonnement
</x-mail::button>

Merci pour votre confiance !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
