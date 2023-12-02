<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductionCycle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionCycleController extends Controller
{
    public function index(Request $request): View
    {
        $parent_cycles = DB::select("
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

        return view('production.production', [
            'parent_cycles' => $parent_cycles,
            'child_cycles' => $child_cycles,
            'user' => $request->user(),
        ]);
    }
}
