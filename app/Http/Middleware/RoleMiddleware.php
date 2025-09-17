<?php

namespace App\Http\Middleware;

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
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // cek apakah role user ada di daftar roles yg diizinkan
        if (!in_array($user->role->name, $roles)) {
            return response()->json(['error' => 'Forbidden: You don\'t have access'], 403);
        }

        return $next($request);
    }
}
