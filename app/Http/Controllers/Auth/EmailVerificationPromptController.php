<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    use HasEnsure;

    /**
     * Display the email verification prompt and send verification email.
     */
    public function __invoke(Request $request,string $employeeNo): RedirectResponse|View
    {

        $user = $this->ensureIsNotNullUser($request->user());
        $employee = User::where('employeeNo',$employeeNo)->get()[0];

        Mail::to($employee->email)->send(new VerifyEmail($employee));

        return $user->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email');
    }
}
