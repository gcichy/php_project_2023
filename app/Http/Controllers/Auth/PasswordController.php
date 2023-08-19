<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    use HasEnsure;

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:11', 'string', 'max:30',
                'regex:/(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%+=-_<>;:?.,\^&\*\)\(])/','different:current_password'],//Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'required' => 'To pole jest wymagane.',
            'max' => 'Wpisany tekst ma za dużo znaków.',
            'current_password.current_password' => 'Aktualne hasło jest nieprawidłowe.',
            'password.different' => 'Nowe hasło nie może być identyczne jak poprzednie.',
            'password.min' => 'Hasło musi zawierać minimum 11 znaków.',
            'password.regex' => 'Hasło musi zawierać małą literę, dużą literę, liczbę i znak specjalny.',
            'password_confirmation.same' => 'Hasła muszą być identyczne.',
        ]);

        $user = $this->ensureIsNotNullUser($request->user());

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
