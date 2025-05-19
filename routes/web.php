<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProfileController;
use \App\Http\Controllers\UserManagementController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ThresholdController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DivisionController;
use \App\Http\Controllers\PasswordRequestController;
use App\Http\Controllers\ReportController;


// Public route
Route::get('/', function () {
    return redirect()->route('login');
});

// Public "Forgot Password" routes (no authentication required)
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
     ->name('password.forgot');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
     ->name('password.forgot.handle');

Route::get('forgot-password/status',[PasswordResetLinkController::class, 'status'])
     ->name('password.forgot.status');

Route::post('forgot-password/recover', [PasswordResetLinkController::class, 'recover'])
     ->middleware('guest')
     ->name('password.recover');

Route::get('/password/check-status', [PasswordRequestController::class, 'checkStatus'])->name('password.check.status');

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

// Admin-only stock management routes (Admin and Super Admin)
Route::middleware(['auth', 'role:Admin|Super Admin'])->group(function () {
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::get('stocks/low', [StockController::class, 'lowStock'])->name('stocks.low')->middleware('auth');
    Route::get('stocks/management', [StockController::class, 'management'])->name('stocks.management');

    // Stock Transaction IN Operation routes
    Route::get('stocks/transactions/in', [StockController::class, 'createTransactionIn'])
         ->name('stocks.transactions.in');
    Route::post('stocks/transactions/in', [StockController::class, 'storeTransactionIn'])
         ->name('stocks.transactions.storeIn');

    // Inline OUT operation from the management page
    Route::post('/stocks/transactions/storeOutLoanReturn', [StockController::class, 'storeOutLoanReturn'])
         ->name('stocks.transactions.storeOutLoanReturn');

    Route::get('stocks/loan', [StockController::class, 'loanedStocks'])->name('stocks.loan');

    // Edit and delete stock routes
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
});

// Super Admin routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])
        ->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->name('users.destroy');
    Route::post('/users/{user}/deactivate', [UserManagementController::class,'deactivate'])
        ->name('users.deactivate');
    Route::post('/users/{user}/activate',   [UserManagementController::class,'activate'])
        ->name('users.activate');
    Route::post('/users/{user}/restore',    [UserManagementController::class,'restore'])
        ->name('users.restore');

    Route::resource('division', DivisionController::class);

    Route::get('/divisions/active', function () {
        // Get all active divisions
        $divisions = App\Models\Division::where('active', '1')->get();
        return response()->json($divisions);
    })->name('api.divisions.active');

    // List password reset requests for Super Admin
    Route::get('password-requests', [PasswordRequestController::class, 'index'])
    ->name('admin.password.requests');

    Route::post('password-requests/reset', [PasswordRequestController::class, 'update'])
    ->name('admin.password.requests.reset');

    // Process the reset password action
    Route::post('password-requests/reset', [PasswordRequestController::class, 'update'])
    ->name('admin.password.requests.update');

    Route::post('password-requests/recover', [PasswordRequestController::class, 'updateRecover'])
    ->name('admin.password.requests.updateRecover');

    Route::resource('products', ProductController::class);

    Route::put('products/{product}', [ProductController::class, 'update'])
    ->name('products.update');

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    
    // Categories routes for Super Admin
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/categories/active', function () {
        // Get all active categories
        $categories = App\Models\Category::where('active', '1')->get();
        return response()->json($categories);
    })->name('api.categories.active');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});

// General stock routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('stocks/by-category/{category}/{division}', [StockController::class, 'showByCategory'])
         ->name('stocks.byCategory');
    Route::get('stocks/search', [StockController::class, 'search'])->name('stocks.search');
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');

    Route::get('stocks/low/{division}/category/{category}', 
    [StockController::class, 'lowByCategory'])
    ->name('stocks.low.byCategory');
    
    // Place fixed routes before dynamic ones.
    Route::get('stocks/overview', [StockController::class, 'overview'])->name('stocks.overview');
    Route::get('stocks/transactions', [StockController::class, 'transactions'])->name('stocks.transactions');
    Route::get('/stocks/transactions/search', [StockController::class, 'ajaxSearch'])->name('stocks.transactions.search');
    
    // Route for showing a specific stock (after category route)
    Route::get('stocks/show/{category}/{division}', [StockController::class, 'show'])->name('stocks.show');

    Route::get('/thresholds/user', [ThresholdController::class, 'getUserThresholds'])->name('thresholds.user');
    Route::post('/thresholds/update', [ThresholdController::class, 'updateThreshold'])->name('thresholds.update');
    Route::post('/thresholds/{product}', [ThresholdController::class, 'update'])
     ->name('thresholds.update');

    Route::get('/transactions/details', [StockController::class, 'getTransactionDetails'])
         ->name('transactions.details');

    // For regular stock details
    Route::get('/stocks/details', [StockController::class, 'getStockDetails'])->name('stocks.details');
    // For available stock details
    Route::get('/stocks/available-details', [StockController::class, 'getAvailableStockDetails'])->name('stocks.availableDetails');
    Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('/reports/stock', [StockController::class, 'stockReport'])->name('reports.stock');


    // Test route
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
