<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Usuń konto') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Kiedy konto zostanie usunięte, wszystkie powiązane z nim dane zostaną usunięte. Zanim usuniesz, upewnij się, że wszystkie niezbędne dane zostały bezpiecznie zapisane na lokalnym dysku.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Usuń konto') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">Czy jesteś pewny, że chcesz usunąć tego użytkownika?</h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Kiedy konto zostanie usunięte, wszystkie powiązane z nim dane zostaną usunięte. Zanim usuniesz, upewnij się, że wszystkie niezbędne dane zostały bezpiecznie zapisane na lokalnym dysku.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Twoje hasło" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Anuluj') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Usuń konto') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
