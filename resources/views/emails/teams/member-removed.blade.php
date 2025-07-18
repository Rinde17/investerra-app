<x-mail::message>
# 👋 You've been removed from the team {!! $team->name !!}

Hi there,

{!! $remover->name !!} has removed you from the team <strong>{!! $team->name !!}</strong>.

<br>
<br>

If you think this was a mistake, please contact the team owner.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
