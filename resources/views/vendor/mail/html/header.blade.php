@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://i2.wp.com/www.dipmar.pl/wp-content/uploads/2017/06/logo6.png?fit=900%2C390&ssl=1" class="logo" alt="Laravel Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
