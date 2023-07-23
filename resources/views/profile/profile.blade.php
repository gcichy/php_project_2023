<x-app-layout>

    <div class="space-x-8 mt-8 flex">
        <a class ='block w-full pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800 bg-gray-50 border-gray-300 focus:outline-none   transition duration-150 ease-in-out'>
            {{ __('Profil Użytkownika') }}
        </a>
    </div>
    <div class="grid  justify-evenly bg-[#00B4D8] dark:bg-gray-300 grid-cols-3">
        @foreach($userData as $span => $data)
            <div class="col-span-1  p-7 m-8 hover:border-b">
                <div class="flex flex-col items-center ">
                        <p class="text-md font-medium lg:text-3xl text-white">{{ $data }}</p>
                        <span class="text-sm font-medium lg:text-xl text-white"> {{ $span }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-10 mx-10 flex justify-center">
        <x-nav-button :href="route('profile.edit')">
            {{ __('Edytuj Profil') }}
        </x-nav-button>
    </div>


</x-app-layout>
