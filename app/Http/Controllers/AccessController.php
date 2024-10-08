<?php

namespace App\Http\Controllers;

use App\Models\User_access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccessController extends Controller
{
    public function usr_acc($usr_acc)
    {
        try {
            $roles = Auth::user()->role_id->pluck('role_name')->toArray();
            if (!session()->has('allowed_roles')) {
                session()->put('allowed_roles', $roles);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        $allowedRole = User_access::where('page_id', $usr_acc)
            ->join('roles', 'user_access.role_id', '=', 'roles.id')
            ->pluck('roles.role')
            ->toArray();

        $allowedRolesInSession = session('allowed_roles');

        $intersection = array_intersect($allowedRole, $allowedRolesInSession);

        if (empty($intersection)) {
            abort(403, "YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
        }

        return $intersection;
    }
}
