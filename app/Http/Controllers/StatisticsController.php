<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard.
     */
    public function index(Request $request): View
    {
        if($request->user()->role == 'employee') {
            return view('statistics.empl_stat_dashboard', [
                'user' => $request->user(),
            ]);
        }
        return view('statistics.stat_dashboard', [
            'user' => $request->user(),
        ]);
    }
}
