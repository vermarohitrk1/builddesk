<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }
}
