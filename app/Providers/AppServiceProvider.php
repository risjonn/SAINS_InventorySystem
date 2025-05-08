<?php

namespace App\Providers;

use App\Models\PasswordResetRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(): void
    {
        // 1. Use Bootstrap for all paginators
        Paginator::useBootstrap();

        // 2. Composer that runs on *every* view
        View::composer('*', function ($view) {
            // pull the currently selected division ID from session (default 'all')
            $selectedDivision = session('selectedDivision', 'all');

            // build your base LOAN query, then conditionally filter by division_id
            $loanedQuery = Stock::where('status', 'LOAN')
                ->when(
                    $selectedDivision !== 'all',
                    fn($q) => $q->where('division_id', $selectedDivision)
                );

            // fetch and count only the truly overdue ones
            $overdueCount = $loanedQuery
                ->get()
                ->filter(function (Stock $stock) {
                    $start = $stock->loan_start_date ?? $stock->updated_at;
                    $period = $stock->loan_period ?? 90;
                    $daysElapsed = Carbon::parse($start)->diffInDays(now());
                    return $daysElapsed > $period;
                })
                ->count();

            // share $overdueCount with every view
            $view->with('overdueCount', $overdueCount);
        });

        View::composer('*', function($view) {
            $resetRequestsCount = PasswordResetRequest::where('completed', 'Incomplete')
                ->where('type', 'reset')
                ->count();

            $recoverRequestsCount = PasswordResetRequest::where('completed', 'Incomplete')
                ->where('type', 'recover')
                ->count();

            $totalRequestsCount = $resetRequestsCount + $recoverRequestsCount;

            $view->with('resetRequestsCount', $resetRequestsCount)
                ->with('recoverRequestsCount', $recoverRequestsCount)
                ->with('totalRequestsCount', $totalRequestsCount);
        });
    }
}
