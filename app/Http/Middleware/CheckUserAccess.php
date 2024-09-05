<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User_access;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next, $usr_acc)
    {
        // Retrieve allowed roles for the page
        $allowedRole = User_access::where('page_id', $usr_acc)
            ->join('roles', 'user_access.role_id', '=', 'roles.id')
            ->pluck('roles.role')
            ->toArray();

        // Get allowed roles from session
        $allowedRolesInSession = session('allowed_roles');

        // Check for intersection of roles
        $intersection = array_intersect($allowedRole, $allowedRolesInSession);

        // If no intersection, abort with a 403 error
        if (empty($intersection)) {
            abort(403, "YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
        }

        return $next($request);
    }
}
