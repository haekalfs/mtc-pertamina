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

    public function company_agreement(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $periodeSelected ?? $nowYear;

        return view('marketing.submenu.agreement', [
            'yearsBefore' => $yearsBefore
        ]);
    }
}
