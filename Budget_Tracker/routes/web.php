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

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\User\BudgetController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\GoalController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile',         [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/categories',              [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories',             [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/transactions',          [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions',          [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}',      [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}',      [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/goals',               [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/create',        [GoalController::class, 'create'])->name('goals.create');
    Route::post('/goals',              [GoalController::class, 'store'])->name('goals.store');
    Route::get('/goals/{goal}/edit',   [GoalController::class, 'edit'])->name('goals.edit');
    Route::put('/goals/{goal}',        [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}',     [GoalController::class, 'destroy'])->name('goals.destroy');
    Route::post('/goals/{goal}/funds', [GoalController::class, 'addFunds'])->name('goals.funds');
});
