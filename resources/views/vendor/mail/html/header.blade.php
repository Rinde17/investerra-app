@props(['url'])
<tr>
<td class="header text-center w-2/3 !bg-red-500">
<img src="{{ $url . '/assets/logos/full-logo-light-no-bg.png' }}" class="logo" alt="{{ config('app.name') }}">
@if (trim($slot))
{!! $slot !!}
@endif
</td>
</tr>
