<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Accepts one or multiple roles (comma separated) that are allowed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $allowedRoles = array_map('trim', explode(',', $roles));
        if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
