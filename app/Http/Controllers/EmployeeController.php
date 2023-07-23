<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display the employee dashboard.
     */
    public function index(Request $request): View
    {
        $employees = User::all();

        if($request->user()->role == 'employee')
        {
            return view('dashboard', [
                'user' => $request->user(),
            ]);
        }

        if(is_null($request->status)) {
            return view('employee.dashboard', [
                'user' => $request->user(),
                'employees' =>$employees,
            ]);
        }
        else {
            return view('employee.dashboard', [
                'user' => $request->user(),
                'employees' =>$employees,
                'status' => $request->status,
            ]);
        }

    }
}
