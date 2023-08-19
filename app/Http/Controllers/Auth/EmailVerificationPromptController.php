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
        $employee = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->get()[0]);

//        Mail::to($employee->email)->send(new VerifyEmail($employee));
        if ($employee->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $employee->sendEmailVerificationNotification();
//        Mail::to($employee->email)->send(new VerifyEmail($employee));
        return $employee->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email', [
                'employee' => $employee,
            ]);
    }
}
