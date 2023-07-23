<x-app-layout>
    @if(isset($status))
        <p>{{$status}}</p>
    @endif
    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ __('Pracownicy') }}
        </a>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button :href="route('register')">
                {{ __('Dodaj Pracownika') }}
            </x-nav-button>
        </div>
    </div>

    @foreach($employees as $emp)
        <p>{{$emp->firstName}}</p>
    @endforeach

</x-app-layout>
