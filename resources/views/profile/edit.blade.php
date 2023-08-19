@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="space-x-8 mt-8 flex">
        <a class='block w-full pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800 bg-gray-50 border-gray-300 focus:outline-none   transition duration-150 ease-in-out'>
            {{ __('Edytuj Profil Użytkownika') }}
        </a>
    </div>
    @if (session('status') === 'password-updated')
        <div class="my-2 flex justify-center">
            <p class="text-green-500">{{ __('Zmieniono hasło.') }}</p>
        </div>
    @endif
    @if(is_null($user->email_verified_at))
        <div class="my-2 flex justify-center">
            <x-nav-button :href="route('verification.notice', $user->employeeNo)" class="bg-red-700">
                @if(isset($currentUser) and $currentUser->employeeNo != $user->employeeNo)
                    {{ __('Zweryfikuj adres e-mail użytkownika '.$user->employeeNo) }}
                @else
                    {{ __('Zweryfikuj swój adres e-mail') }}
                @endif

            </x-nav-button>
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            @if( Auth::user()->employeeNo == $user->employeeNo)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            @endif
            @if( Auth::user()->role != 'pracownik')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
