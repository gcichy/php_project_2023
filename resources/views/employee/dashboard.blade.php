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

    <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-full">
                @foreach($employees as $emp)
                    <div class="space-x-8 flex bg-gray-50 border-gray-300  justify-between">
                        <a class ='block w-1/2 pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
                            {{$emp->firstName}}
                        </a>
                        <div class="py-7 pr-7 flex justify-center align-middle">
                            <x-nav-button :href="route('employee.details.work', $emp->employeeNo)">
                                {{ __('Szczegóły pracownika') }}
                            </x-nav-button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



</x-app-layout>
