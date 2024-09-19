<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Campaign_type;
use App\Models\SocialsInsights;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index()
    {
        $headline = Campaign::orderBy('updated_at', 'desc')->take(min(Campaign::count(), 12))->get();
        $queryAgreement = Agreement::query();
        $countAgreement = $queryAgreement->count();
        $getAgreements = $queryAgreement->limit(3)->get();

        $queryCampaign = Campaign::query();
        $countCampaign = $queryCampaign->count();
        $campaignChart = Campaign::select(DB::raw('date, COUNT(*) as count'))
        ->groupBy('date')
        ->get();

        $visitorsInsight = SocialsInsights::sum('visitors_count');


        return view('marketing.index', ['headline' => $headline, 'countAgreement' => $countAgreement, 'getAgreements' => $getAgreements, 'campaignChart' => $campaignChart, 'countCampaign' => $countCampaign, 'visitors' => $visitorsInsight]);
    }

    public function campaign(Request $request)
    {
        // Get current year and the selected year and month from the request
        $currentYear = now()->year;
        $yearSelected = $request->input('year', 'all');  // Default to 'all' if not provided
        $monthSelected = $request->input('month', 'all'); // Default to 'all' if not provided
        $typeSelected = $request->input('typeSelected', '-1'); // Default to '-1' if not provided

        // Query to filter data based on year, month, and type
        $query = Campaign::query();

        if ($yearSelected !== 'all') {
            $query->whereYear('created_at', $yearSelected);
        }

        if ($monthSelected !== 'all') {
            $query->whereMonth('created_at', $monthSelected);
        }

        if ($typeSelected !== '-1') {
            $query->where('campaign_type_id', $typeSelected);
        }

        // Add pagination (6 items per page)
        $data = $query->paginate(6);
        $users = User::all();
        $campaignType = Campaign_type::all();

        return view('marketing.submenu.campaign', [
            'data' => $data,
            'users' => $users,
            'yearSelected' => $yearSelected,
            'monthSelected' => $monthSelected,
            'typeSelected' => $typeSelected,
            'currentYear' => $currentYear,
            'campaignType' => $campaignType
        ]);
    }

    public function company_agreement(Request $request)
    {
        // Fetch the selected period (year)
        $periode = $request->input('periode');

        // Get all agreements, apply filtering if a specific year is selected
        if ($periode && $periode != -1) {
            $data = Agreement::whereYear('date', $periode)->get();
        } else {
            $data = Agreement::all();
        }

        // Get all statuses
        $statuses = Status::all();

        return view('marketing.submenu.agreement', ['data' => $data, 'statuses' => $statuses, 'selectedPeriode' => $periode]);
    }
}
