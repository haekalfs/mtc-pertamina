<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('auth.register', ['roles' => $roles, 'departments' => $departments, 'positions' => $positions]);
    }
}
