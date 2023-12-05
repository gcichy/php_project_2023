<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ParentCycleView;
use App\Models\ProductionCycle;
use App\Models\User;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductionCycleController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $users = User::select('employeeNo', 'role')->get();

        if(is_null($request->exp_end_start) or is_null($request->exp_end_end)) {
            if(!(is_null($request->exp_end_start) and is_null($request->exp_end_end))) {
                $status_err = 'Nie przefiltrowano terminu. Aby filtrować po terminie podaj początek i koniec.';
            }
            $current_time = new DateTime();
            $end_time = $current_time->format('Y-m-d');
            $current_time->sub(new DateInterval('P14D'));
            $start_time = $current_time->format('Y-m-d');
            $filt_start_time = $start_time;
            $filt_end_time = $end_time;
            $parent_cycles = ParentCycleView::whereBetween('expected_end_time', [$start_time." 00:00:00",$end_time." 23:59:59"]);
        }
        else {
            $filt_start_time = $request->exp_end_start;
            $filt_end_time = $request->exp_end_end;
            $parent_cycles = ParentCycleView::whereBetween('expected_end_time', [$request->exp_end_start." 00:00:00",$request->exp_end_end." 23:59:59"]);
        }

        $where_clause = $this->createWhereClause($request);
        if(!empty($where_clause)) {
            try {
                foreach ($where_clause as $column => $in_clause) {
                    $parent_cycles = $parent_cycles->whereIn($column, $in_clause);
                }
                $parent_cycles->paginate(10);
            } catch(Exception $e) {
                Log::channel('error')->error('Error filtering parent cycles grid: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                $parent_cycles = ParentCycleView::paginate(10);
                $status_err = 'Nie udało się przefiltrować - błąd systemu.';
            }
        } else {
            $parent_cycles = $parent_cycles->paginate(10);
        }
        $child_cycles = DB::select("
            select * from (
                select
                    cy1.id as parent_id,
                    cy2.id as child_id,
                    cy2.level,
                    cy2.component_id,
                    null as prod_schema_id,
                    null as prod_schema_sequence_no,
                    cy2.finished,
                    c2.name,
                    c2.height,
                    c2.length,
                    c2.width,
                    c2.image,
                    cy2.current_amount,
                    cy2.total_amount,
                    cy2.defect_amount,
                    CONCAT(CAST(CAST(cy2.current_amount/cy2.total_amount*100 as INT ) as CHAR),'%') as style_progress,
                    CONCAT(CAST(ROUND(cy2.current_amount/cy2.total_amount*100,2) as CHAR),'%') as progress,
                    CONCAT(CAST(ROUND(IFNULL(cy2.defect_amount/(cy2.defect_amount+cy2.current_amount),0)*100,2) as CHAR),'%') as defect_percent,
                    IFNULL(w.time_spent,0) as time_spent,
                    IFNULL(case when w.time_spent < 60
                                    then CONCAT('0:',IF(w.time_spent < 10,'0',''),CAST(w.time_spent as char))
                                else  CONCAT(CAST(CAST((w.time_spent - (w.time_spent % 60))/60 as int) as char),':',IF(w.time_spent % 60 < 10,'0',''),CAST(w.time_spent % 60 as char))
                               end,'0:00') as time_spent_in_hours,
                    IFNULL(ROUND(pstd.amount_per_hour *  w.time_spent / 60,2),0) as expected_amount_per_spent_time,
                    IFNULL(amount_per_hour * 8,0) as expected_amount_per_time_frame,
                    'day' as expected_amount_time_frame,
                    c2.material,
                    c2.description
                from production_cycle cy1
                join production_cycle cy2
                    on cy1.id = cy2.parent_id
                join component c2
                    on cy2.component_id = c2.id
                left join (select production_cycle_id, product_id, component_id, sum(duration_minute) as time_spent
                           from work
                           group by production_cycle_id, product_id, component_id) w
                    on cy1.id = w.production_cycle_id
                    and cy2.component_id = w.component_id
                left join (select component_id, 60/sum(duration_hours*60/amount) as amount_per_hour
                           from production_standard
                           group by component_id) pstd
                    on cy2.component_id = pstd.component_id
                where cy1.level = 1
            union
                select
                    cy1.id as parent_id,
                    cy3.id as child_id,
                    cy3.level,
                    cy2.component_id as component_id,
                    cy3.production_schema_id as prod_schema_id,
                    cps.sequence_no as prod_schema_sequence_no,
                    cy3.finished,
                    ps3.production_schema,
                    null,
                    null,
                    null,
                    null,
                    cy3.current_amount,
                    cy3.total_amount,
                    cy3.defect_amount,
                    CONCAT(CAST(CAST(cy3.current_amount/cy3.total_amount*100 as INT ) as CHAR),'%') as style_progress,
                    CONCAT(CAST(ROUND(cy3.current_amount/cy3.total_amount*100,2) as CHAR),'%') as progress,
                    CONCAT(CAST(ROUND(IFNULL(cy3.defect_amount/(cy3.defect_amount+cy3.current_amount),0)*100,2) as CHAR),'%') as defect_percent,
                    IFNULL(w.time_spent,0) as time_spent,
                    IFNULL(case when w.time_spent < 60
                                    then CONCAT('0:',IF(w.time_spent < 10,'0',''),CAST(w.time_spent as char))
                                else  CONCAT(CAST(CAST((w.time_spent - (w.time_spent % 60))/60 as int) as char),':',IF(w.time_spent % 60 < 10,'0',''),CAST(w.time_spent % 60 as char))
                               end,'0:00') as time_spent_in_hours,
                    IFNULL(ROUND(pstd.amount_per_hour *  w.time_spent / 60,2),0) as expected_amount_per_spent_time,
                    IFNULL(amount_per_hour*8,0) as expected_amount_per_time_frame,
                    'day' as expected_amount_time_frame,
                    null,
                    ps3.description
                from production_cycle cy1
                join production_cycle cy2
                    on cy1.id = cy2.parent_id
                join production_cycle cy3
                    on cy2.id = cy3.parent_id
                join production_schema ps3
                    on cy3.production_schema_id = ps3.id
                join component_production_schema cps
                    on ps3.id = cps.production_schema_id
                    and cy2.component_id = cps.component_id
                left join (select production_cycle_id, product_id, component_id, production_schema_id, sum(duration_minute) as time_spent
                            from work
                            group by production_cycle_id, product_id, component_id, production_schema_id) w
                       on cy1.id = w.production_cycle_id
                       and cy3.production_schema_id = w.production_schema_id
                left join (select production_schema_id, component_id, amount/duration_hours as amount_per_hour
                        from production_standard) pstd
                       on cy2.component_id = pstd.component_id
                       and cy3.production_schema_id = pstd.production_schema_id
                where cy1.level = 1
            union
                select
                    cy1.id as parent_id,
                    cy2.id as child_id,
                    cy2.level,
                    cy1.component_id as component_id,
                    cy2.production_schema_id as prod_schema_id,
                    cps.sequence_no as prod_schema_sequence_no,
                    cy2.finished,
                    ps2.production_schema,
                    null,
                    null,
                    null,
                    null,
                    cy2.current_amount,
                    cy2.total_amount,
                    cy2.defect_amount,
                    CONCAT(CAST(CAST(cy2.current_amount/cy2.total_amount*100 as INT ) as CHAR),'%') as style_progress,
                    CONCAT(CAST(ROUND(cy2.current_amount/cy2.total_amount*100,2) as CHAR),'%') as progress,
                    CONCAT(CAST(ROUND(IFNULL(cy2.defect_amount/(cy2.defect_amount+cy2.current_amount),0)*100,2) as CHAR),'%') as defect_percent,
                    IFNULL(w.time_spent,0) as time_spent,
                    IFNULL(case when w.time_spent < 60
                                    then CONCAT('0:',IF(w.time_spent < 10,'0',''),CAST(w.time_spent as char))
                                else  CONCAT(CAST(CAST((w.time_spent - (w.time_spent % 60))/60 as int) as char),':',IF(w.time_spent % 60 < 10,'0',''),CAST(w.time_spent % 60 as char))
                               end,'0:00') as time_spent_in_hours,
                    IFNULL(ROUND(pstd.amount_per_hour *  w.time_spent / 60,2),0) as expected_amount_per_spent_time,
                    IFNULL(amount_per_hour * 8,0) as expected_amount_per_time_frame,
                    'day' as expected_amount_time_frame,
                    null,
                    ps2.description
                from production_cycle cy1
                         join production_cycle cy2
                              on cy1.id = cy2.parent_id
                         join production_schema ps2
                              on cy2.production_schema_id = ps2.id
                         join component_production_schema cps
                              on ps2.id = cps.production_schema_id
                                  and cy1.component_id = cps.component_id
                         left join (select production_cycle_id, component_id, production_schema_id, sum(duration_minute) as time_spent
                                    from work
                                    where product_id is null
                                    group by production_cycle_id, component_id, production_schema_id) w
                              on cy1.id = w.production_cycle_id
                              and cy2.production_schema_id = w.production_schema_id
                         left join (select production_schema_id, component_id, amount/duration_hours as amount_per_hour
                                    from production_standard) pstd
                               on cy1.component_id = pstd.component_id
                               and cy2.production_schema_id = pstd.production_schema_id
                where cy1.level = 1) sub_cycles
        order by sub_cycles.parent_id, sub_cycles.component_id, sub_cycles.prod_schema_sequence_no
        ");

        $order_table = array(
            'status' => 'Status',
            'name' => 'Nazwa',
            'productivity' => 'Produktywność (%)',
            'expected_end_time' => 'Termin',
            'progress' => 'Postęp (%)',
            'start_time' => 'Start cyklu',
            'end_time' => 'Koniec cyklu',
            'expected_start_time' => 'Planowany start',
            'current_amount' => 'Ilość',
            'total_amount' => 'Cel',
            'defect_amount' => 'Defekty',
            'time_spend' => 'Czas pracy (h)',
            'expected_amount_per_time_frame' => 'Oczekiwana ilość (szt)',
        );

        return view('production.production', [
            'parent_cycles' => $parent_cycles,
            'child_cycles' => $child_cycles,
            'user' => $user,
            'users' => $users,
            'order' => $order_table,
            'status' => isset($status)? $status : null,
            'status_err' => isset($status_err)? $status_err : null,
            'filt_start_time' => $filt_start_time,
            'filt_end_time' => $filt_end_time,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }


    private function createWhereClause($request): array
    {
        $where_in_clause = array();
        if(!is_null($request->name)) {
            $where_in_clause['name'] = array($request->name);
        }
        if(!is_null($request->status)) {
            $where_in_clause['status'] = explode(',',$request->status);
        }
        if(!is_null($request->category)) {
            $where_in_clause['category'] = explode(',',$request->category);
        }
        if(!is_null($request->employees)) {
            $where_in_clause['category'] = explode(',',$request->category);
        }
        return $where_in_clause;
    }
}
