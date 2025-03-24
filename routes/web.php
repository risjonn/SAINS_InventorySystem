<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ThresholdController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard and related API endpoints (unchanged)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats'])
    ->name('dashboard.stats')
    ->middleware(['auth', 'verified']);

Route::get('/dashboard/stocks', [DashboardController::class, 'getStockData'])
    ->name('dashboard.stocks')
    ->middleware(['auth', 'verified']);

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only stock management routes (now allow both Admin and Super Admin)
Route::middleware(['auth', 'role:Admin|Super Admin'])->group(function () {
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');

    // Route to low stock page
    Route::get('stocks/low', [StockController::class, 'lowStock'])->name('stocks.low')->middleware('auth');

    // Dedicated Stock Management page
    Route::get('stocks/management', [StockController::class, 'management'])->name('stocks.management');
    
    // Stock Transaction IN Operation routes
    Route::get('stocks/transactions/in', [StockController::class, 'createTransactionIn'])
         ->name('stocks.transactions.in');
    Route::post('stocks/transactions/in', [StockController::class, 'storeTransactionIn'])
         ->name('stocks.transactions.storeIn');

        // Inline OUT operation from the management page
        Route::post('/stocks/transactions/storeOutLoanReturn', [StockController::class, 'storeOutLoanReturn'])->name('stocks.transactions.storeOutLoanReturn');
        Route::get('stocks/loan', [StockController::class, 'loanedStocks'])->name('stocks.loan');


    // Edit and delete stock routes
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
});

// Super Admin routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::resource('products', ProductController::class);

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    
    // Super Admin routes for Categories
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});

// General stock routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Route for displaying stock details by category – note the "category" prefix!
    Route::get('stocks/by-category/{category}/{division}', [StockController::class, 'showByCategory'])
    ->name('stocks.byCategory');

    Route::get('stocks/search', [StockController::class, 'search'])->name('stocks.search');
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    
    // Place fixed routes before dynamic ones.
    Route::get('stocks/overview', [StockController::class, 'overview'])->name('stocks.overview');
    Route::get('stocks/transactions', [StockController::class, 'transactions'])->name('stocks.transactions');
    Route::get('/stocks/transactions/search', [StockController::class, 'ajaxSearch'])->name('stocks.transactions.search');
    
    // Route for showing a specific stock. This should come after the category route.
    Route::get('stocks/show/{category}/{division}', [StockController::class, 'show'])->name('stocks.show');

    Route::get('/thresholds/user', [ThresholdController::class, 'getUserThresholds'])->name('thresholds.user');
    Route::post('/thresholds/update', [ThresholdController::class, 'updateThreshold'])->name('thresholds.update');

    Route::get('/transactions/details', [StockController::class, 'getTransactionDetails'])
    ->name('transactions.details');

    //test route
    Route::get('/test', function () {
        return view('test');
    });
});

// Logout route
Route::post('/logout', function (Request $request) {
    session()->forget('selectedSection');
    session()->invalidate();
    session()->regenerateToken();
    Auth::logout();
    return redirect('/')->with('status', 'logged-out');
})->name('logout');

// Auth routes (register, login, etc.)
require __DIR__.'/auth.php';
