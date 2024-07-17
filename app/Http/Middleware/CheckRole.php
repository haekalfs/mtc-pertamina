<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $roles = array_slice(func_get_args(), 2);
        $user = $request->user();
        $userRoles = $user->role_id()->whereIn('role_name', $roles)->get();

        if ($userRoles->count() > 0) {
            return $next($request);
        }
        Session::flash('failed', "You don't have rights to access this page!");
        return redirect('/');
    }

}
