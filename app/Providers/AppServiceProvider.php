<?php

namespace App\Providers;

use App\Models\Http_request_access;
use App\Models\User_access;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        Blade::if('role', function ($role) {
            return in_array($role, session('allowed_roles', []));
        });

        Blade::if('usr_acc', function (...$usr_acc) { // Accept multiple parameters
            $allowedRoles = User_access::whereIn('page_id', $usr_acc) // Directly pass as an array
                ->join('roles', 'user_access.role_id', '=', 'roles.id')
                ->pluck('roles.role')
                ->toArray();

            $allowedRolesInSession = session('allowed_roles', []);

            return !empty(array_intersect($allowedRoles, $allowedRolesInSession)); // Ensure boolean return
        });

        Blade::if('mtd_acc', function ($usr_acc) {
            $allowedRole = Http_request_access::where('method_id', $usr_acc)
                ->join('roles', 'http_request_access.role_id', '=', 'roles.id')
                ->pluck('roles.role')
                ->toArray();
            $allowedRolesInSession = session('allowed_roles', []);
            return array_intersect($allowedRole, $allowedRolesInSession);
        });
    }
}
