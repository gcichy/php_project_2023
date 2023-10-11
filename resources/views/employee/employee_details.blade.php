<x-app-layout>
    @php
        $left = "Praca";
        $right = "Profil";
    @endphp
    <x-toggle-buttons :leftBtn="$left" :rightBtn="$right">
        <x-slot name="leftContent">
            @php
                $viewName = 'Praca';
            @endphp
            <x-information-panel :viewName="$viewName"></x-information-panel>
            <p>{{$user->firstName}}</p>
        </x-slot>

        <x-slot name="rightContent">
            @include('layouts.profile-content')
        </x-slot>
    </x-toggle-buttons>
</x-app-layout>
