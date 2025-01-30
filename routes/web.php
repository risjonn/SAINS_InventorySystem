<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
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

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only stock management routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::get('stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
});

// Stock management routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('stocks/search', [StockController::class, 'search'])->name('stocks.search');
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
});

// Auth routes
require __DIR__.'/auth.php';
