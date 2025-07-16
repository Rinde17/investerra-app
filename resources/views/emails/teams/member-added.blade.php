<x-mail::message>
# 👋 You've been added to the team {!! $team->name !!}

Hi there,

{!! $inviter->name !!} has added you to the team <strong>{!! $team->name !!}</strong>.

<x-mail::table>
### 👥 Current team members:
@foreach ($members as $member)
- {!! $member->name !!} ({{ $member->pivot->role ?? 'member' }})
@endforeach
</x-mail::table>

<x-mail::button :url="route('teams.show', $team)">
View the Team
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
