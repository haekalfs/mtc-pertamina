<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Role;
use App\Models\User_access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageAccessController extends Controller
{
    public function index()
    {
        $r_name = Role::all();

        $pages = Page::all();

        $user_access = DB::table('roles')
        ->select('roles.id', 'pages.description AS pageDesc', 'roles.description', 'pages.id AS page_id', 'user_access.created_at')
        ->leftJoin('user_access', 'roles.id', '=', 'user_access.role_id')
        ->rightJoin('pages', 'pages.id', '=', 'user_access.page_id')
        ->orderBy('page_id', 'asc')
        ->get();

        $usersAcData = [];
        foreach ($user_access as $userAc) {
            if (!isset($usersAcData[$userAc->page_id])) {

                // If this is the first time we've seen this user, create a new entry in the $usersData array
                $usersAcData[$userAc->page_id] = [
                    'page_id' => $userAc->page_id,
                    'page' => $userAc->pageDesc,
                    'GrantAcc' => [],
                    'created_at' => $userAc->created_at
                ];
            }

            // Add the role to the list of roles for this user
            $usersAcData[$userAc->page_id]['GrantAcc'][] = $userAc->description;
        }

        // Convert the $usersData array into a simple list of users with comma-delimited roles
        $usersAcList = [];
        $autoIncrement = 1;
        foreach ($usersAcData as $userAcData) {
            $grantList = implode(', ', $userAcData['GrantAcc']);
            $usersAcList[] = [
                'id' => $autoIncrement++,
                'page_id' => $userAcData['page_id'],
                'page' => $userAcData['page'],
                'grantTo' => $grantList,
                'created_at' => $userAcData['created_at']
            ];
        }

        return view('manage-access.index', ['access' => $usersAcList, 'pages' => $pages,'r_name' => $r_name]);
    }

    public function grant_access_to_roles(Request $request)
    {
        $this->validate($request, [
            'inputPage' => 'required',
            'inputRole' => 'required'
        ]);

        $roleName = Role::where('id', $request->inputRole)->pluck('description')->first();
        User_access::create([
            'page_id' => $request->inputPage,
            'role_id' => $request->inputRole
        ]);
        return redirect()->back()->with('success', "Access Granted to $roleName");
    }

    public function remove_access($id)
    {
        User_access::where('page_id', $id)->delete();
        return redirect()->back()->with('failed', 'All Access has been Removed!');
    }
}
