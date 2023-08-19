<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function create(): View
    {
        $user = Auth::user();
        return view('dashboard')->with('user', $user)->with('role', $user->role);
    }

}
