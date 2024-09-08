<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomError2029Exception;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RestrictRequestRole
{
    public function handle($request, Closure $next)
    {
        // Get the current HTTP method (POST, PUT, DELETE, etc.)
        $currentMethod = $request->method();

        // Find the method_id from the `methods` table
        $method = DB::table('methods')->where('method', $currentMethod)->first();

        if (!$method) {
            return response()->json(['error' => 'Method not allowed'], 403);
        }

        // Get the allowed roles for the current method
        $allowedRoles = DB::table('http_request_access')
            ->join('roles', 'http_request_access.role_id', '=', 'roles.id')
            ->where('http_request_access.method_id', $method->id)
            ->pluck('roles.role')
            ->toArray();

        // Get allowed roles from session
        $allowedRolesInSession = Session::get('allowed_roles', []);

        // Check if the user has one of the allowed roles
        if (array_intersect($allowedRoles, $allowedRolesInSession)) {
            return $next($request);
        }

        // Throw custom exception for error 2029
        throw new CustomError2029Exception("YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
    }
}
