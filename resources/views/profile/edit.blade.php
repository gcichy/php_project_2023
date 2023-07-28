<x-app-layout>
    <div class="space-x-8 mt-8 flex">
        <a class ='block w-full pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800 bg-gray-50 border-gray-300 focus:outline-none   transition duration-150 ease-in-out'>
            {{ __('Edytuj Profil Użytkownika') }}
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if( $user->role != 'employee')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            @if( $user->role != 'employee')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
