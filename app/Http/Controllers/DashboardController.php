<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function create(): View
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        try {
            $works = WorkView::where('exec_employees', 'like', '%'.$user->id.'%')->orderBy('start_time');
            $works = $works->paginate(15);
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering work grid in DashboardController create method: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);

            $works = WorkView::paginate(15);

            $status_err = 'Nie udało się załadować pracy - błąd systemu.';
        }

        return view('dashboard', [
            'works' => $works,
            'user' => $user,
            'status_err' => isset($status_err)? $status_err : null,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

}


