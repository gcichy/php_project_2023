<x-app-layout>

    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button :href="route('employee.details.work', $user->employeeNo)">
                {{ __('Praca') }}
            </x-nav-button>
        </div>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button>
                {{ __('Profil') }}
            </x-nav-button>
        </div>
    </div>

    @include('layouts.profile-content')



</x-app-layout>
