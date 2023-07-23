<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- firstName -->
            <div>
                <x-input-label for="firstName" :value="__('Imię')" />
                <x-text-input id="firstName" class="block mt-1 w-full" type="text" name="firstName" :value="old('firstName')" required autofocus />
                <x-input-error :messages="$errors->get('firstName')" class="mt-2" />
            </div>

            <!-- lastName -->
            <div>
                <x-input-label for="lastName" :value="__('Nazwisko')" />
                <x-text-input id="lastName" class="block mt-1 w-full" type="text" name="lastName" :value="old('lastName')" required autofocus />
                <x-input-error :messages="$errors->get('lastName')" class="mt-2" />
            </div>

            <!-- role -->
            <div>
                <x-input-label for="role" :value="__('Stanowisko')" />
                <x-text-input id="role" class="block mt-1 w-full" type="text" name="role" :value="old('role')" required autofocus />
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- employeeNo -->
            <div>
                <x-input-label for="employeeNo" :value="__('Nazwa Użytkownika')" />
                <x-text-input id="employeeNo" class="block mt-1 w-full" type="text" name="employeeNo" :value="old('employeeNo')" required autofocus />
                <x-input-error :messages="$errors->get('employeeNo')" class="mt-2" />
            </div>

            <!-- phoneNr -->
            <div>
                <x-input-label for="phoneNr" :value="__('Numer Telefonu')" />
                <x-text-input id="phoneNr" class="block mt-1 w-full" type="text" name="phoneNr" :value="old('phoneNr')" required autofocus />
                <x-input-error :messages="$errors->get('phoneNr')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- salary -->
            <div>
                <x-input-label for="salary" :value="__('Wynagrodzenie')" />
                <x-text-input id="salary" class="block mt-1 w-full" type="text" name="salary" :value="old('salary')" required autofocus />
                <x-input-error :messages="$errors->get('salary')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Hasło')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Powtórz Hasło')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
{{--                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">--}}
{{--                    {{ __('Already registered?') }}--}}
{{--                </a>--}}

                <x-primary-button class="ml-4">
                    {{ __('Zarejestruj Użytkownika') }}
                </x-primary-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
