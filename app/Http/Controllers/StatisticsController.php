<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCycleView;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $users = User::select('id','employeeNo','role')->get();

        try {
            $data = $this->getLineBarChart($request);
            $chart_data_1 = $data['line_bar_chart'];
            $filt_start_date_1 = $data['dates'][0];
            $filt_end_date_1 = $data['dates'][1];
            $validation_err = $data['validation_err'];
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering statistics: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            $status_err = 'Błąd systemu - nie udało się załadować wykresów.';
            $chart_data_1 = null;
        }


        return view('statistics.stat_dashboard', [
            'filt_items' => [],
            'user' => $request->user(),
            'users' => $users,
            'chart_data_1' => $chart_data_1,
            'status_err' => isset($status_err)? $status_err : null,
            'filt_start_date_1' => $filt_start_date_1,
            'filt_end_date_1' => $filt_end_date_1,
            'validation_err' => $validation_err,
        ]);
    }

    /**
     * @throws Exception
     */
    private function getLineBarChart($request): array
    {
        $default_end_date = date("Y-m-d");
        $default_start_date = date("Y-m-d", strtotime("-2 weeks", strtotime(date("Y-m-d"))));
        $end_date = is_null($request->end_date_1)? $default_end_date: $request->end_date_1;
        $start_date = is_null($request->start_date_1)? $default_start_date : $request->start_date_1;

        if($end_date <= $start_date ) {
            $end_date = $default_end_date;
            $start_date = $default_start_date;
            $validation_err = "Data 'Praca od' musi być wcześniej niż data 'Praca do'";
        }

        $chart_data = DB::select("select d.work_date,
                                               work.weighetd_productivity_average,
                                               work_duration
                                        from (
                                                 select
                                                     date_id,
                                                     full_date as work_date
                                                 from dates
                                                 where full_date between '".$start_date."' and '".$end_date."'
                                             ) d
                                                 left join (
                                            select
                                                date_id,
                                                sum(productivity * duration_minute) / day_duration_minute as weighetd_productivity_average,
                                                avg(day_duration_minute) / 60 as work_duration
                                            from (
                                                     select
                                                         date_format(start_time, '%Y%m%d') AS date_id,
                                                         sum(duration_minute) over (partition by date(start_time) order by date(start_time)) day_duration_minute,
                                                         wv.*
                                                     from work_view wv) wv
                                            group by date_id) work
                                                           on work.date_id = d.date_id
                                        order by d.date_id");


        $chart_data = json_encode($chart_data);
        return ['line_bar_chart' => $chart_data,
            'dates' => [$start_date, $end_date],
            'validation_err' => isset($validation_err)? $validation_err : null,
        ];
    }
}
