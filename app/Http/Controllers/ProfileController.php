<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HasEnsure;
    /**
     * Display the user's profile.
     */
    public function index(Request $request): View
    {
        $userData = getUserData::getUserData($request->user());

        return view('profile.profile', [
            'user' => $request->user(),
            'userData' => $userData,
        ]);

    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {


        $userData = getUserData::getEditUserData();

        return view('profile.edit', [
            'user' => $request->user(),
            'userData' => $userData,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $user = $this->ensureIsNotNullUser($request->user());

//        $data = $this->ensureIsArray($request->validated());


        $data = $this->validateUpdate($request, $user);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->emailVerifiedAt = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $this->ensureIsNotNullUser($request->user());

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Returns dict with user data.
     *
     */
    private function getUserData(Request $request): array
    {
        $userData = [];
        try {
            $user = $request->user();
            if(is_null($user)) {
                throw new Exception("User is not defined");
            }
            $name = (!is_null($user->firstName) ? $user->firstName : '').' '.
                (!is_null($user->lastName) ? $user->lastName : '');
            $userData = [
                'Imię i Nazwisko' => $name,
                'Stanowisko' => !is_null($user->role) ? $user->role : '-',
                'Nazwa Użytkownika' => !is_null($user->employeeNo) ? $user->employeeNo : '-',
                'E-mail' => !is_null($user->email) ? $user->email : '-',
                'Nr Telefonu' => !is_null($user->phoneNr) ? $user->phoneNr : '-',
                'Wynagrodzenie' => !is_null($user->salary) ? $user->salary : '-',
            ];


        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

        return $userData;
    }

    private function getEditUserData(): array
    {
        return [
            'Imię' => 'firstName',
            'Nazwisko' => 'lastName',
            'Stanowisko' => 'role',
            'Nazwa Użytkownika' => 'employeeNo',
            'Wynagrodzenie' => 'salary',
            'Nr Telefonu' => 'phoneNr',
        ];
    }

    private function validateUpdate(Request $request, User $user) {
        $request->validate([
            'firstName' => ['required', 'string',  'max:30','regex:/^[a-zA-ZźżćśńółąęŻŹĆŚŃÓŁĄĘ ]+$/'],
            'lastName' => ['required', 'string',  'max:30', 'regex:/^[a-zA-ZźżćśńółąęŻŹĆŚŃÓŁĄĘ ]+$/'],
            'role' => ['required', 'string', 'in:admin,manager,pracownik'],
            'employeeNo' => ['required', 'string',  'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phoneNr' => ['required', 'string',  'max:30', 'digits:9', Rule::unique(User::class)->ignore($user->id)],
            'email' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],#Rule::unique('users', 'email')
            'salary' => ['required', 'numeric', 'min:0']
        ],
            [
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'firstName.regex' => 'Pole Imię może zawierać tylko litery.',
                'lastName.regex' => 'Pole Nazwisko może zawierać tylko litery.',
                'role.in' => 'Niepoprawne stanowisko. Musi być jedno z: pracownik, manager, admin.',
                'employeeNo.unique' => 'Ta nazwa użytkownika jest zajęta.',
                'phoneNr.digits' => 'Numer telefonu musi zawierać dokładnie 9 cyfr.',
                'phoneNr.unique' => 'Ten numer telefonu jest już w systemie.',
                'email.unique' => 'Ten email jest już w systemie.',
                'email.email' => 'Niepoprawny email. Upewnij się, że wpisujesz poprawny adres.',
                'salary.numeric' => 'Wynagrodzenie musi być liczbą',
                'salary.min' => 'Wynagrodzenie musi być liczbą nieujemną.',
            ]);

        $data = [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'role' => $request->role,
            'employeeNo' => $request->employeeNo,
            'phoneNr' => $request->phoneNr,
            'email' => $request->email,
            'salary' => $request->salary,
        ];

        return $data;
    }
}

