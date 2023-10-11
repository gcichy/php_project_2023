<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('zaloguj', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('zaloguj', [AuthenticatedSessionController::class, 'store']);

    Route::get('zapomniales-hasla', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('zapomniales-hasla', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('resetuj-haslo/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('resetuj-haslo', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::get('rejestracja', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('rejestracja', [RegisteredUserController::class, 'store']);

    Route::get('zweryfikuj-email/{employeeNo}', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('zweryfikuj-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed','throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/weryfikacja-powiadomienie/{employeeNo}', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('potwierdz-haslo', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('potwierdz-haslo', [ConfirmablePasswordController::class, 'store']);

    Route::put('haslo', [PasswordController::class, 'update'])->name('password.update');

    Route::post('wyloguj', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
