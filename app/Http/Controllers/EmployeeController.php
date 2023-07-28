<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use http\Url;
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

    public function workDetails(Request $request): View
    {
        $employeeID = (int) array_slice(explode('/', $request->url()), -2, 1)[0];
        $employee = User::find($employeeID);

        return view('employee.employee_details_work', [
            'user' => $employee,
        ]);




    }

    public function profileDetails(Request $request): View
    {
        $employeeID = (int) array_slice(explode('/', $request->url()), -2, 1)[0];
        $employee = User::find($employeeID);

        $userData = getUserData::getUserData($employee);

        return view('employee.employee_details_profile', [
            'user' => $employee,
            'userData' => $userData,
        ]);


    }
}
