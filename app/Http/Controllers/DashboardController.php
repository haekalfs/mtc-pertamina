<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Infografis_peserta;
use App\Models\Instructor;
use App\Models\Inventory_tools;
use App\Models\MorningBriefing;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use App\Models\Regulation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $roles = Auth::user()->role_id->pluck('role_name')->toArray();
            if (!session()->has('allowed_roles')) {
                session()->put('allowed_roles', $roles);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        $headline = Campaign::orderBy('updated_at', 'desc')->take(min(Campaign::count(), 12))->get();
        $getPesertaCount = Infografis_peserta::count();
        $queryCampaign = Campaign::query();
        $countCampaign = $queryCampaign->count();

        //sum profits
        $rawProfits = Profit::sum('profit');

        // Calculate the average feedback score
        $averageFeedbackScore = DB::table('feedback_mtc')
        ->select(DB::raw('
            avg(
                (
                    COALESCE(relevansi_materi, 0) +
                    COALESCE(manfaat_training, 0) +
                    COALESCE(durasi_training, 0) +
                    COALESCE(sistematika_penyajian, 0) +
                    COALESCE(tujuan_tercapai, 0) +
                    COALESCE(kedisiplinan_steward, 0) +
                    COALESCE(fasilitasi_steward, 0) +
                    COALESCE(layanan_pelaksana, 0) +
                    COALESCE(proses_administrasi, 0) +
                    COALESCE(kemudahan_registrasi, 0) +
                    COALESCE(kondisi_peralatan, 0) +
                    COALESCE(kualitas_boga, 0) +
                    COALESCE(media_online, 0) +
                    COALESCE(rekomendasi, 0)
                ) / 14
            ) as average_score
        '))
        ->value('average_score');

        $getAssetCount = Inventory_tools::count();
        $getAssetStock = Inventory_tools::sum('initial_stock');

        // Count total instructors
        $instructorCount = Instructor::count();

        //Jumlah Pelatihan
        $penlatCount = Penlat::count();

        //Jumlah Batch
        $batchCount = Penlat_batch::count();
        $regulations = Regulation::orderBy('created_at', 'desc')->take(6)->get();
        $morningBriefing = MorningBriefing::orderBy('updated_at', 'desc')->take(1)->get();

        // Cache::forget('jobs_processing');
        return view('dashboard', [
            'morningBriefing' => $morningBriefing,
            'regulations' => $regulations,
            'headline' => $headline,
            'getPesertaCount' => $getPesertaCount,
            'countCampaign' => $countCampaign,
            'rawProfits' => $rawProfits,
            'averageFeedbackScore' => $averageFeedbackScore,
            'getAssetCount' => $getAssetCount,
            'getAssetStock' => $getAssetStock,
            'instructorCount' => $instructorCount,
            'penlatCount' => $penlatCount,
            'batchCount' => $batchCount
        ]);
    }
}
