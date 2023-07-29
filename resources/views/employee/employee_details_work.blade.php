<x-app-layout>
    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-around">
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button>
                {{ __('Praca') }}
            </x-nav-button>
        </div>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button :href="route('employee.details.profile', $user->employeeNo)">
                {{ __('Profil') }}
            </x-nav-button>
        </div>
    </div>


    <p>{{$user->firstName}}</p>



</x-app-layout>
