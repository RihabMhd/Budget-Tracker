<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated only
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile',         [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

    Route::get('/transactions',          [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions',          [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}',      [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}',      [TransactionController::class, 'destroy'])->name('transactions.destroy');


    Route::get('/budgets',            [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets',           [BudgetController::class, 'store'])->name('budgets.store');
    Route::patch('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
    Route::post('/budgets/monthly',     [BudgetController::class, 'storeMonthly'])->name('budgets.storeMonthly');
    Route::post('/budgets/category',    [BudgetController::class, 'storeCategory'])->name('budgets.storeCategory');

    Route::get('/categories',              [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories',             [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});