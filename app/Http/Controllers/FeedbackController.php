<?php

namespace App\Http\Controllers;

use App\Models\Feedback_mtc;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends Controller
{
    public function feedback_mtc(Request $request)
    {

        if ($request->ajax()) {
            $query = Feedback_mtc::query();

            // Return the DataTables response
            return DataTables::of($query)
                ->make(true);
        }

        return view('plan_dev.submenu.feedback-mtc.index');
    }

    public function feedback_mtc_import()
    {
        return view('plan_dev.submenu.feedback-mtc.feedback-mtc-import');
    }
}
