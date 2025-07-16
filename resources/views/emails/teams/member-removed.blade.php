<x-mail::message>
# 👋 You've been removed from the team {!! $team->name !!}

Hi there,

{!! $remover->name !!} has removed you from the team <strong>{!! $team->name !!}</strong>.

<x-mail::panel>
### Remaining team members:
@foreach ($members as $member)
- {!! $member->name !!} ({{ $member->pivot->role ?? 'member' }})
@endforeach
</x-mail::panel>

If you think this was a mistake, please contact the team owner.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
