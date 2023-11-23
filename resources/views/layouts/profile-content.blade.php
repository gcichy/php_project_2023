
@if(is_null($user->email_verified_at))
    <div class="my-2 flex justify-center">
        <x-nav-button :href="route('verification.notice', $user->employeeNo)" class=" my-4 bg-red-700">
            @if(isset($currentUser) and $currentUser->employeeNo != $user->employeeNo)
                {{ __('Zweryfikuj adres e-mail użytkownika '.$user->employeeNo) }}
            @else
                {{ __('Zweryfikuj swój adres e-mail') }}
            @endif

        </x-nav-button>
    </div>
@endif
@php
    $viewName = 'Profil Użytkownika';
@endphp
<x-information-panel :viewName="$viewName">
    <x-nav-button :href="route('profile.edit', $user->employeeNo)" class="mr-3 lg:mr-5 bg-orange-500">
        {{ __('Edytuj') }}
    </x-nav-button>
</x-information-panel>

<div class="grid mt-6 justify-evenly bg-[#00B4D8] dark:bg-gray-300 grid-cols-3">
    @foreach($userData as $span => $data)
        <div class="col-span-1  p-7 m-8 hover:border-b">
            <div class="flex flex-col items-center ">
                    <p class="text-md font-medium lg:text-3xl text-white">{{ $data }}</p>
                    <span class="text-sm font-medium lg:text-xl text-white"> {{ $span }}</span>
            </div>
        </div>
    @endforeach
</div>


