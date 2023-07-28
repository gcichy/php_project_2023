<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeStatisticsController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//profile
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'create'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile_edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile_edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile_edit', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//emplopyees
Route::middleware('auth')->group(function () {
    Route::get('/pracownicy', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/pracownicy/{id}/work', [EmployeeController::class, 'workDetails'])->name('employee.details.work');
    Route::get('/pracownicy/{id}/profile', [EmployeeController::class, 'profileDetails'])->name('employee.details.profile');
});

//production
Route::middleware('auth')->group(function () {
    Route::get('/produkcja', [ProductionController::class, 'index'])->name('production.index');
});
require __DIR__.'/auth.php';

//schedule
Route::middleware('auth')->group(function () {
    Route::get('/harmonogram', [ScheduleController::class, 'index'])->name('schedule.index');
});

//boss statistics
Route::middleware('auth')->group(function () {
    Route::get('/statystki', [StatisticsController::class, 'index'])->name('stastistics.index');
});


require __DIR__.'/auth.php';

