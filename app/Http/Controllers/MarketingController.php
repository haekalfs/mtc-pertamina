<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function index()
    {
        return view('marketing.index');
    }

    public function campaign()
    {
        $data = Campaign::all();
        $users = User::all();
        return view('marketing.submenu.campaign', ['data' => $data, 'users' => $users]);
    }

    public function company_agreement(Request $request)
    {
        $data = Agreement::all();
        $statuses = Status::all();

        return view('marketing.submenu.agreement', ['data' => $data, 'statuses' => $statuses]);
    }
}
