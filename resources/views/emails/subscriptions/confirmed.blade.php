<x-mail::message>
# Votre abonnement est confirmé ! 🎉

Bonjour {{ $user->name }},

Nous sommes ravis de vous confirmer que votre abonnement est maintenant actif.

Vous pouvez gérer votre abonnement et consulter vos factures à tout moment via votre tableau de bord :

<x-mail::button :url="route('settings.subscription')">
Gérer mon abonnement
</x-mail::button>

Merci de faire partie de notre communauté !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
