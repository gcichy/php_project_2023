<?php

use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeStatisticsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StatisticsController;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Http\Request;
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
    Route::get('/profil/{employeeNo}', [ProfileController::class, 'index'])->name('profile.index');
//    Route::get('/profile_edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/edytuj-profil/{employeeNo}', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile_edit/{employeeNo}', function (Request $request, string $employeeNo) {
//        dd($request);
//    })->name('profile.update');
    Route::patch('/edytuj-profil/{employeeNo}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/usun-profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//employees
Route::middleware(['auth'])->group(function () {
    Route::get('/pracownicy', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/pracownicy/{employeeNo}', [EmployeeController::class, 'details'])->name('employee.details');
});

//products
Route::middleware(['auth'])->group(function () {
    Route::get('/produkty', [ProductController::class, 'index'])->name('product.index');
    Route::get('/produkty/{id}', [ProductController::class, 'productDetails'])->name('product.details');
    Route::get('/dodaj-produkt', [ProductController::class, 'addProduct'])->name('product.add');
    Route::post('/dodaj-produkt', [ProductController::class, 'storeProduct'])->name('product.store');
    Route::get('/dodaj-produkt/{id}', [ProductController::class, 'editProduct'])->name('product.add-similar');
    Route::get('/edytuj-produkt/{id}', [ProductController::class, 'editProduct'])->name('product.edit');
    Route::post('/edytuj-produkt', [ProductController::class, 'storeUpdatedProduct'])->name('product.update');
    Route::delete('/usun-produkt', [ProductController::class, 'destroyProduct'])->name('product.destroy');
});

//components
Route::middleware(['auth'])->group(function () {
    Route::get('/komponenty', [ComponentController::class, 'index'])->name('component.index');
    Route::get('/komponenty/{id}', [ComponentController::class, 'componentDetails'])->name('component.details');
    Route::get('/dodaj-komponent', [ComponentController::class, 'addComponent'])->name('component.add');
    Route::post('/dodaj-komponent', [ComponentController::class, 'storeComponent'])->name('component.store');
    Route::get('/dodaj-komponent/{id}', [ComponentController::class, 'editComponent'])->name('component.add-similar');
    Route::get('/edytuj-komponent/{id}', [ComponentController::class, 'editComponent'])->name('component.edit');
    Route::post('/edytuj-komponent', [ComponentController::class, 'storeUpdatedComponent'])->name('component.update');
    Route::delete('/usun-komponent', [ComponentController::class, 'destroyComponent'])->name('component.destroy');
});

//production
Route::middleware('auth')->group(function () {
    Route::get('/produkcja', [ProductionController::class, 'index'])->name('production.index');
});

//schedule
Route::middleware('auth')->group(function () {
    Route::get('/harmonogram', [ScheduleController::class, 'index'])->name('schedule.index');
});

//boss statistics
Route::middleware('auth')->group(function () {
    Route::get('/statystki', [StatisticsController::class, 'index'])->name('stastistics.index');
});


require __DIR__.'/auth.php';

