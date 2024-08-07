<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function index()
    {
        return view('marketing.index');
    }

    public function campaign()
    {
        return view('marketing.submenu.campaign');
    }

    public function company_agreement()
    {
        return view('marketing.submenu.agreement');
    }
}
