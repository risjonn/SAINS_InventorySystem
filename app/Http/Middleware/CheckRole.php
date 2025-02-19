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
        // Split roles using the pipe delimiter
        $allowedRoles = explode('|', $roles);
        $allowedRoles = array_map('strtolower', array_map('trim', $allowedRoles));
        
        $userRole = auth()->check() ? strtolower(auth()->user()->role) : null;
    
        if (!$userRole || !in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }
    
        return $next($request);
    }
}
