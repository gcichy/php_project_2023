<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCycleView;
use App\Models\User;
use App\Models\Work;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

            $data = $this->getLineBarChart($request, $users);
            $chart_data_1 = $data['line_bar_chart'];
            $filt_start_date_1 = $data['dates'][0];
            $filt_end_date_1 = $data['dates'][1];
            $validation_err_1 = $data['validation_err'];
            $chart_title_1 = $data['chart_title'];
            $filt_users_1 = $data['user_id_array'];


            $data_2 = $this->getBarChart($request);
            $chart_data_2 = $data_2['line_bar_chart'];
            $filt_start_date_2 = $data_2['dates'][0];
            $filt_end_date_2 = $data_2['dates'][1];
            $validation_err_2= $data_2['validation_err'];
            $chart_title_2 = $data_2['chart_title'];

        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering statistics: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            $status_err = 'Błąd systemu - nie udało się załadować wykresów.';
            $chart_data_1 = null;
        }


        return view('statistics.stat_dashboard', [
            'user' => $request->user(),
            'users' => $users,
            'chart_data_1' => $chart_data_1,
            'status_err' => isset($status_err)? $status_err : null,
            'chart_title_1' => isset($chart_title_1)? $chart_title_1 : null,
            'filt_users_1' => isset($filt_users_1)? $filt_users_1 : null,
            'filt_start_date_1' => isset($filt_start_date_1)? $filt_start_date_1 : null,
            'filt_end_date_1' => isset($filt_end_date_1)? $filt_end_date_1 : null,
            'validation_err_1' => isset($validation_err_1)? $validation_err_1 : null,
            'chart_data_2' => $chart_data_2,
            'chart_title_2' => isset($chart_title_2)? $chart_title_2 : null,
            'filt_users_2' => isset($filt_users_2)? $filt_users_2 : null,
            'filt_start_date_2' => isset($filt_start_date_2)? $filt_start_date_2 : null,
            'filt_end_date_2' => isset($filt_end_date_2)? $filt_end_date_2 : null,
            'validation_err_2' => isset($validation_err_2)? $validation_err_2 : null,
        ]);
    }

    /**
     * @throws Exception
     */
    private function getBarChart($request): array
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

        $chart_data = DB::select("
                                       select d.work_date,
                                               d.min_date,
                                               work.weighted_productivity_average,
                                               work.work_duration,
                                               work.amount

                                        from (
                                                 select
                                                     min(full_date) as min_date,
                                                     concat(year,'-',month,'-(',min(day_of_month),'-',max(day_of_month),')') as work_date,
                                                     cast(concat(year,week_of_year) as int) as week_year_id
                                                 from dates
                                                 where full_date between '2024-01-01' and '2024-01-15'
                                                 group by year,week_of_year,month
                                             ) d
                                                 left join (
                                            select
                                                week_year_id,
                                                sum(productivity * duration_minute) / week_duration_minute as weighted_productivity_average,
                                                avg(week_duration_minute) / 60 as work_duration,
                                                avg(week_amount) as amount
                                            from (
                                                     select
                                                         cast(concat(year(start_time),week(start_time))as int) as week_year_id,
                                                         sum(duration_minute) over (partition by cast(concat(year(start_time),week(start_time))as int)) week_duration_minute,
                                                         sum(amount) over (partition by cast(concat(year(start_time),week(start_time))as int)) week_amount,
                                                         wv.*
                                                     from work_view wv
                                                     where component_id = 11
                                                 ) wv
                                            group by week_year_id, week_duration_minute) work
                                                           on work.week_year_id = d.week_year_id
                                        order by d.min_date");

        $chart_title = 'Produktywność pracy dla';
        $chart_data = json_encode($chart_data);
        return ['line_bar_chart' => $chart_data,
            'dates' => [$start_date, $end_date],
            'validation_err' => isset($validation_err)? $validation_err : null,
            'chart_title' => $chart_title,
        ];
    }

    /**
     * @throws Exception
     */
    private function getlineBarChart($request, Collection $users): array
    {

        if(!is_null($request->employees)) {
            $user_where_clause = $request->employees;
            $user_id_array = explode(',',$user_where_clause);
            $employee_no_string = User::whereIn('id', $user_id_array)->select('employeeNo')->get();
            $employee_no_string = collect($employee_no_string)->map(function (User $arr) { return $arr->employeeNo; })->toArray();
            $employee_no_string = implode(',', $employee_no_string);
            $chart_title = 'Praca '.$employee_no_string;
        } else {
            $user_where = collect($users)->map(function (User $arr) { return $arr->id; })->toArray();
            $user_where_clause = implode(', ', $user_where);
            $chart_title = 'Praca firmy ogółem';

        }

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
                                               work.weighted_productivity_average,
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
                                                                sum(productivity * duration_minute) / day_duration_minute as weighted_productivity_average,
                                                                avg(day_duration_minute) / 60 as work_duration
                                                          from (
                                                             select
                                                                 date_format(start_time, '%Y%m%d') AS date_id,
                                                                 sum(duration_minute_per_user) over (partition by date(start_time) order by date(start_time)) day_duration_minute,
                                                                 wv.productivity,
                                                                 wu.duration_minute_per_user as duration_minute
                                                              from work_view wv
                                                                      join work_user wu
                                                                           on wu.work_id = wv.work_id
                                                                           and wu.user_id in (".$user_where_clause.")) wv
                                                            group by date_id) work
                                                                on work.date_id = d.date_id
                                            order by d.date_id");


        $chart_data = json_encode($chart_data);
        return ['line_bar_chart' => $chart_data,
            'dates' => [$start_date, $end_date],
            'validation_err' => isset($validation_err)? $validation_err : null,
            'chart_title' => $chart_title,
            'user_id_array' => isset($user_id_array)? $user_id_array : null,
        ];
    }


}
