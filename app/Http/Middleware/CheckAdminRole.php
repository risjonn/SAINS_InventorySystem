<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['Admin', 'Super Admin'])) {
            abort(403, 'Unauthorized Action');
        }
        return $next($request);
    }
}
