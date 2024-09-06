<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StoreUserRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = Auth::user();

            // Ensure user is authenticated before proceeding
            if ($user) {
                // Get roles from the authenticated user
                $roles = $user->role_id->pluck('role_name')->toArray();

                // If session doesn't already have 'allowed_roles', store the roles
                if (!session()->has('allowed_roles')) {
                    session()->put('allowed_roles', $roles);
                }
            }
        } catch (\Exception $e) {
            // Log or handle exception if necessary (optional)
        }

        return $next($request);
    }
}
