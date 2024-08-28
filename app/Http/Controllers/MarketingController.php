<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index()
    {
        $queryAgreement = Agreement::query();
        $countAgreement = $queryAgreement->count();
        $getAgreements = $queryAgreement->limit(3)->get();

        $queryCampaign = Campaign::query();
        $countCampaign = $queryCampaign->count();
        $campaignChart = Campaign::select(DB::raw('date, COUNT(*) as count'))
        ->groupBy('date')
        ->get();


        return view('marketing.index', ['countAgreement' => $countAgreement, 'getAgreements' => $getAgreements, 'campaignChart' => $campaignChart, 'countCampaign' => $countCampaign]);
    }

    public function campaign(Request $request)
    {
        // Get current year and the selected year and month from the request
        $currentYear = now()->year;
        $yearSelected = $request->input('year', 'all');  // Default to 'all' if not provided
        $monthSelected = $request->input('month', 'all'); // Default to 'all' if not provided

        // Query to filter data based on year and month
        $query = Campaign::query();

        if ($yearSelected !== 'all') {
            $query->whereYear('created_at', $yearSelected);
        }

        if ($monthSelected !== 'all') {
            $query->whereMonth('created_at', $monthSelected);
        }

        $data = $query->get();
        $users = User::all();

        return view('marketing.submenu.campaign', [
            'data' => $data,
            'users' => $users,
            'yearSelected' => $yearSelected,
            'monthSelected' => $monthSelected,
            'currentYear' => $currentYear
        ]);
    }

    public function company_agreement(Request $request)
    {
        $data = Agreement::all();
        $statuses = Status::all();

        return view('marketing.submenu.agreement', ['data' => $data, 'statuses' => $statuses]);
    }
}
