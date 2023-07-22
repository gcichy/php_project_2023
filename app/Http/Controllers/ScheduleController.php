<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display the user's schedule.
     */
    public function index(Request $request): View
    {
        return view('schedule.schedule', [
            'user' => $request->user(),
        ]);
    }
}
