@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    @if(isset($user))
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @if(is_null($user->email_verified_at))
            <div class="my-2 flex justify-center">
                <x-nav-button :href="route('verification.notice', $user->employeeNo)" class="my-4 bg-red-700">
                    @if(isset($currentUser) and $currentUser->employeeNo != $user->employeeNo)
                        {{ __('Zweryfikuj adres e-mail użytkownika '.$user->employeeNo) }}
                    @else
                        {{ __('Zweryfikuj swój adres e-mail') }}
                    @endif

                </x-nav-button>
            </div>
        @endif
        @php
            $viewName = 'Edytuj Profil Użytkownika';
        @endphp
        <x-information-panel :viewName="$viewName">
        </x-information-panel>
        @if (session('status') === 'password-updated')
            <div class="my-2 flex justify-center">
                <p class="text-green-500">{{ __('Zmieniono hasło.') }}</p>
            </div>
        @endif
        <div class="py-12">
            <div class="max-w-7xl lg:w-[80%] xl:w-[60%] mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-center">
                    <div class="w-[90%] max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
                @if( Auth::user()->employeeNo == $user->employeeNo)
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-center">
                        <div class="w-[90%] max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                @endif
                @if( Auth::user()->role != 'pracownik')
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-center">
                        <div class="w-[90%] max-w-xl">
                            @include('profile.partials.delete-user-form',['user' => $user])
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
