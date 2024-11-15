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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HasEnsure;
    /**
     * Display the user's profile.
     */
    public function index(Request $request, string $employeeNo): View
    {

        $status = $request->verified == 1 ? 'Pomyślnie zweryfikowano adres email' : '';
        $user = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->firstOrFail());
        $userData = getUserData::getUserData($user);

        return view('profile.profile', [
            'user' => $user,
            'userData' => $userData,
            'status' => $status,
        ]);

    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, string $employeeNo): View
    {
        $user = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->firstOrFail());
        $userData = getUserData::getEditUserData();

        $currentUser = $this->ensureIsNotNullUser($request->user());


        return view('profile.edit', [
            'user' => $user,
            'currentUser' => $currentUser,
            'userData' => $userData,

        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, string $employeeNo): RedirectResponse
    {
        $user = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->firstOrFail());

//        $data = $this->ensureIsArray($request->validated());

        $data = $this->validateUpdate($request, $user);
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', $user->employeeNo )->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        try {
            $request->validate([
                'confirmation' => ['regex:(usuń|usun)'],
            ],
                [
                    'confirmation.regex' => 'Nie można usunąć użytkownika: niepoprawna wartość. Wpisz "usuń".',
                ]);
        }
        catch (Exception $e) {
            return redirect()->back()->with('status_err', $e->getMessage());
        }

        try {
            $user = User::where('employeeNo',$request->remove_id)->first();
            if(!$user instanceof User) {
                Log::channel('error')->error('Error deleting user: error occurred in Profile->destroy method. Failed to find user '.$request->remove_id. ' in the database.', [
                    'employeeNo' => $user->employeeNo,
                ]);
                return redirect()->back()->with('status_err', 'Nie udało się usunąć użytkownika: nie znleziono użytkownika w systemie.');
            }
            $user->delete();
        }
        catch(Exception $e) {
            Log::channel('error')->error('Error deleting user: error occurred in Profile->destroy method. Failed to delete user '.$request->remove_id. ' from the database.', [
                'employeeNo' => $user->employeeNo,
            ]);
            return redirect()->back()->with('status_err', 'Nie udało się usunąć użytkownika: problem przy usuwaniu z systemu.');
        }
        return redirect()->route('employee.index')->with('status', 'Usunięto użytkownika');
    }



    private function validateUpdate(Request $request, User $user) {

        $request->validate([
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

