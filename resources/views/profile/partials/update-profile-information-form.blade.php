@php use Illuminate\Contracts\Auth\MustVerifyEmail; @endphp
<section>
    <header>
        <h2 class="lg:text-xl font-medium text-gray-900">
            {{ __('Dane osobowe') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Zaktualizuj dane osobowe użytkownika.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>


    <form method="post" action="{{ route('profile.update', $user->employeeNo) }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        @foreach($userData as $span => $data)
            <div>
                <x-input-label for="{{$data}}" :value="$span" class="lg:text-xl"/>
                <x-text-input id="{{$data}}" name="{{$data}}" type="text" class="mt-1 block w-full"
                              :value="old($data, $user->$data)" required autocomplete="{{$data}}"/>
                <x-input-error class="mt-2" :messages="$errors->get($data)"/>
            </div>
        @endforeach


        <div>
            <x-input-label for="email" :value="__('Email')" class="lg:text-xl"/>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="email"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            @if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Zapisz Zmiany') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 15000)"
                    class="text-sm text-gray-600"
                >{{ __('Zapisano.') }}</p>
            @endif
        </div>
    </form>
</section>
