<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkView;
use http\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        return view('employee.dashboard', [
            'user' => $request->user(),
            'employees' =>$employees,
        ]);
    }

    public function details(Request $request, string $employeeNo): View
    {

        $currentUser = Auth::user();

        $employee = $this->ensureIsNotNullUser(User::where('employeeNo',$employeeNo)->firstOrFail());
        $userData = getUserData::getUserData($employee);
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        try {
            $works = WorkView::where('exec_employees', 'like', '%'.$employee->id.'%')->orderBy('start_time');
            $works = $works->paginate(15);
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering work grid in DashboardController create method: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);

            $works = WorkView::paginate(15);

            $status_err = 'Nie udało się załadować pracy - błąd systemu.';
        }


        return view('employee.employee_details', [
            'user' => $employee,
            'userData' => $userData,
            'currentUser' => $currentUser,
            'works' => $works,
            'status_err' => isset($status_err)? $status_err : null,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);

    }


}

