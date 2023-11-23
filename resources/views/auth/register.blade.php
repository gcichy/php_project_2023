<x-app-layout>

    @php
        $viewName = 'Zarejestruj użytkownika';
    @endphp
    <x-information-panel :viewName="$viewName">
    </x-information-panel>

        <div class="py-12">
            <div class="max-w-7xl lg:w-[80%] xl:w-[60%] mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-center">
                    <div class="w-[80%] max-w-xl">
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
                            <div class="lg:mt-2">
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
                    </div>
                </div>
            </div>
        </div>

</x-app-layout>
