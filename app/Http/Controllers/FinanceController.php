<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function dashboard()
    {
        return view('finance.index');
    }

    public function vendor_payment()
    {
        return view('finance.submenu.vendor_payment');
    }

    public function costs()
    {
        return view('finance.submenu.costs');
    }
}
