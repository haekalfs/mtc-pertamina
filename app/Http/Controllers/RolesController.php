<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Usr_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('manage-roles.index', ['roles' => $roles]);
    }

    /**
     * Store a newly created role in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_code' => 'required',
            'role_name' => 'required|string|max:255',
            'role_description' => 'nullable|string',
        ]);

        // Create a new role
        Role::create([
            'role' => $request->input('role_code'),
            'description' => $request->input('role_name'),
            'role_description' => $request->input('role_description'),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Role has been created successfully.');
    }

    /**
     * Delete the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if the role is used in the Usr_role table
        $isRoleUsed = Usr_role::where('role_id', $id)->exists();

        if ($isRoleUsed) {
            // If the role is used, set a flash message and redirect back
            Session::flash('failed', 'Role cannot be deleted because it is assigned to a user.');
            return redirect()->back();
        }

        // If the role is not used, delete it
        $role = Role::findOrFail($id);
        $role->delete();

        // Set a success message and redirect back
        Session::flash('success', 'Role deleted successfully.');
        return redirect()->back();
    }
}
