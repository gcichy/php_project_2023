<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Models\User;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    use HasEnsure;
    /**
     * Display the employee dashboard.
     */
    public function index(Request $request): View
    {
        $employees = User::all();

        if($request->user()->role == 'pracownik')
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

    public function details(Request $request, string $employeeNo): View
    {
        $currentUser = Auth::user();
//        $employeeID = (int) array_slice(explode('/', $request->url()), -2, 1)[0];
        $employee = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->firstOrFail());
        $userData = getUserData::getUserData($employee);

        return view('employee.employee_details', [
            'user' => $employee,
            'userData' => $userData,
            'currentUser' => $currentUser,
        ]);

    }

}
