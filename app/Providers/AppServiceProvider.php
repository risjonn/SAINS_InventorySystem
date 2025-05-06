<?php

namespace App\Providers;

use App\Models\PasswordResetRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

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
