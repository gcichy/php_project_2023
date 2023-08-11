@component('mail::message')

Witaj {{$name}}. Proszę Zweryfikuj swój adres email.

<x-nav-button :href="'https://dipmar-produkcja.pl/dashboard'">
    {{ __('Zweryfikuj') }}
</x-nav-button>

@endcomponent
