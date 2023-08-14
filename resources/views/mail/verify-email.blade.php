@component('mail::message')

Witaj {{$name}}. Proszę Zweryfikuj swój adres email.

<x-nav-button :href="route('profile.index', ['employeeNo' => $user->employeeNo])">
    {{ __('Zweryfikuj') }}
</x-nav-button>

@endcomponent
