<x-app-layout>

    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ __('Zarejestruj nowego pracownika') }}
        </a>
    </div>
    <x-auth-card>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- firstName -->
            <div class="lg:mt-2">
                <x-input-label for="firstName" :value="__('Imię')" />
                <x-text-input id="firstName" class="block mt-1 w-full" type="text" name="firstName" :value="old('firstName')" required autofocus />
                <x-input-error :messages="$errors->get('firstName')" class="mt-2" />
            </div>

            <!-- lastName -->
            <div class="lg:mt-2">
                <x-input-label for="lastName" :value="__('Nazwisko')" />
                <x-text-input id="lastName" class="block mt-1 w-full" type="text" name="lastName" :value="old('lastName')" required autofocus />
                <x-input-error :messages="$errors->get('lastName')" class="mt-2" />
            </div>

            <!-- role -->
            <div class="lg:mt-2">
                <x-input-label for="role" :value="__('Stanowisko')" />
                <x-text-input id="role" class="block mt-1 w-full" type="text" name="role" :value="old('role')" required autofocus />
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- employeeNo -->
            <div class="lg:mt-2">
                <x-input-label for="employeeNo" :value="__('Nazwa Użytkownika')" />
                <x-text-input id="employeeNo" class="block mt-1 w-full" type="text" name="employeeNo" :value="old('employeeNo')" required autofocus />
                <x-input-error :messages="$errors->get('employeeNo')" class="mt-2" />
            </div>

            <!-- phoneNr -->
            <div class="lg:mt-2">
                <x-input-label for="phoneNr" :value="__('Numer Telefonu')" />
                <x-text-input id="phoneNr" class="block mt-1 w-full" type="text" name="phoneNr" :value="old('phoneNr')" required autofocus />
                <x-input-error :messages="$errors->get('phoneNr')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="lg:mt-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- salary -->
            <div class="lg:mt-2">
                <x-input-label for="salary" :value="__('Wynagrodzenie')" />
                <x-text-input id="salary" class="block mt-1 w-full" type="text" name="salary" :value="old('salary')" required autofocus />
                <x-input-error :messages="$errors->get('salary')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="lg:mt-2"">
            <x-input-label for="password" :value="__('Hasło')" />

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="lg:mt-2">
                <x-input-label for="password_confirmation" :value="__('Powtórz Hasło')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation" required />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-center mt-4">
                {{--                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">--}}
                {{--                    {{ __('Already registered?') }}--}}
                {{--                </a>--}}

                <x-primary-button>
                    {{ __('Zarejestruj Użytkownika') }}
                </x-primary-button>
            </div>
        </form>
    </x-auth-card>
</x-app-layout>
