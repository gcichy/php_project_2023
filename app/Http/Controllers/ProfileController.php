<?php

namespace App\Http\Controllers;

use App\Helpers\HasEnsure;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HasEnsure;
    /**
     * Display the user's profile.
     */
    public function index(Request $request): View
    {
        $userData = $this->getUserData($request);

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
        $userData = $this->getUserData($request);

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


        $data = $this->ensureIsArray($request->validated());

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
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
}

