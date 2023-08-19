<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EmailVerificationNotificationController extends Controller
{
    use HasEnsure;

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request, string $employeeNo): RedirectResponse
    {
        $employee = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->get()[0]);
        if ($employee->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $employee->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
