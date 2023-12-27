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
use App\Models\StaticValue;
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

        $child_cycles = ChildCycleView::where('parent_id', $id)->paginate(10);

        $child_components = null;
        $child_prod_schemas = null;
        if($parent_cycle->category == 1) {
            $child_components = ChildCycleView::where(['child_cycle_view.parent_id' => $id,
                'child_cycle_view.prod_schema_id' => null])->get();
            $child_prod_schemas = array();
            $child_schemas = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->join('task','task.production_schema_id', '=', 'child_cycle_view.prod_schema_id')
                ->select('child_cycle_view.*',DB::raw('task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('child_cycle_view.component_id', 'asc','child_cycle_view.prod_schema_sequence_no','asc','task.sequence_no','asc');

            foreach ($child_components as $comp) {
                $temp_schemas = clone $child_schemas;
                $child_prod_schemas[$comp->component_id] = $temp_schemas->where('component_id', $comp->component_id)->get();
            }

        }
        else if($parent_cycle->category == 2) {
            $child_prod_schemas = ChildCycleView::where(['child_cycle_view.parent_id' => $id])
                ->join('task','task.production_schema_id', '=', 'child_cycle_view.prod_schema_id')
                ->select('child_cycle_view.*',DB::raw('task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('child_cycle_view.prod_schema_sequence_no','asc','task.sequence_no','asc')->get();
        }
        else if($parent_cycle->category == 3) {
            $child_prod_schemas = ParentCycleView::where('cycle_id', $id)
                ->join('production_cycle','production_cycle.id', '=', 'parent_cycle_view.cycle_id')
                ->join('task','task.production_schema_id', '=', 'production_cycle.production_schema_id')
                ->select('parent_cycle_view.*',DB::raw('task.name as task_name, task.sequence_no as task_sequence_no, task.amount_required as task_amount_required'))
                ->orderBy('task.sequence_no','asc')->get();
        }

        if(count($child_prod_schemas) == 0) {
            return back();
        }

        return view('work.work-add', [
            'p_cycle' => $parent_cycle,
            'child_cycles' => $child_cycles,
            'child_components' => $child_components,
            'child_prod_schemas' => $child_prod_schemas,
            'user' => $user,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function storeCycle(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        $this->validateStoreCycle($request);
        try{
            DB::beginTransaction();
            $employees = $this->validateEmployees($request->employees, $employee_no);

            $cycle_id = $this->insertProductionCycle($request, $employee_no);

            $this->insertProductionCycleUsers($employees, $cycle_id, $employee_no);



//            $cycle = ProductionCycle::where('id', $cycle_id)->first();
//            $user_list = ProductionCycleUser::where('production_cycle_id', $cycle_id)
//                ->join('users', 'users.id', '=', 'production_cycle_user.user_id')
//                ->select('users.firstName', 'users.lastName', 'users.employeeNo','users.email');
//
//            foreach ($user_list as $user) {
//                Mail::to($user->email)->send(new CycleCreated($cycle, $user));
//            }

            DB::commit();


        }
        catch(Exception $e) {
            DB::rollBack();
            if($e->getCode() == '1') {
                return back()->with('status_err', $e->getMessage())->withInput();
            }
            Log::channel('error')->error('Error inserting cycle: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status_err', 'Nie udało się dodać cyklu. Błąd systemu.')->withInput();
        }

        return redirect()->route('production.index')->with('status', 'Dodano cykl produkcji.');
    }

    public function storeUpdatedCycle(Request $request, $id): RedirectResponse
    {
        $cycle_status = ProductionCycle::where(['id' => $id, 'start_time' => null])->first();
        if(!$cycle_status instanceof ProductionCycle) {
            return back()->with('status_err', 'Nie można edytować cyklu. Cykl rozpoczęty.')->withInput();
        }
        try {
            $this->validateStoreCycle($request, true);
        } catch(Exception $e) {
            $messages = isset($e->validator)? $e->validator->messages()->all() : null;
            return back()->with(['status_err' => 'Nie udało się edytować cyklu: podano błędne wartości. Spróbuj ponownie.',
                                 'edit_err' => $messages])
                         ->withInput();
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        try {
            $employees_name = 'employees'.$id;
            $employees = $request->$employees_name;
            $employees = $this->validateEmployees($employees, $employee_no, true);

            DB::beginTransaction();
            $this->updateProductionCycle($request, $employee_no);
            $this->updateProductionCycleUsers($employees, $id, $employee_no);

            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            if($e->getCode() == '1') {
                return back()->with('status_err', $e->getMessage())->withInput();
            }
            Log::channel('error')->error('Error updating cycle: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status_err', 'Nie udało się edytować cyklu. Błąd systemu.')->withInput();
        }
        return redirect()->route('production.index')->with('status', 'Edytowano cykl produkcji.');
    }

    public function destroyCycle(Request $request, $id): RedirectResponse
    {
        $p_cycle = ProductionCycle::find($id);
        if(!$p_cycle instanceof ProductionCycle) {
            return back()->with('status_err', 'Nie można usunąć cyklu. Nie odnaleziono cyklu w systemie.');
        }
        $cycle_status = ProductionCycle::where(['id' => $id, 'start_time' => null])->first();
        if(!$cycle_status instanceof ProductionCycle) {
            return back()->with('status_err', 'Nie można usunąć cyklu. Cykl rozpoczęty.');
        }

        try {
            $request->validate([
                'confirmation' => ['regex:(usuń|usun)'],
            ],
                [
                    'confirmation.regex' => 'Nie można usunąć produktu: niepoprawna wartość. Wpisz "usuń".',
                ]);
        }
        catch (Exception $e) {
            return redirect()->back()->with('status_err', $e->getMessage());
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        try {
            DB::beginTransaction();
            ProductionCycleUser::where('production_cycle_id', $id)->delete();
            $this->deleteProductionCycle($p_cycle->category, $id);
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::channel('error')->error('Error deleting cycle: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status_err', 'Nie udało się usunąć cyklu. Błąd systemu.')->withInput();
        }

        return redirect()->route('production.index')->with('status', 'Usunięto cykl produkcji.');
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
