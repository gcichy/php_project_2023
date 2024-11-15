<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    use HasEnsure;

    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'max:255', 'email'],#Rule::unique('users', 'email')
            'password' => ['required', 'min:11', 'string', 'max:30',
                'regex:/(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%+=-_<>;:?.,\^&\*\)\(])/'],//Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ],
            [
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'email.email' => 'Niepoprawny email. Upewnij się, że wpisujesz poprawny adres.',
                'password.min' => 'Hasło musi zawierać minimum 11 znaków.',
                'password.regex' => 'Hasło musi zawierać małą literę, dużą literę, liczbę i znak specjalny.',
                'password_confirmation.same' => 'Hasła muszą być identyczne.',
            ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $password = $this->ensureIsString($request->password);

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $status = $this->ensureIsStringOrNull($status);

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
