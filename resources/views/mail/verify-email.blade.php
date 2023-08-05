@component('mail::message')

Witaj {{$name}}. Proszę Zweryfikuj swój adres email.

    @component('mail::button', ['url' => 'https://dipmar-produkcja.pl/dashboard'])
        Zweryfikuj
    @endcomponent

@endcomponent
