<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use HasEnsure;

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {


        $request->validate([
            'firstName' => ['required', 'string',  'max:30','regex:/^[a-zA-ZźżćśńółąęŻŹĆŚŃÓŁĄĘ ]+$/'],
            'lastName' => ['required', 'string',  'max:30', 'regex:/^[a-zA-Z ]+$/'],
            'role' => ['required', 'string', 'in:admin,manager,pracownik'],
            'employeeNo' => ['required', 'string',  'max:255', 'unique:'.User::class],
            'phoneNr' => ['required', 'string',  'max:30', 'digits:9', 'unique:'.User::class],
            'email' => ['required', 'string', 'max:255', 'unique:'.User::class, 'email'],#Rule::unique('users', 'email')
            'password' => ['required', 'min:11', 'string', 'max:30',
                'regex:/(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%+=-_<>;:?.,\^&\*\)\(])/'],//Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'salary' => ['required', 'numeric', 'min:0']
            ],
            [
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'firstName.regex' => 'Pole Imię może zawierać liczb.',
                'lastName.regex' => 'Pole Nazwisko nie może zawierać liczb.',
                'role.in' => 'Niepoprawne stanowisko. Musi być jedno z: pracownik, manager, admin.',
                'employeeNo.unique' => 'Ta nazwa użytkownika jest zajęta.',
                'phoneNr.digits' => 'Numer telefonu musi zawierać dokładnie 9 cyfr.',
                'phoneNr.unique' => 'Ten numer telefonu jest już w systemie.',
                'email.unique' => 'Ten email jest już w systemie.',
                'email.email' => 'Niepoprawny email. Upewnij się, że wpisujesz poprawny adres.',
                'password.min' => 'Hasło musi zawierać minimum 11 znaków.',
                'password.regex' => 'Hasło musi zawierać małą literę, dużą literę, liczbę i znak specjalny.',
                'password_confirmation.same' => 'Hasła muszą być identyczne.',
                'salary.numeric' => 'Wynagrodzenie musi być liczbą',
                'salary.min' => 'Wynagrodzenie musi być liczbą nieujemną.',
            ]);

        $password = $this->ensureIsString($request->password);

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'role' =>  $request->role,
            'employeeNo' => $request->employeeNo,
            'phoneNr' => $request->phoneNr,
            'email' => $request->email,
            'isVerified' => false,
            'password' => Hash::make($password),
            'salary' => $request->salary,
            'created_at' => date('d.m.Y H:i:s'),
            'updated_at' => date('d.m.Y H:i:s'),
        ]);

        event(new Registered($user));

        //Auth::login($user);

        return redirect()->action(
            [EmployeeController::class, 'index'],
            ['status' => 'Użytkownik '.$request->firstName.' '.$request->lastName.' został zarejestrowany.']
        );
    }
}
