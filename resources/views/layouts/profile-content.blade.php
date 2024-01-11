
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

<dl class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 rounded-l-lg w-full mt-6">
    @foreach($userData as $span => $data)
        <div class="w-full flex justify-center">
            <x-list-element class="col-span-1 bg-blue-450 flex-col lg:py-0 py-3 w-[40%] lg:w-[60%]">
                <div class="w-full flex flex-row justify-start">
                    <div class="w-full flex flex-col justify-between items-center">
                        <div class="w-full rounded-lg flex justify-center items-center flex-col">
                            <p class="my-2 mr-2 text-center rounded-lg inline-block text-white list-element-name py-2 px-3 xl:text-lg whitespace-nowrap overflow-clip">
                                {{$data }}
                                <span class="block text-sm font-medium lg:text-xl text-white"> {{ $span }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </x-list-element>
        </div>
    @endforeach
</dl>


