<?php

namespace App\Http\Middleware;

use App\Utils\ResponseCode;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return ResponseCode::unauthorized('Unauthorized: Please login first.');
        }

        // No role relation found
        if (!isset($user->role)) {
            return ResponseCode::forbidden('Access denied. Role not found.');
        }

        // Role not allowed
        if (!in_array($user->role->name, $roles)) {
            return ResponseCode::forbidden("Access denied. You don't have permission to access this resource.");
        }

        return $next($request);
    }
}
