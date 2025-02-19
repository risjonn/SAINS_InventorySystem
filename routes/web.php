    <?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\StockController;
    use App\Http\Controllers\DashboardController;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    //use App\Http\Controllers\StockTransactionController;


    // Public route
    Route::get('/', function () {
        return view('welcome');
    });

    // ✅ Fixed: Single Dashboard Route with Middleware
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(['auth', 'verified']);

    // ✅ API endpoint for stats (requires authentication)
    Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats'])
        ->name('dashboard.stats')
        ->middleware(['auth', 'verified']);

    // ✅ API endpoint for fetching stocks by section
    Route::get('/dashboard/stocks', [DashboardController::class, 'getStockData'])
        ->name('dashboard.stocks')
        ->middleware(['auth', 'verified']);

    // ✅ Profile routes (Only for authenticated users)
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ✅ Admin-only stock management routes
    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');

        // Route to low stock page
        Route::get('stocks/low', [StockController::class, 'lowStock'])->name('stocks.low')->middleware('auth');

        // Dedicated Stock Management page
        Route::get('stocks/management', [StockController::class, 'management'])->name('stocks.management');
        
        // Routes for Stock Transaction IN Operation
        Route::get('stocks/transactions/in', [StockController::class, 'createTransactionIn'])
        ->name('stocks.transactions.in');
        Route::post('stocks/transactions/in', [StockController::class, 'storeTransactionIn'])
        ->name('stocks.transactions.storeIn');

        // Inline OUT operation from the management page
        Route::post('stocks/{stock}/out', [StockController::class, 'outOperation'])->name('stocks.out');
        Route::post('/stocks/transactions/storeOutOrLoan', [StockController::class, 'storeOutOrLoan'])->name('stocks.transactions.storeOutOrLoan');


        // Edit stock route (check for admin manually in controller)
        Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
        Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');

        // Delete stock route (check for admin manually in controller)
        Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
    });

    // User Manager routes
    Route::middleware(['auth', 'role:User Manager'])->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
    });

// ✅ General stock routes for authenticated users
    Route::middleware(['auth'])->group(function () {
        Route::get('stocks/search', [StockController::class, 'search'])->name('stocks.search');
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
        // Route::get('stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');

        // Place fixed routes before dynamic ones.
        Route::get('stocks/overview', [StockController::class, 'overview'])->name('stocks.overview');
        Route::get('stocks/transactions', [StockController::class, 'transactions'])->name('stocks.transactions');
        Route::get('/stocks/transactions/search', [StockController::class, 'ajaxSearch'])->name('stocks.transactions.search');


       // In your routes/web.php
        // Route for displaying stock details by category
        Route::get('stocks/category/{category?}', [StockController::class, 'showByCategory'])->name('stocks.byCategory');
        Route::get('stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
        Route::get('stocks/{category}', [StockController::class, 'show'])->name('stocks.show');

    });


    // Logout route (clear session and perform logout)
    Route::post('/logout', function (Request $request) {
        // ✅ Clear Laravel session and section selection
        session()->forget('selectedSection'); // Remove section from Laravel session
        session()->invalidate(); // Invalidate the session
        session()->regenerateToken(); // Regenerate CSRF token

        // ✅ Perform Laravel logout
        Auth::logout();

        // ✅ Redirect to login with a message
        return redirect('/')->with('status', 'logged-out');
    })->name('logout');


    // Auth routes (register, login, etc.)
    require __DIR__.'/auth.php';