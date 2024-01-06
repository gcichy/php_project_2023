<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\CycleCreated;
use App\Models\ChildCycleView;
use App\Models\Component;
use App\Models\ComponentProductionSchema;
use App\Models\ParentCycleView;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductionCycle;
use App\Models\ProductionCycleUser;
use App\Models\ProductionSchema;
use App\Models\ReasonCode;
use App\Models\StaticValue;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkUser;
use App\Models\WorkView;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use JetBrains\PhpStorm\NoReturn;

class WorkController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $users = User::select('id','employeeNo', 'role')->get();
        try {
            $filt_by_time_table = $this->filterWorkByEndTime($request);
            $works = $filt_by_time_table['works'];
            $filt_start_time = $filt_by_time_table['filt_start_time'];
            $filt_end_time = $filt_by_time_table['filt_end_time'];
            $status_err = $filt_by_time_table['status_err'];
            $where_clause = $this->createWhereClause($request);
            $works = $this->filterWorks($request, $works, $where_clause);
            $filt_items = array_merge($where_clause['where_in'], $where_clause['where_like']);
            $works = $this->orderWorks($works, $request->order);
            $works = $works->paginate(3);
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering parent cycles grid: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            if(isset($works) and $works instanceof Builder) {
                $works = $works->paginate(10);
            } else {
                $works = WorkView::paginate(10);
            }
            $status_err = 'Nie udało się przefiltrować - błąd systemu.';
            $filt_start_time = isset($filt_start_time)? $filt_start_time : null;
            $filt_end_time = isset($filt_end_time)? $filt_end_time : null;
        }

        $order_items = is_string($request->order)? explode(',',$request->order) : null;

        $order_table = array(
            'start_time' => 'Początek pracy',
            'end_time' => 'Koniec pracy',
            'amount' => 'Ilość (szt)',
            'productivity' => 'Produktywność (%)',
            'duration_minute' => 'Czas pracy (h)',
            'cycle_category' => 'Kategoria cyklu',
            'production_schema' => 'Zadanie',
            'task_name' => 'Podzadanie',
            'component_name' => 'Materiał',
            'product_name' => 'Produkt',
            'defect_amount' => 'Defekty (szt)',
            'defect_percent' => 'Defekty (%)',
            'exp_amount_per_time_spent' => 'Oczek. ilość/Czas pracy',
            'exp_amount_per_hour' => 'Oczek. ilość/godzina (szt)',
            'waste_amount' => 'Odpady',
        );

        return view('work.work', [
            'works' => $works,
            'user' => $user,
            'users' => $users,
            'order' => $order_table,
            'status' => isset($status)? $status : null,
            'status_err' => isset($status_err)? $status_err : null,
            'filt_start_time' => $filt_start_time,
            'filt_end_time' => $filt_end_time,
            'filt_items' => isset($filt_items)? $filt_items : [],
            'order_items' => $order_items,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function addWorkWrapper(): RedirectResponse
    {
        session(['add_work' => true]);
        return  redirect()->route('production.index');
    }

    public function addWork(Request $request, $id): View|RedirectResponse
    {
        $user = Auth::user();
        $parent_cycle = ParentCycleView::where('cycle_id', $id)->first();
        $users = User::select('id','employeeNo', 'role')->get();
        $child_cycles = ChildCycleView::where('parent_id', $id)->paginate(10);
        $reason_codes = ReasonCode::select('reason_code',DB::raw("concat(reason_code, ' - ', description) as reason_code_desc"))->get();
        $units = Unit::select('unit')->get();
        $modal_data = null;
        $child_components = null;
        $child_prod_schemas = null;
        if($parent_cycle->category == 1) {
            $child_components = ChildCycleView::where(['child_cycle_view.parent_id' => $id,
                'child_cycle_view.prod_schema_id' => null])->get();
            $child_prod_schemas = array();
            $child_schemas = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->join('task','task.production_schema_id', '=', 'child_cycle_view.prod_schema_id')
                ->select('child_cycle_view.*',DB::raw('task.id as task_id, task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('child_cycle_view.component_id', 'asc')
                ->orderBy('child_cycle_view.prod_schema_sequence_no','asc')
                ->orderBy('task_sequence_no','asc');

            foreach ($child_components as $comp) {
                $temp_schemas = clone $child_schemas;
                $child_prod_schemas[$comp->child_id] = $temp_schemas->where('component_id', $comp->component_id)->get();
            }
            $modal_data = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->select('child_id','status','category','image','name','productivity','time_spent_in_hours',
                        'current_amount','expected_amount_per_spent_time','total_amount','progress','start_time',
                        'end_time','expected_amount_per_time_frame','expected_time_to_complete_in_hours',
                        'defect_amount','defect_percent','waste_amount','waste_unit')
                ->orderBy('child_cycle_view.component_id', 'asc')
                ->orderBy('child_cycle_view.prod_schema_sequence_no','asc');
        }
        else if($parent_cycle->category == 2) {
            $child_prod_schemas = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->join('task','task.production_schema_id', '=', 'child_cycle_view.prod_schema_id')
                ->select('child_cycle_view.*',DB::raw('task.id as task_id, task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('child_cycle_view.prod_schema_sequence_no','asc')
                ->orderBy('task.sequence_no','asc')->get();

            $modal_data = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->select('child_id','status','category','image','name','productivity','time_spent_in_hours',
                    'current_amount','expected_amount_per_spent_time','total_amount','progress','start_time',
                    'end_time','expected_amount_per_time_frame','expected_time_to_complete_in_hours',
                    'defect_amount','defect_percent','waste_amount','waste_unit')
                ->orderBy('child_cycle_view.prod_schema_sequence_no','asc');
        }
        else if($parent_cycle->category == 3) {
            $child_prod_schemas = ParentCycleView::where('cycle_id', $id)
                ->join('production_cycle','production_cycle.id', '=', 'parent_cycle_view.cycle_id')
                ->join('task','task.production_schema_id', '=', 'production_cycle.production_schema_id')
                ->select('parent_cycle_view.*',DB::raw('task.production_schema_id as prod_schema_id, task.id as task_id, task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('task.sequence_no','asc')->get();
        }

        $parent_modal = ParentCycleView::where('cycle_id', $id)
            ->select(DB::raw('cycle_id as child_id'),'status','category','image','name','productivity','time_spent_in_hours',
                'current_amount','expected_amount_per_spent_time','total_amount','progress','start_time',
                'end_time','expected_amount_per_time_frame','expected_time_to_complete_in_hours',
                'defect_amount','defect_percent',DB::raw('null as waste_amount,null as waste_unit'));

        if(isset($modal_data)) {
            $modal_data = $modal_data->union($parent_modal)->get();

        } else {
            $modal_data = $parent_modal->get();
        }

        if(count($child_prod_schemas) == 0) {
            return back();
        }

        $checked_boxes_id_string = null;
        if(count(old()) > 0) {
            $checked_boxes_id_string = $this->getOldCheckedRowsId(old());
        }

//        $max_time = new DateTime('now');
//        $max_time->setTime(23, 59);
//        $max_time = $max_time->format('Y-m-d\TH:i');
        $max_time = date("Y-m-d");

        dd($child_prod_schemas);
        return view('work.work-add', [
            'p_cycle' => $parent_cycle,
            'child_cycles' => $child_cycles,
            'child_components' => $child_components,
            'child_prod_schemas' => $child_prod_schemas,
            'modal_data' => $modal_data,
            'checked_boxes_id_string' => $checked_boxes_id_string,
            'user' => $user,
            'users' => $users,
            'reason_codes' => $reason_codes,
            'max_time' => $max_time,
            'units' => $units,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function storeWork(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        try {
            $return_array = $this->validateWork($request, $id, $employee_no);
            $suffix_array = $return_array['SUFFIX'];
            $employee_id_array = $return_array['EMPLOYEEID'];
        }
        catch (Exception $e) {
            if($e instanceof ValidationException) {
                $error_arr = $e->validator->getMessageBag()->all();
                $error_arr[] = "- Zwróć uwagę na kolumnę 'Pracownicy'. Jeśli dodałeś więcej niż jednego pracownika do któregoś podzadania, uzupełnij ją ponownie.";
                return back()->with('validation_err', $error_arr)->withInput();
            }
            else if ($e->getCode() == 2) {
                return back()->with('validation_err', [$e->getMessage(),"- Zwróć uwagę na kolumnę 'Pracownicy'. Jeśli dodałeś więcej niż jednego pracownika do któregoś podzadania, uzupełnij ją ponownie."])->withInput();
            }
            else if($e->getCode() == 1) {
                return back()->with('status_err', $e->getMessage())->withInput();
            }
            else {
                Log::channel('error')->error("Error inserting work: ".$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return back()->with('status_err', 'Nie udało się dodać pracy. Wystąpił nieoczekiwany błąd systemu.')->withInput();
            }
        }

        try {
            DB::beginTransaction();
            $category = intval($request->selected_cycle_category);
            if($category == 1) {
                $parent_cycle = ProductionCycle::where('id', $id)->first();
                $component_cycle_id = intval($request->selected_component_cycle_id);
                $prod_schema_cycle_id = intval($request->selected_prod_schema_cycle_id);

                $child_component_cycle = ProductionCycle::where('id', $component_cycle_id)->first();
                $other_child_component_cycles = ProductionCycle::where('parent_id', $id)
                    ->where('id', '!=', $component_cycle_id);
                $child_schema_cycle = ProductionCycle::where('id', $prod_schema_cycle_id)->first();
                $other_child_schema_cycles = ProductionCycle::where('parent_id', $component_cycle_id)
                    ->where('id', '!=', $prod_schema_cycle_id);

                $work_id_array = $this->insertWorkWrapper($request, $employee_id_array, $suffix_array,
                    $id, $employee_no, $child_component_cycle->component_id, $parent_cycle->product_id);

                $work_stats = $this->updateProdSchemaCycle($id, $prod_schema_cycle_id, $child_schema_cycle, $work_id_array, $employee_no);

                $updated_child_schema_cycle  = ProductionCycle::where('id', $prod_schema_cycle_id)->first();

                $this->updateComponentCycle($child_component_cycle, $updated_child_schema_cycle,
                    $other_child_schema_cycles, $work_stats, $employee_no);

                $updated_child_component_cycle  = ProductionCycle::where('id', $component_cycle_id)->first();

                $this->updateProductCycle($parent_cycle, $updated_child_component_cycle,
                    $other_child_component_cycles, $work_stats, $employee_no);
            }
            else if($category == 2) {
                $parent_cycle = ProductionCycle::where('id', $id)->first();
                $prod_schema_cycle_id = intval($request->selected_prod_schema_cycle_id);
                $child_schema_cycle = ProductionCycle::where('id', $prod_schema_cycle_id)->first();
                $other_child_schema_cycles = ProductionCycle::where('parent_id', $id)
                    ->where('id', '!=', $prod_schema_cycle_id);

                $work_id_array = $this->insertWorkWrapper($request, $employee_id_array, $suffix_array,
                                                           $id, $employee_no, $parent_cycle->component_id, null);

                $work_stats = $this->updateProdSchemaCycle($id, $prod_schema_cycle_id, $child_schema_cycle, $work_id_array, $employee_no);

                $updated_child_schema_cycle  = ProductionCycle::where('id', $prod_schema_cycle_id)->first();

                $this->updateComponentCycle($parent_cycle, $updated_child_schema_cycle,$other_child_schema_cycles,
                                            $work_stats, $employee_no);


            }
            else if($category == 3) {
                $parent_cycle = ProductionCycle::where('id', $id)->first();

                $work_id_array = $this->insertWorkWrapper($request, $employee_id_array, $suffix_array,
                    $id, $employee_no, null, null);

                $this->updateProdSchemaCycle($id, $id, $parent_cycle, $work_id_array, $employee_no);
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            if($e->getCode() == 1) {
                return back()->with('status_err', $e->getMessage())->withInput();
            }
            else {
                Log::channel('error')->error("Error inserting work: ".$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return back()->with('status_err', 'Nie udało się dodać pracy. Wystąpił nieoczekiwany błąd przy wprowadzaniu danych do systemu.')->withInput();
            }
        }

        return back()->with('status', 'Dodano pracę.');
    }


    /**
     * @throws Exception
     */
    private function insertWorkWrapper(Request $request, array $employee_id_array, array   $suffix_array, int $id,
                                       string $employee_no, $component_id = null, $product_id = null): array
    {
        $work_id_array = [];
        foreach ($suffix_array as $suffix) {
            $ids = $this->parseSuffix($suffix, $employee_no);
            $prod_schema_id = $ids[0];
            $task_id = $ids[1];
            if(!(array_key_exists($suffix, $employee_id_array) and is_array($employee_id_array[$suffix]))) {
                Log::channel('error')->error('Error inserting work: Error while processing work table insert. $employee_id_array does not contain key: '.$suffix
                    .' or entry is not an array.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu przy wprowadzaniu danych.', 1);
            }
            $employee_id_task_array = $employee_id_array[$suffix];
            $work_id = $this->insertWork($request, $id, $suffix, $product_id, $component_id, $prod_schema_id, $task_id, $employee_id_task_array, $employee_no);

            $work_id_array[] = $work_id;
        }
        return $work_id_array;
    }
    private function insertWork(Request $request, int $cycle_id, string $suffix,
                                int|null $product_id, int|null $component_id,  int $prod_schema_id, int $task_id,
                                array $employee_id_task_array, string $employee_no): int
    {
        $start_time = 'start_time'.$suffix;
        $end_time = 'end_time'.$suffix;
        $duration_minute = 'work_duration'.$suffix;
        $duration_coef = max(count($employee_id_task_array), 1);
        $additional_comment = 'comment'.$suffix;
        $amount = 'amount'.$suffix;
        $defect_amount = 'defect'.$suffix;
        $defect_reason_code = 'defect_rc'.$suffix;
        $waste_amount = 'waste'.$suffix;
        $waste_reason_code = 'waste_rc'.$suffix;
        $waste_unit = 'waste_unit'.$suffix;
        $waste_unit_id = Unit::where('unit', $request->$waste_unit)->select('id')->first();
        if($waste_unit_id instanceof  Unit) {
            $waste_unit_id = $waste_unit_id->id;
        }
        else {
            $waste_unit_id = null;
        }


        $work_id = DB::table('work')->insertGetId([
            'production_cycle_id' => $cycle_id,
            'production_schema_id' => $prod_schema_id,
            'task_id' => $task_id,
            'component_id' => $component_id,
            'product_id' => $product_id,
            'start_time' => $request->$start_time,
            'end_time' => $request->$end_time,
            'duration_minute' => $request->$duration_minute * $duration_coef,
            'amount' => $request->$amount,
            'defect_amount' => $request->$defect_amount,
            'defect_reason_code' => $request->$defect_reason_code,
            'waste_amount' => $request->$waste_amount,
            'waste_reason_code' => $request->$waste_reason_code,
            'waste_unit_id' => $waste_unit_id,
            'additional_comment' => $request->$additional_comment,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);


        foreach ($employee_id_task_array as $user_id) {
            DB::table('work_user')->insert([
                'work_id' => $work_id,
                'user_id' => $user_id,
                'duration_minute_per_user' => $request->$duration_minute,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);
        }
        return $work_id;
    }

    private function updateProductCycle(ProductionCycle $product_cycle, ProductionCycle $updated_child_component_cycle,
                                          Builder $other_child_component_cycles, Work $work_stats, string $employee_no): void
    {
        $update_amount = $product_cycle->current_amount;
        $update_finished = 0;
        $update_start_time = $product_cycle->start_time;
        $update_end_time = $product_cycle->end_time;
        $update_duration_minute_sum = $product_cycle->duration_minute_sum + $work_stats->sum_duration;

        $updated_child_component_amount = $updated_child_component_cycle->current_amount;
        $updated_child_component_start_time = $updated_child_component_cycle->start_time;
        $updated_child_component_end_time = $updated_child_component_cycle->end_time;
        $other_stats = $other_child_component_cycles->select(DB::raw('min(current_amount) as min_amount'))->first();

        //amount of prod cycle is updated only when current_amount for each child comp cycle is greater than prod cycle current_amount
        if($other_stats instanceof  ProductionCycle and !is_null($other_stats->min_amount)) {
            if(min($other_stats->min_amount, $updated_child_component_amount) > $product_cycle->current_amount) {
                $update_amount = min($other_stats->min_amount, $updated_child_component_amount);
            }
        }
        else {
            if($updated_child_component_amount > $product_cycle->current_amount) {
                $update_amount = $updated_child_component_amount;
            }
        }

        //start_time is set if it was null before or work start time was earlier than cycle start time
        if(is_null($update_start_time) or $updated_child_component_start_time < $update_start_time) {
            $update_start_time = $updated_child_component_start_time;
        }

        //comp cycle is finished if calculated amount exceeds total_amount
        if($update_amount >= $product_cycle->total_amount) {
            $update_finished = 1;
        }

        //end time is set if cycle is marked as finished
        if($update_finished == 1) {
            $update_end_time = $updated_child_component_end_time;
        }

        DB::table('production_cycle')->where('id',$product_cycle->id)->update([
            'start_time' => $update_start_time,
            'end_time' => $update_end_time,
            'duration_minute_sum' => $update_duration_minute_sum,
            'current_amount' => $update_amount,
            'finished' => $update_finished,
            'updated_by' => $employee_no,
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
    private function updateComponentCycle(ProductionCycle $component_cycle, ProductionCycle $updated_child_schema_cycle,
                                          Builder $other_child_schema_cycles, Work $work_stats, string $employee_no) :void
    {
        $update_amount = $component_cycle->current_amount;
        $update_defect_amount = $component_cycle->defect_amount;
        $update_finished = 0;
        $update_start_time = $component_cycle->start_time;
        $update_end_time = $component_cycle->end_time;
        $update_duration_minute_sum = $component_cycle->duration_minute_sum + $work_stats->sum_duration;

        $updated_child_schema_amount = $updated_child_schema_cycle->current_amount;
        $updated_child_schema_start_time = $updated_child_schema_cycle->start_time;
        $updated_child_schema_end_time = $updated_child_schema_cycle->end_time;

        $other_stats = $other_child_schema_cycles->select(DB::raw('min(current_amount) as min_amount, max(sequence_no) as max_sequence_no'))->first();

        //amount of comp cycle is updated only when current_amount for each child cycle is greater
        if($other_stats instanceof  ProductionCycle and !is_null($other_stats->min_amount)) {
            if(min($other_stats->min_amount, $updated_child_schema_amount) > $component_cycle->current_amount) {
                $update_amount = min($other_stats->min_amount, $updated_child_schema_amount);
            }
        }
        else {
            if($updated_child_schema_amount > $component_cycle->current_amount) {
                $update_amount = $updated_child_schema_amount;
            }
        }

        //defect is added to component cycle only if defect for last prod schema cycle was reported
        if($other_stats instanceof ProductionCycle and !is_null($other_stats->max_sequence_no)) {
            if($updated_child_schema_cycle->sequence_no > $other_stats->max_sequence_no) {
                $update_defect_amount += $work_stats->defect_amount;
            }
        }
        else {
            $update_defect_amount += $work_stats->defect_amount;
        }

        //start_time is set if it was null before or work start time was earlier than cycle start time
        if(is_null($update_start_time) or $updated_child_schema_start_time < $update_start_time) {
            $update_start_time = $updated_child_schema_start_time;
        }

        //comp cycle is finished if calculated amount exceeds total_amount
        if($update_amount >= $component_cycle->total_amount) {
            $update_finished = 1;
        }

        //end time is set if cycle is marked as finished
        if($update_finished == 1) {
            $update_end_time = $updated_child_schema_end_time;
        }

        DB::table('production_cycle')->where('id',$component_cycle->id)->update([
            'start_time' => $update_start_time,
            'end_time' => $update_end_time,
            'duration_minute_sum' => $update_duration_minute_sum,
            'current_amount' => $update_amount,
            'defect_amount' => $update_defect_amount,
            'finished' => $update_finished,
            'updated_by' => $employee_no,
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
    private function updateProdSchemaCycle(int $parent_cycle_id, int $schema_cycle_id, ProductionCycle $prod_schema_cycle,
                                           array $work_id_array, string $employee_no): Work
    {
        $update_stats = Work::whereIn('id', $work_id_array)
            ->select(DB::raw('min(start_time) as work_start, max(end_time) as work_end,
                            sum(duration_minute) as sum_duration, max(amount) as amount,
                            max(defect_amount) as defect_amount'))->first();


        $update_start_time = (is_null($prod_schema_cycle->start_time) or $update_stats->work_start < $prod_schema_cycle->start_time)? $update_stats->work_start : $prod_schema_cycle->start_time;
        $update_duration_minute_sum = $prod_schema_cycle->duration_minute_sum + $update_stats->sum_duration;
        $update_amount = $prod_schema_cycle->current_amount + $update_stats->amount;
        $update_finished = $update_amount >= $prod_schema_cycle->total_amount? 1 : 0;
        $update_end_time = null;
        if($update_finished == 1) {
            //max end time among all cycle's works is taken as cycle end time
            $max_cycle_end_time = Work::where('production_cycle_id', $parent_cycle_id)->select(DB::raw('max(end_time) as end_time'))->first();
            if($max_cycle_end_time instanceof Work) {
                $update_end_time = $max_cycle_end_time->end_time;
            }
        }
        $update_defect_amount = $prod_schema_cycle->defect_amount + $update_stats->defect_amount;

        DB::table('production_cycle')->where('id',$schema_cycle_id)->update([
            'start_time' => $update_start_time,
            'end_time' => $update_end_time,
            'duration_minute_sum' => $update_duration_minute_sum,
            'current_amount' => $update_amount,
            'defect_amount' => $update_defect_amount,
            'finished' => $update_finished,
            'updated_by' => $employee_no,
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        return $update_stats;
    }
    /**
     * @throws Exception
     */
    private function parseSuffix(string $suffix, string $employee_no): array
    {
        $id_parts = substr($suffix, 1);
        $id_parts = explode('_',$id_parts);
        if(count($id_parts) != 2) {
            Log::channel('error')->error("Error inserting work: parsing suffix error. Suffix: '".$suffix."' doesn't contain prod_schema_id or task_id", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie udało się przetworzyć danych.', 1);
        }
        $id_parts[0] = intval($id_parts[0]);
        $id_parts[1] = intval($id_parts[1]);
        if(!ProductionSchema::find($id_parts[0]) instanceof ProductionSchema) {
            Log::channel('error')->error("Error inserting work: parsing suffix error. ProductionSchema with id: ".$id_parts[0]." extracted from Suffix: '".$suffix."' not found in the system.", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie udało się przetworzyć danych.', 1);
        }
        if(!Task::find($id_parts[1]) instanceof Task) {
            Log::channel('error')->error("Error inserting work: parsing suffix error. Task with id: ".$id_parts[1]." extracted from Suffix: '".$suffix."' not found in the system.", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie udało się przetworzyć danych.', 1);
        }

        return $id_parts;
    }
    private function getOldCheckedRowsId(array $old): string
    {
        $checkedRows = array_intersect_key($old, array_flip(preg_grep('/^check_/', array_keys($old))));
        $checkbox_id_string = '';
        foreach ($checkedRows as $key => $value) {
            $parts = explode('check_', $key);
            if(count($parts) > 0) {
                $ids = $parts[count($parts)-1];
                $checkbox_id_string .= 'selected-check-'.str_replace('_', '-', $ids).';';
            }
        }
        return $checkbox_id_string;
    }
    /**
     * @throws Exception
     */
    private function validateWork(Request $request, int $id, string $employee_no): array
    {
        if(!ProductionCycle::where(['id' => $id, 'parent_id' => null])->first() instanceof ProductionCycle) {
            Log::channel('error')->error("Error inserting work: validation failed. ProductionCycle with id: ".$id." not found.", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu przy walidacji danych.', 1);
        }
        if(is_null($request->selected_cycle_category) or !in_array($request->selected_cycle_category,['1','2','3'])) {
            Log::channel('error')->error("Error inserting work: validation failed. Incorrect 'selected_cycle_category' input value: ".$request->selected_cycle_category.".", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu przy walidacji danych.', 1);
        }
        if($request->selected_cycle_category == '1') {
            if(!(ctype_digit($request->selected_component_cycle_id) and (int)$request->selected_component_cycle_id > 0
                and ctype_digit($request->selected_prod_schema_cycle_id) and (int)$request->selected_prod_schema_cycle_id > 0) ) {
                Log::channel('error')->error("Error inserting work: validation failed. Incorrect 'selected_component_cycle_id' or 'selected_prod_schema_cycle_id' input values:
                component_cycle - " . $request->selected_component_cycle_id .
                    "production_schema_cycle" . $request->selected_prod_schema_cycle_id . ".", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie znaleziono cyklu dla wybranego materiału.', 1);
            }
            if(!ProductionCycle::find($request->selected_component_cycle_id) instanceof ProductionCycle) {
                Log::channel('error')->error("Error inserting work: validation failed. 'selected_component_cycle_id' input value error: Production cycle with id = ".$request->selected_component_cycle_id." not found.", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie znaleziono cyklu dla wybranego zadania.', 1);
            }
        }
        if($request->selected_cycle_category == '2') {
            if(!(ctype_digit($request->selected_prod_schema_cycle_id) and (int)$request->selected_prod_schema_cycle_id > 0) ) {
                Log::channel('error')->error("Error inserting work: validation failed. Incorrect 'selected_prod_schema_cycle_id' input value: " . $request->selected_prod_schema_cycle_id . ".", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu przy walidacji danych.', 1);
            }
            if(!ProductionCycle::find($request->selected_prod_schema_cycle_id) instanceof ProductionCycle) {
                Log::channel('error')->error("Error inserting work: validation failed. 'selected_prod_schema_cycle_id' input value error: Production cycle with id = ".$request->selected_prod_schema_cycle_id." not found.", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie znaleziono cyklu dla wybranego zadania.', 1);
            }
        }
        $check_parameters = $request->only(preg_grep('/^check_/', $request->keys()));

        if(count($check_parameters) == 0) {
            throw new Exception('Aby dodać pracę wybierz wykonane podzadanie/pozdadania.', 2);
        }

        $time_array = array();
        $suffix_array = array();
        $employee_id_array = [];
        foreach ($check_parameters as $key => $value) {
            $suffix = substr($key, 5);
            $suffix_array[] = $suffix;
            $employee_id_task_array = array();
            $rule_array = [
                'start_time'.$suffix => ['required','date'],
                'end_time'.$suffix => ['required', 'date', 'after_or_equal:start_time'.$suffix],
                'work_duration'.$suffix => ['gt:0'],

            ];
            $reason_codes_array =
                ['start_time'.$suffix.'.required' => '- Początek pracy dla jednego z podzadań jest nieokreślony. Podaj początek pracy.',
                'end_time'.$suffix.'.required' => '- Zakończenie pracy dla jednego z podzadań jest nieokreślone. Podaj zakończenie pracy.',
                'start_time'.$suffix.'.date' => '- Jeden z wprowadzonych czasów startu nie jest prawidłowy. Spróbuj ponownie.',
                'end_time'.$suffix.'.date' => '- Jeden z wprowadzonych czasów zakończenia nie jest prawidłowy. Spróbuj ponownie.',
                'end_time'.$suffix.'.after_or_equal' => '- Rozpoczęcie pracy musi być wcześniej niż zakończenie pracy.',
                'work_duration'.$suffix.'.gt' => '- Czas pracy musi być większy od 0.',
            ];
            if($request->has('amount'.$suffix)) {
                $rule_array['amount'.$suffix] = ['required','gt:0'];
                $reason_codes_array['amount'.$suffix.'.required'] = '- Aby dodać pracę dla jednego z podzadań wymagane jest podanie ilości wykonanych sztuk.';
                $reason_codes_array['amount'.$suffix.'.gt'] = '- Ilość wykonanych sztuk musi być większa od 0.';
            }
            $employee_name = 'employee'.$suffix;
            if($request->has($employee_name)) {
                $employee_id_task_array[] = $request->$employee_name;
                $rule_array[$employee_name] = ['required','exists:App\Models\User,id'];
                $reason_codes_array[$employee_name.'.required'] = '- Nie podano pracownika, który wykonał zadanie.';
                $reason_codes_array[$employee_name.'.exists'] = '- Podanego pracownika nie znaleziono w systemie';
            }
            $defect_name = 'defect'.$suffix;
            if($request->has($defect_name) and !is_null($request->$defect_name)) {
                $rule_array[$defect_name] = ['integer','gt:0'];
                $reason_codes_array[$defect_name.'.integer'] = '- Liczba wyprodukowanych defektów musi być całkowita.';
                $reason_codes_array[$defect_name.'.gt'] = '- Liczba defektów musi być większa od 0. Jeśli nie wyprodukowano defektów pozostaw puste pole.';
                $rule_array['defect_rc'.$suffix] = ['required','exists:App\Models\ReasonCode,reason_code'];
                $reason_codes_array['defect_rc'.$suffix.'.required'] = '- Jeśli wyprodukowano defekty podaj kod błędu.';
                $reason_codes_array['defect_rc'.$suffix.'.exists'] = '- Podanego kodu błedu nie znaleziono w systemie.';
            }
            $waste_name = 'waste'.$suffix;
            if(!is_null($request->$waste_name)) {
                $rule_array[$waste_name] = ['gt:0'];
                $reason_codes_array[$waste_name.'.gt'] = '- Odpad musi być większy od 0. Jeśli nie wyprodukowano odpadu pozostaw puste pole.';
                $rule_array['waste_rc'.$suffix] = ['required','exists:App\Models\ReasonCode,reason_code'];
                $reason_codes_array['waste_rc'.$suffix.'.required'] = '- Jeśli wyprodukowano odpad podaj kod błędu.';
                $reason_codes_array['waste_rc'.$suffix.'.exists'] = '- Podanego kodu błedu nie znaleziono w systemie.';
                $rule_array['waste_unit'.$suffix] = ['required','exists:App\Models\Unit,unit'];
                $reason_codes_array['waste_unit'.$suffix.'.required'] = '- Jeśli wyprodukowano odpad podaj jednostkę.';
                $reason_codes_array['waste_unit'.$suffix.'.exists'] = '- Podanej jednostki nie znaleziono w systemie.';
            }
            $employee_count_name = 'employee_count'.$suffix;
            $employee_count = $request->$employee_count_name;
            if(intval($employee_count) <= 0) {
                Log::channel('error')->error("Error inserting work: validation failed. 'employee_count".$suffix."' input value error: ".$employee_count." is not proper integer value.", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu - nie znaleziono cyklu dla wybranego zadania.', 1);

            }

            //first employee is validated earlier
            if(intval($employee_count) > 1) {
                for($i = 2; $i <= intval($employee_count); $i++) {
                    $employee_name = $i.'_employee'.$suffix;
                    if($request->has($employee_name)) {
                        $employee_id_task_array[] = $request->$employee_name;
                        $rule_array[$employee_name] = ['required','exists:App\Models\User,id'];
                        $reason_codes_array[$employee_name.'.required'] = '- Nie podano pracownika, który wykonał zadanie. Żadna z dodanych list z nazwą pracownika nie może być pusta.';
                        $reason_codes_array[$employee_name.'.exists'] = '- Jednego z podanych pracowników nie znaleziono w systemie';
                    }
                    else {
                        throw new Exception('- Nie podano pracownika, który wykonał zadanie. Żadna z dodanych list z nazwą pracownika nie może być pusta.', 2);
                    }
                }
            }
            $request->validate( $rule_array, $reason_codes_array);


            if(!($employee_id_task_array === array_unique($employee_id_task_array))) {
                throw new Exception("- Dla jednego z podzadań wybrano więcej niż 1 raz tego samego pracownika w kolumnie 'Pracownicy'.", 2);
            }
            $start_time_input = 'start_time'.$suffix;
            $end_time_input = 'end_time'.$suffix;

            $time_array[] = [$request->$start_time_input, $request->$end_time_input];
            $employee_id_array[$suffix] = $employee_id_task_array;
        }

        $this->validateWorkTimeOverlap($time_array, $employee_id_array, $employee_no);

        return array('SUFFIX' =>$suffix_array, 'EMPLOYEEID' => $employee_id_array);
    }


    /**
     * @throws Exception
     */
    private function validateWorkTimeOverlap(array $time_array, array $employee_id_array, string $employee_no): void
    {
        if(count($time_array) != count($employee_id_array)) {
            Log::channel('error')->error('Error inserting work: validation failed in validateWorkTimeOverlap method. $time_array and $employee_id_array counts differ.', [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu przy walidacji czasów pracy', 1);
        }
        $employee_id_array = array_values($employee_id_array);

        if(count($time_array) > 0) {
            if(count(($time_array)) == 1) {
                $start = new DateTime($time_array[0][0]);
                $end = new DateTime($time_array[0][1]);
                $this->validateOtherWorks($start, $end, $employee_id_array[0]);
            }
            else {
                for ($i = 0; $i < count($time_array) - 1; $i++) {
                    $start1 = new DateTime($time_array[$i][0]);
                    $end1 = new DateTime($time_array[$i][1]);
                    $this->validateOtherWorks($start1, $end1, $employee_id_array[$i]);
                    for ($j = $i + 1; $j < count($time_array); $j++) {
                        $start2 = new DateTime($time_array[$j][0]);
                        $end2 = new DateTime($time_array[$j][1]);
                        if ($start1 < $end2 && $end1 > $start2) {
                            throw new Exception('Daty 2 lub więcej zadań nachodzą na siebie.', 2);
                        }
                    }
                }
            }

        }
    }

    /**
     * @throws Exception
     */
    private function validateOtherWorks($start, $end, $employee_id_array): void
    {
        $where_start1 = $start->format('Y-m-d H:i:s');
        $where_end1 = $end->format('Y-m-d H:i:s');
        $work_query = WorkUser::whereIn('user_id',$employee_id_array)
            ->join('work','work.id', '=', 'work_user.work_id');
        $work_query_start = clone $work_query;
        $work_query_start = $work_query_start->where('start_time','<',$where_start1)
            ->where('end_time', '>', $where_start1)->get();
        $work_query_end = clone $work_query;
        $work_query_end = $work_query_end->where('start_time','<',$where_end1)
            ->where('end_time', '>', $where_end1)->get();

        if(count($work_query_start) > 0){
            throw new Exception('- O godzinie '.$where_start1.' jeden z wybranych użytkowników wykonywał inną pracę.', 2);
        }
        else if (count($work_query_end) > 0) {
            throw new Exception('- O godzinie '.$where_end1.' jeden z wybranych użytkowników wykonywał inną pracę.', 2);
        }
    }
    private function insertProductionCycleUsers(array $employees, int $cycle_id, string $employee_no) : void
    {
        foreach ($employees as $id) {
            DB::table('production_cycle_user')->insert([
                'production_cycle_id' => $cycle_id,
                'user_id' => $id,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),]);
        }
    }

    private function filterWorkByEndTime(Request $request): array
    {
        $work_start_from = Carbon::parse($request->work_start_from);
        $work_start_to = Carbon::parse($request->work_start_to);

        if(is_null($request->work_start_from) or is_null($request->work_start_to) or $work_start_from->gt($work_start_to)) {
            if($work_start_from->gt($work_start_to)) {
                $status_err = 'Nie przefiltrowano Startu. Data "Start pracy od" nie może być później od daty "Start pracy do".';
            }
            else if(!(is_null($request->work_start_from) and is_null($request->work_start_to))) {
                $status_err = 'Nie przefiltrowano Startu. Aby filtrować po starcie podaj początek i koniec.';
            }
            $current_time = new DateTime();
            $end_time = $current_time->format('Y-m-d');
            $current_time->sub(new DateInterval('P7D'));
            $start_time = $current_time->format('Y-m-d');
            $filt_start_time = $start_time;
            $filt_end_time = $end_time;
            $works = WorkView::whereBetween('start_time', [$start_time." 00:00:00",$end_time." 23:59:59"]);
        }
        else {
            $filt_start_time = $request->work_start_from;
            $filt_end_time = $request->work_start_to;
            $works = WorkView::whereBetween('start_time', [$request->work_start_from." 00:00:00",$request->work_start_to." 23:59:59"]);
        }
        return array(
            'filt_end_time' => $filt_end_time,
            'filt_start_time' => $filt_start_time,
            'status_err' => isset($status_err)? $status_err : null,
            'works' => $works);
    }

    private function filterWorks(Request $request, Builder $works, array $where_clause): Builder
    {
        $where_in_clause = $where_clause['where_in'];
        $where_like_clause = $where_clause['where_like'];

        if(!empty($where_in_clause)) {
            foreach ($where_in_clause as $column => $in_clause) {
                $works = $works->whereIn($column, $in_clause);
            }
        }
        if(!empty($where_like_clause)) {
            foreach ($where_like_clause as $column => $like_clause) {
                if($column == 'employees') {
                    foreach ($like_clause as $employee){
                        $works = $works->where('exec_employees', 'like', '%'.$employee.'%');
                    }
                }
                else {
                    $works = $works->where($column, 'like', '%'.$like_clause.'%');
                }
            }
        }
        return  $works;
    }
    private function orderWorks(Builder $works, $order_table): Builder
    {
        $columns = explode(',',$order_table);
        foreach ($columns as $col) {
            $col_and_dir = explode(';',$col);
            if(count($col_and_dir) == 2) {
                $works = $works->orderBy($col_and_dir[0], $col_and_dir[1]);
            }
        }
        return $works;
    }
    private function createWhereClause($request): array
    {

        $where_in_clause = array();
        $where_like_clause = array();
        if(!is_null($request->product_name)) {
            $where_like_clause['product_name'] = $request->product_name;
        }
        if(!is_null($request->component_name)) {
            $where_like_clause['component_name'] = $request->component_name;
        }
        if(!is_null($request->production_schema)) {
            $where_like_clause['production_schema'] = $request->production_schema;
        }
        if(!is_null($request->task_name)) {
            $where_like_clause['task_name'] = $request->task_name;
        }
        if(!is_null($request->cycle_category)) {
            $where_in_clause['cycle_category'] = explode(',',$request->cycle_category);
        }
        if(!is_null($request->employees)) {
            $where_like_clause['employees'] = explode(',',$request->employees);
        }
        return array(
                'where_in' => $where_in_clause,
                'where_like' => $where_like_clause
            );
    }

    private function validateStoreCycle($request, bool $edit = false)
    {
        $keyword = $edit? 'edytować' : 'dodać';
        $request->validate([
            'id' => ['required'],
            'exp_start' => ['date', 'required'],
            'exp_end' => ['date','required','after:yesterday', 'after_or_equal:exp_start'],
            'pack_prod_id' => ['nullable', 'integer'],
            'comment' => ['max:255'],
            'amount' => ['required','integer', 'gt:0']
        ],
            [
                'id.required' => 'Aby '.$keyword.' cykl wybierz produkt/materiał/zadanie.',
                'amount.required' => 'Aby '.$keyword.' cykl należy podać ilość sztuk.',
                'amount.integer' => 'Ilość sztuk musi być liczbą całkowitą.',
                'amount.gt' => 'Ilość sztuk musi być liczbą większa od 0.',
                'exp_start.required' => 'Aby '.$keyword.' cykl ustaw jego przewidywany start.',
                'exp_start.date' => "Pole 'Start' powinno być datą w formacie 'yyyy-mm-dd'.",
                'exp_end.after' => "'Termin' musi być ustawiony w przyszłości.",
                'exp_end.after_or_equal' => "'Termin' musi być później niż 'Start'",
                'exp_end.required' => 'Aby '.$keyword.' cykl ustaw termin wykonania.',
                'exp_end.date' => "Pole 'Termin' powinno być datą w formacie 'yyyy-mm-dd'.",
                'comment.max' => 'Pole uwagi zawiera zbyt dużo znaków.',
            ]);
    }
}
