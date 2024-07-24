<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Users_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DepartmentPositionController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('manage-dept-post.index', ['positions' => $positions, 'departments' => $departments]);
    }

    public function department_store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
        ]);

        Department::create([
            'department_name' => $request->department_name,
        ]);

        return redirect()->back()->with('success', 'Department registered successfully');
    }

    public function position_store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'priority_level' => 'required|integer',
        ]);

        Position::create([
            'position_name' => $request->position_name,
            'position_level' => $request->priority_level,
        ]);

        return redirect()->back()->with('success', 'Position registered successfully');
    }

    /**
     * Delete the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_department($id)
    {
        // Check if the role is used in the Usr_role table
        $isDeptUsed = Users_detail::where('department_id', $id)->exists();

        if ($isDeptUsed) {
            // If the role is used, set a flash message and redirect back
            Session::flash('failed', 'Department cannot be deleted because it is assigned to a user.');
            return redirect()->back();
        }

        // If the role is not used, delete it
        $dept = Department::findOrFail($id);
        $dept->delete();

        // Set a success message and redirect back
        Session::flash('success', 'Department deleted successfully.');
        return redirect()->back();
    }

    /**
     * Delete the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_position($id)
    {
        // Check if the role is used in the Usr_role table
        $isPostUsed = Users_detail::where('position_id', $id)->exists();

        if ($isPostUsed) {
            // If the role is used, set a flash message and redirect back
            Session::flash('failed', 'Position cannot be deleted because it is assigned to a user.');
            return redirect()->back();
        }

        // If the role is not used, delete it
        $position = Position::findOrFail($id);
        $position->delete();

        // Set a success message and redirect back
        Session::flash('success', 'Position deleted successfully.');
        return redirect()->back();
    }
}
