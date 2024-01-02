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
use App\Models\Unit;
use App\Models\User;
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

        return view('work.work-add', [
            'p_cycle' => $parent_cycle,
            'child_cycles' => $child_cycles,
            'child_components' => $child_components,
            'child_prod_schemas' => $child_prod_schemas,
            'modal_data' => $modal_data,
            'user' => $user,
            'users' => $users,
            'reason_codes' => $reason_codes,
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
            $this->validateWork($request, $employee_no);
        }
        catch (Exception $e) {
            if($e instanceof ValidationException) {
                return back()->with('validation_err', $e->validator->getMessageBag()->all())->withInput();
            }
            else if ($e->getCode() == 2) {
                return back()->with('validation_err', [$e->getMessage()])->withInput();
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

        $category = intval($request->selected_cycle_category);
        if($category == 1) {

        }
        else if($category == 2) {

        }
        else if($category == 3) {

        }
        return back();
    }

    /**
     * @throws Exception
     */
    private function validateWork(Request $request,string $employee_no): void
    {
        if(is_null($request->selected_cycle_category) or !in_array($request->selected_cycle_category,['1','2','3'])) {
            Log::channel('error')->error("Error inserting work: validation failed. Incorrect 'selected_cycle_category' input value: ".$request->selected_cycle_category.".", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać pracy. Błąd systemu.', 1);
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
                throw new Exception('Nie udało się dodać pracy. Błąd systemu.', 1);
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
        foreach ($check_parameters as $key => $value) {
            if($value != 'on') {
                Log::channel('error')->error("Error inserting work: validation failed. Incorrect '".$key."' checkbox input value: ".$value.".", [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać pracy. Błąd systemu.', 1);
            }
            $suffix = substr($key, 5);
            $rule_array = [
                'start_time'.$suffix => ['required','date'],
                'end_time'.$suffix => ['required', 'date', 'after_or_equal:start_time'.$suffix],
                'work_duration'.$suffix => ['gt:0'],

            ];
            $reason_codes_array =
                ['start_time'.$suffix.'.required' => 'Początek pracy dla jednego z podzadań jest nieokreślony. Podaj początek pracy.',
                'end_time'.$suffix.'.required' => 'Zakończenie pracy dla jednego z podzadań jest nieokreślone. Podaj zakończenie pracy.',
                'start_time'.$suffix.'.date' => 'Jeden z wprowadzonych czasów startu nie jest prawidłowy. Spróbuj ponownie.',
                'end_time'.$suffix.'.date' => 'Jeden z wprowadzonych czasów zakończenia nie jest prawidłowy. Spróbuj ponownie.',
                'end_time'.$suffix.'.after_or_equal' => 'Rozpoczęcie pracy musi być wcześniej niż zakończenie pracy.',
                'work_duration'.$suffix.'.gt' => 'Czas pracy musi być większy od 0.',
            ];
            if($request->has('amount'.$suffix)) {
                $rule_array['amount'.$suffix] = ['required','gt:0'];
                $reason_codes_array['amount'.$suffix.'.required'] = 'Aby dodać pracę dla jednego z podzadań wymagane jest podanie ilości wykonanych sztuk.';
                $reason_codes_array['amount'.$suffix.'.gt'] = 'Ilość wykonanych sztuk musi być większa od 0.';
            }
            if($request->has('employee'.$suffix)) {
                $rule_array['employee'.$suffix] = ['required','exists:App\Models\User,id'];
                $reason_codes_array['employee'.$suffix.'.required'] = 'Nie podano pracownika, który wykonał zadanie.';
                $reason_codes_array['employee'.$suffix.'.exists'] = 'Podanego pracownika nie znaleziono w systemie';
            }
            $request->validate( $rule_array, $reason_codes_array);

            $start_time_input = 'start_time'.$suffix;
            $end_time_input = 'end_time'.$suffix;

            $time_array[] = [$request->$start_time_input, $request->$end_time_input];
        }
        $this->validateWorkTimeOverlap($time_array);
    }


    /**
     * @throws Exception
     */
    private function validateWorkTimeOverlap($time_array)
    {
        if(count($time_array) > 0) {
            for ($i = 0; $i < count($time_array) - 1; $i++) {
                $start1 = new DateTime($time_array[$i][0]);
                $end1 = new DateTime($time_array[$i][1]);

                for ($j = $i + 1; $j < count($time_array); $j++) {
                    $start2 = new DateTime($time_array[$j][0]);
                    $end2 = new DateTime($time_array[$j][1]);

                    if ($start1 < $end2 && $end1 > $start2) {
                        throw new Exception('Daty 2 lub więcej zadań nachodzą na siebie.', 2);
                    }
                }
            }
        }

        function hasOverlap($datePeriods): bool
        {
            $count = count($datePeriods);

            for ($i = 0; $i < $count - 1; $i++) {
                $start1 = new DateTime($datePeriods[$i][0]);
                $end1 = new DateTime($datePeriods[$i][1]);

                for ($j = $i + 1; $j < $count; $j++) {
                    $start2 = new DateTime($datePeriods[$j][0]);
                    $end2 = new DateTime($datePeriods[$j][1]);

                    if ($start1 <= $end2 && $end1 >= $start2) {
                        return true;
                    }
                }
            }
            return false;
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

    private function deleteProductionCycle(int $category, int $id): void
    {
        //remove product cycle
        if($category == 1) {
            $cycles_2 = ProductionCycle::where('parent_id', $id)->select('id')->get();
            foreach ($cycles_2 as $cycle) {
                ProductionCycle::where('parent_id', $cycle->id)->delete();
            }
            ProductionCycle::where('parent_id', $id)->delete();
            ProductionCycle::where('id', $id)->delete();
        }
        //remove component cycle
        else if($category == 2) {
            ProductionCycle::where('parent_id', $id)->delete();
            ProductionCycle::where('id', $id)->delete();
        }
        //remove prod_schema cycle
        else {
            ProductionCycle::where('id', $id)->delete();
        }
    }
    private function updateProductionCycleUsers(array $employees, int $cycle_id, string $employee_no) : void
    {
        if(empty($employees)) {
            ProductionCycleUser::where('production_cycle_id', $cycle_id)->delete();
        }
        else {
            $cycle_users = ProductionCycleUser::where('production_cycle_id', $cycle_id)->select('production_cycle_id', 'user_id')->get();
            if(count($cycle_users) == 0) {
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
            else {
                foreach ($cycle_users as $user) {
                    //if user isn't in employees input, remove it, and remove its id from input array
                    if(!in_array($user->user_id,$employees)) {
                        ProductionCycleUser::where(['production_cycle_id' => $cycle_id,
                                                    'user_id' => $user->user_id])->delete();
                    } else {
                        array_splice($employees,array_search($user->user_id, $employees),1);
                    }

                }
                //insert new row for not spliced ids
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

        }
    }

    private function validateEmployees(string|null $employees, string $employee_no, bool $edit = false) : array
    {
        if(is_null($employees)) {
            return [];
        }
        $keyword1 = $edit? 'updating' : 'inserting';
        $keyword2 = $edit? 'edytować' : 'dodać';
        $employees_tab = explode(',',$employees);
        if(count(User::whereIn('id', $employees_tab)->get()) != count($employees_tab)) {
            Log::channel('error')->error("Error '.$keyword1.' cycle: incorrect 'employees' input value: ".$employees.".", [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się '.$keyword2.' cyklu. Błąd systemu.', 1);
        }
        return $employees_tab;
    }

    /**
     * @throws Exception
     */
    private function insertProductionCycle($request, string $employee_no) : int
    {
        $category = intval($request->category);
        $id = intval($request->id);
        $amount = intval($request->amount);
        $parent_id = null;
        if(!in_array($category, [1,2,3])) {
            Log::channel('error')->error('Error inserting cycle: incorrect $request->category value: '.$request->category, [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się dodać cyklu. Błąd systemu przy ustalaniu kategorii.',1);
        }
            //product
        if($category == 1) {
            if(count(ProductionCycle::where(['product_id' => $id, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego produktu. Aby dodać nowy cykl zakończ poprzedni.',1);
            }
            if(!Product::find($id) instanceof Product) {
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id value: '.$request->id.'. Product with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla produktu. Błąd systemu.',1);
            }

            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 1,
                'product_id' => $id,
                'expected_start_time' => $request->exp_start.' 00:00:00',
                'expected_end_time' => $request->exp_end.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),]

            );
            $components = ProductComponent::where('product_id', $id)->select('component_id', 'amount_per_product')->get();

            foreach ($components as $comp) {
                $parent_id2 = DB::table('production_cycle')->insertGetId([
                        'level' => 2,
                        'category' => 2,
                        'component_id' => $comp->component_id,
                        'parent_id' => $parent_id,
                        'expected_start_time' => $request->exp_start.' 00:00:00',
                        'expected_end_time' => $request->exp_end.' 23:59:59',
                        'total_amount' => $amount * $comp->amount_per_product,
                        'created_by' => $employee_no,
                        'updated_by' => $employee_no,
                        'created_at' => date('y-m-d h:i:s'),
                        'updated_at' => date('y-m-d h:i:s'),]

                );

                $comp_prod_schemas = ComponentProductionSchema::where('component_id', $comp->component_id)->select('production_schema_id','sequence_no')->get();
                foreach ($comp_prod_schemas as $prod_schema) {
                    DB::table('production_cycle')->insert([
                        'level' => 3,
                        'category' => 3,
                        'production_schema_id' => $prod_schema->production_schema_id,
                        'parent_id' => $parent_id2,
                        'sequence_no' => $prod_schema->sequence_no,
                        'expected_start_time' => $request->exp_start.' 00:00:00',
                        'expected_end_time' => $request->exp_end.' 23:59:59',
                        'total_amount' => $amount * $comp->amount_per_product,
                        'created_by' => $employee_no,
                        'updated_by' => $employee_no,
                        'created_at' => date('y-m-d h:i:s'),
                        'updated_at' => date('y-m-d h:i:s'),]);
                }
            }

        }
        else if($category == 2) {
            if(count(ProductionCycle::where(['component_id' => $id, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego materiału. Aby dodać nowy cykl zakończ poprzedni.',1);
            }
            if(!Component::find($id) instanceof Component) {
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id value: '.$request->id.'. Component with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla materiału. Błąd systemu.',1);
            }
            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 2,
                'component_id' => $id,
                'expected_start_time' => $request->exp_start.' 00:00:00',
                'expected_end_time' => $request->exp_end.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),]);

            $comp_prod_schemas = ComponentProductionSchema::where('component_id', $id)->select('production_schema_id','sequence_no')->get();
            foreach ($comp_prod_schemas as $prod_schema) {
                DB::table('production_cycle')->insert([
                    'level' => 2,
                    'category' => 3,
                    'component_id' => $prod_schema->production_schema_id,
                    'parent_id' => $parent_id,
                    'sequence_no' => $prod_schema->sequence_no,
                    'expected_start_time' => $request->exp_start.' 00:00:00',
                    'expected_end_time' => $request->exp_end.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),]);
            }
        }
        else if($category == 3) {
            if(!is_null($request->pack_prod_id)) {
                if(count(ProductionCycle::where(['production_schema_id' => $id, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                    throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego zadania. Aby dodać nowy cykl zakończ poprzedni.',1);
                }
            } else {
                if(count(ProductionCycle::where(['production_schema_id' => $id, 'product_id' => $request->pack_prod_id, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                    throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego zadania. Aby dodać nowy cykl zakończ poprzedni.',1);
                }
            }

            if(!ProductionSchema::find($id) instanceof ProductionSchema) {
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id value: '.$request->id.'. ProductionSchema with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla zadania. Błąd systemu.',1);
            }
            $pack_prod_id = Product::where('id', $request->pack_prod_id) instanceof Product? $request->pack_prod_id : null;
            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 3,
                'production_schema_id' => $id,
                'product_id' => $pack_prod_id,
                'total_amount' => $amount,
                'expected_start_time' => $request->exp_start.' 00:00:00',
                'expected_end_time' => $request->exp_end.' 23:59:59',
                'additional_comment' => $request->comment,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),]);

        }
        return $parent_id;
    }

    /**
     * @throws Exception
     */
    private function updateProductionCycle($request, string $employee_no) : void
    {
        $category = intval($request->category);
        $id = intval($request->id);
        $amount = intval($request->amount);

        if(!in_array($category, [1,2,3])) {
            Log::channel('error')->error('Error updating cycle: incorrect $request->category value: '.$request->category, [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się edytować cyklu. Błąd systemu przy ustalaniu kategorii.',1);
        }
        if (!ProductionCycle::find($id) instanceof ProductionCycle) {
            Log::channel('error')->error('Error updating cycle: incorrect $request->id value: ' . $request->id . '. ProductionCycle with provided id not found.', [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się edytować cyklu. Błąd systemu.', 1);
        }

        //product cycle update
        if($category == 1) {
            $amount_coef = ProductionCycle::where('id',$id)->select('total_amount')->first();
            $amount_coef = $amount_coef instanceof ProductionCycle? $amount / $amount_coef->total_amount : 1;
            DB::table('production_cycle')->where('id', $id)->update([
                    'expected_start_time' => $request->exp_start.' 00:00:00',
                    'expected_end_time' => $request->exp_end.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]

            );
            $cycles_2 = ProductionCycle::where('parent_id', $id)->select('id', 'total_amount')->get();

            foreach ($cycles_2 as $cycle) {
                DB::table('production_cycle')->where('id', $cycle->id)->update([
                        'expected_start_time' => $request->exp_start.' 00:00:00',
                        'expected_end_time' => $request->exp_end.' 23:59:59',
                        'total_amount' => $amount_coef * $cycle->total_amount,
                        'updated_by' => $employee_no,
                        'updated_at' => date('y-m-d h:i:s'),]

                );
                DB::table('production_cycle')->where('parent_id', $cycle->id)->update([
                    'expected_start_time' => $request->exp_start.' 00:00:00',
                    'expected_end_time' => $request->exp_end.' 23:59:59',
                    'total_amount' => $amount_coef * $cycle->total_amount,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]);
            }
        }
        //component cycle update
        else if($category == 2) {
            DB::table('production_cycle')->where('id', $id)->update([
                'expected_start_time' => $request->exp_start.' 00:00:00',
                'expected_end_time' => $request->exp_end.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment,
                'updated_by' => $employee_no,
                'updated_at' => date('y-m-d h:i:s'),]);

            DB::table('production_cycle')->where('parent_id', $id)->update([
                    'expected_start_time' => $request->exp_start.' 00:00:00',
                    'expected_end_time' => $request->exp_end.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]);
        }
        //production schema update
        else if($category == 3) {

            DB::table('production_cycle')->where('id', $id)->update([
                'total_amount' => $amount,
                'expected_start_time' => $request->exp_start . ' 00:00:00',
                'expected_end_time' => $request->exp_end . ' 23:59:59',
                'additional_comment' => $request->comment,
                'updated_by' => $employee_no,
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
