<x-mail::message>
# Nouveau Message de Contact LandAnalysis

Vous avez reçu un nouveau message via le formulaire de contact de votre site web LandAnalysis.

**Nom :** {{ $contactData['name'] }}
**Email :** {{ $contactData['email'] }}
**Sujet :** {{ $contactData['subject'] ?? 'Non spécifié' }}

**Message :**
{{ $contactData['message'] }}

<x-mail::button :url="url('/')">
Aller sur le Site
</x-mail::button>

Merci,<br>
L'équipe de {{ config('app.name') }}
</x-mail::message>
