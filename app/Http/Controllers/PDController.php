<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDController extends Controller
{
    public function index()
    {
        return view('plan_dev.index');
    }

    public function feedback_report()
    {
        return view('plan_dev.submenu.feedback');
    }

    public function feedback_report_import()
    {
        return view('plan_dev.submenu.feedback-import');
    }

    public function regulation()
    {
        return view('plan_dev.submenu.regulation');
    }

    public function certificate()
    {
        return view('plan_dev.submenu.certificate');
    }

    public function instructor()
    {
        return view('plan_dev.submenu.instructor');
    }

    public function training_reference()
    {
        return view('plan_dev.submenu.training-reference');
    }
}
