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
use App\Models\Work;
use App\Models\WorkView;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProductionCycleController extends Controller
{
    public function index(Request $request): View
    {
        //if is work cycle then work.work-cycle view is handled
        $is_work_cycle = $request->route()->uri == 'praca-w-cyklu';
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $users = User::select('id','employeeNo', 'role')->get();
        try {
            $filt_by_exp_end_table = $this->filterByExpectedEndTimeParentCycles($request);
            $parent_cycles = $filt_by_exp_end_table['parent_cycles'];
            $filt_start_time = $filt_by_exp_end_table['filt_start_time'];
            $filt_end_time = $filt_by_exp_end_table['filt_end_time'];
            $status_err = $filt_by_exp_end_table['status_err'];
            $where_clause = $this->createWhereClause($request);
            $parent_cycles = $this->filterParentCycles($request, $parent_cycles, $where_clause);
            $filt_items = array_merge($where_clause['where_in'], $where_clause['where_like']);
            $parent_cycles = $this->orderParentCycles($parent_cycles, $request->order);
            if($is_work_cycle) {
                $filtered_cycles_work = $this->filterParentCyclesByWork($parent_cycles);
                $parent_cycles = $filtered_cycles_work[0];
                $work_array = $filtered_cycles_work[1];
                $parent_cycles = $parent_cycles->paginate(2);
            }
            else {
                if(session('add_work')) {
                    $parent_cycles->where('finished',0);
                }
                $parent_cycles = $parent_cycles->paginate(2);
            }
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering parent cycles grid: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            if(isset($parent_cycles) and $parent_cycles instanceof Builder) {
                $parent_cycles = $is_work_cycle? $parent_cycles->paginate(2) : $parent_cycles->paginate(2);
            } else {
                $parent_cycles = $is_work_cycle? ParentCycleView::paginate(2) : ParentCycleView::paginate(2);
            }
            $status_err = 'Nie udało się przefiltrować - błąd systemu.';
            $filt_items = isset($filt_items)? $filt_items : null;
            $filt_start_time = isset($filt_start_time)? $filt_start_time : null;
            $filt_end_time = isset($filt_end_time)? $filt_end_time : null;
        }

        $order_items = is_string($request->order)? explode(',',$request->order) : null;
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
            'time_spent' => 'Czas pracy (h)',
            'expected_amount_per_time_frame' => 'Oczekiwana ilość (szt)',
        );

        $status_add_work = $this->getWorkStatus($request);
        $view = $is_work_cycle? 'work.work-cycle' : 'production.production';

        return view($view, [
            'parent_cycles' => $parent_cycles,
            'user' => $user,
            'users' => $users,
            'order' => $order_table,
            'status' => isset($status)? $status : null,
            'status_add_work' => $status_add_work,
            'status_err' => isset($status_err)? $status_err : null,
            'filt_start_time' => $filt_start_time,
            'filt_end_time' => $filt_end_time,
            'filt_items' => $filt_items,
            'order_items' => $order_items,
            'work_array' => isset($work_array)? $work_array : null,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function indexWrapper(): RedirectResponse
    {
        return redirect()->route('production.index');
    }

    public function cycleDetails(Request $request, $id): View|RedirectResponse
    {
        $user = Auth::user();
        $child_cycles = ChildCycleView::where('parent_id', $id)->paginate(10);
        $parent_cycle = ParentCycleView::where('cycle_id', $id)->first();
        if(count($child_cycles) == 0) {
            return back();
        }
        return view('production.cycle-details', [
            'p_cycle' => $parent_cycle,
            'child_cycles' => $child_cycles,
            'user' => $user,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function addCycleWrapper(Request $request): RedirectResponse
    {
        return redirect()->route('production.add-cycle',['category' => $request->category]);
    }

    public function addCycle(Request $request, $category): View
    {
        $user = Auth::user();
        $users = User::all();
        if($category == 2) {
            //get components with minutes per one pcs calculated
            $elements = Component::where('independent',1)
                ->join('production_standard', 'component.id', '=', 'production_standard.component_id')
                ->select('component.id', 'component.name', 'component.material', 'component.height', 'component.length',
                    'component.width', 'component.description', 'component.image',
                    DB::raw('truncate(60/(1/sum(duration_hours/amount)),2) as minutes_per_pcs'))
                ->groupBy('component.id', 'component.name', 'component.material', 'component.height', 'component.length',
                        'component.width','component.description', 'component.image');

            $category_name = 'Materiał';
            if(is_string($request->filter_elem)) {
                $elements = $elements->where('product.name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('product.material', 'like', '%'.$request->filter_elem.'%');
            }
            $elements = $elements->paginate(10);
        } else if($category == 3) {
            $pack_product_id = StaticValue::where('type', 'pack_schema')->select('value')->first();
            $pack_prod_show = (bool) $request->query('pak-prod');
            //get pack products with minutes per one pcs calculated
            $products = Product::select('product.id', 'product.name', 'product.image', 'product.material', DB::raw('truncate(60/(amount/duration_hours),2) as minutes_per_pcs'))
                ->join('production_standard', 'product.id', '=','production_standard.product_id' )
                ->orderBy('name')->paginate(1,['*'],'pak-prod');
            $category_name = 'Zadanie';

            $union = ProductionSchema::select('id','production_schema', 'description', DB::raw('null as minutes_per_pcs'))
                ->where('id', $pack_product_id->value);
            //get pack production_schemas with minutes per one pcs calculated, but with null for pack product production_schema in this column
            $elements = ProductionSchema::join('production_standard', 'production_schema.id', '=','production_standard.production_schema_id')
                ->where(['production_standard.component_id' => null,
                         'production_standard.product_id' => null])
                ->select('production_schema.id','production_schema.production_schema', 'production_schema.description', DB::raw('truncate(60/(amount/duration_hours),2) as minutes_per_pcs'))
                ->union($union);

            if(is_string($request->filter_elem)) {
                $elements = $elements->where('production_schema', 'like', '%' . $request->filter_elem . '%');
            }
            $elements = $elements->orderBy('id','asc')->paginate(2,['*'],'dodaj-cykl');
        } else {
            //get products with minutes per one pcs calculated
            $elements = Product::join('product_component', 'product.id', '=', 'product_component.product_id')
                ->join('production_standard', 'production_standard.component_id', '=', 'product_component.component_id')
                ->select('product.id','product.name', 'product.gtin', 'product.description', 'product.material',
                    'product.height', 'product.length', 'product.width', 'product.color', 'product.image',
                    'product.barcode_image', 'product.price', 'product.piecework_fee',
                    DB::raw('truncate(60/(1/sum(duration_hours/amount)),2) as minutes_per_pcs'))
                ->groupBy('product.id','product.name', 'product.gtin', 'product.description', 'product.material',
                    'product.height', 'product.length', 'product.width', 'product.color', 'product.image',
                    'product.barcode_image', 'product.price', 'product.piecework_fee');

            $category_name = 'Produkt';
            if(is_string($request->filter_elem)) {
                $elements = $elements->where('product.name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('product.material', 'like', '%'.$request->filter_elem.'%')
                                    ->paginate(10);
                ;
            } else {
                $elements = $elements->paginate(10);
            }
        }
        return view('production.cycle-add', [
            'user' => $user,
            'users' => $users,
            'category' => $category,
            'category_name' => $category_name,
            'elements' => $elements,
            'filter_elem' => $request->filter_elem,
            'products' => isset($products)? $products : null,
            'pack_product_id' => isset($pack_product_id )? $pack_product_id : null,
            'pack_prod_show' => isset($pack_prod_show)? $pack_prod_show : false,
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
            $employees_name = 'employees_2'.$id;
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
        $category = intval($request->category_2);
        $id = intval($request->id_2);
        $amount = intval($request->amount_2);
        $parent_id = null;
        if(!in_array($category, [1,2,3])) {
            Log::channel('error')->error('Error inserting cycle: incorrect $request->category_2 value: '.$request->category_2, [
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
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id_2 value: '.$request->id_2.'. Product with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla produktu. Błąd systemu.',1);
            }

            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 1,
                'product_id' => $id,
                'expected_start_time' => $request->exp_start_2.' 00:00:00',
                'expected_end_time' => $request->exp_end_2.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment_2,
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
                        'expected_start_time' => $request->exp_start_2.' 00:00:00',
                        'expected_end_time' => $request->exp_end_2.' 23:59:59',
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
                        'expected_start_time' => $request->exp_start_2.' 00:00:00',
                        'expected_end_time' => $request->exp_end_2.' 23:59:59',
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
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id_2 value: '.$request->id_2.'. Component with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla materiału. Błąd systemu.',1);
            }
            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 2,
                'component_id' => $id,
                'expected_start_time' => $request->exp_start_2.' 00:00:00',
                'expected_end_time' => $request->exp_end_2.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment_2,
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
                    'expected_start_time' => $request->exp_start_2.' 00:00:00',
                    'expected_end_time' => $request->exp_end_2.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment_2,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),]);
            }
        }
        else if($category == 3) {
            if(!is_null($request->pack_prod_id_2)) {
                if(count(ProductionCycle::where(['production_schema_id' => $id, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                    throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego zadania. Aby dodać nowy cykl zakończ poprzedni.',1);
                }
            } else {
                if(count(ProductionCycle::where(['production_schema_id' => $id, 'product_id' => $request->pack_prod_id_2, 'parent_id' => null, 'finished' => 0])->get()) > 0) {
                    throw new Exception('Nie można dodać nowego cyklu - istnieje rozpoczęty cykl dla tego zadania. Aby dodać nowy cykl zakończ poprzedni.',1);
                }
            }

            if(!ProductionSchema::find($id) instanceof ProductionSchema) {
                Log::channel('error')->error('Error inserting cycle: incorrect $request->id_2 value: '.$request->id_2.'. ProductionSchema with provided id not found.', [
                    'employeeNo' => $employee_no,
                ]);
                throw new Exception('Nie udało się dodać cyklu dla zadania. Błąd systemu.',1);
            }
            $pack_prod_id = Product::where('id', $request->pack_prod_id_2)->first() instanceof Product? $request->pack_prod_id_2 : null;
            $parent_id = DB::table('production_cycle')->insertGetId([
                'level' => 1,
                'category' => 3,
                'production_schema_id' => $id,
                'product_id' => $pack_prod_id,
                'total_amount' => $amount,
                'expected_start_time' => $request->exp_start_2.' 00:00:00',
                'expected_end_time' => $request->exp_end_2.' 23:59:59',
                'additional_comment' => $request->comment_2,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),]);

        }
        return $parent_id;
    }


    private function getWorkStatus(Request $request) : string|null
    {
        $previous = $request->session()->previousUrl();
        if($previous == str_contains($previous,'praca-raportuj') || str_contains($previous,'produkcja') || str_contains($previous,'dodaj-prace')) {
            return (session('add_work'))? 'Aby zaraportować pracę wybierz cykl produkcji (możesz też dodać nowy).' : null;
        }
        else {
            session()->forget('add_work');
        }
        return null;
    }
    /**
     * @throws Exception
     */
    private function updateProductionCycle($request, string $employee_no) : void
    {
        $category = intval($request->category_2);
        $id = intval($request->id_2);
        $amount = intval($request->amount_2);

        if(!in_array($category, [1,2,3])) {
            Log::channel('error')->error('Error updating cycle: incorrect $request->category_2 value: '.$request->category_2, [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się edytować cyklu. Błąd systemu przy ustalaniu kategorii.',1);
        }
        if (!ProductionCycle::find($id) instanceof ProductionCycle) {
            Log::channel('error')->error('Error updating cycle: incorrect $request->id_2 value: ' . $request->id_2 . '. ProductionCycle with provided id not found.', [
                'employeeNo' => $employee_no,
            ]);
            throw new Exception('Nie udało się edytować cyklu. Błąd systemu.', 1);
        }

        //product cycle update
        if($category == 1) {
            $amount_coef = ProductionCycle::where('id',$id)->select('total_amount')->first();
            $amount_coef = $amount_coef instanceof ProductionCycle? $amount / $amount_coef->total_amount : 1;
            DB::table('production_cycle')->where('id', $id)->update([
                    'expected_start_time' => $request->exp_start_2.' 00:00:00',
                    'expected_end_time' => $request->exp_end_2.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment_2,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]

            );
            $cycles_2 = ProductionCycle::where('parent_id', $id)->select('id', 'total_amount')->get();

            foreach ($cycles_2 as $cycle) {
                DB::table('production_cycle')->where('id', $cycle->id)->update([
                        'expected_start_time' => $request->exp_start_2.' 00:00:00',
                        'expected_end_time' => $request->exp_end_2.' 23:59:59',
                        'total_amount' => $amount_coef * $cycle->total_amount,
                        'updated_by' => $employee_no,
                        'updated_at' => date('y-m-d h:i:s'),]

                );
                DB::table('production_cycle')->where('parent_id', $cycle->id)->update([
                    'expected_start_time' => $request->exp_start_2.' 00:00:00',
                    'expected_end_time' => $request->exp_end_2.' 23:59:59',
                    'total_amount' => $amount_coef * $cycle->total_amount,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]);
            }
        }
        //component cycle update
        else if($category == 2) {
            DB::table('production_cycle')->where('id', $id)->update([
                'expected_start_time' => $request->exp_start_2.' 00:00:00',
                'expected_end_time' => $request->exp_end_2.' 23:59:59',
                'total_amount' => $amount,
                'additional_comment' => $request->comment_2,
                'updated_by' => $employee_no,
                'updated_at' => date('y-m-d h:i:s'),]);

            DB::table('production_cycle')->where('parent_id', $id)->update([
                    'expected_start_time' => $request->exp_start_2.' 00:00:00',
                    'expected_end_time' => $request->exp_end_2.' 23:59:59',
                    'total_amount' => $amount,
                    'additional_comment' => $request->comment_2,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),]);
        }
        //production schema update
        else if($category == 3) {

            DB::table('production_cycle')->where('id', $id)->update([
                'total_amount' => $amount,
                'expected_start_time' => $request->exp_start_2 . ' 00:00:00',
                'expected_end_time' => $request->exp_end_2 . ' 23:59:59',
                'additional_comment' => $request->comment_2,
                'updated_by' => $employee_no,
                'updated_at' => date('y-m-d h:i:s'),]);
        }
    }
    private function filterByExpectedEndTimeParentCycles(Request $request): array
    {
        $exp_start = Carbon::parse($request->exp_start);
        $exp_end = Carbon::parse($request->exp_end);


        if(is_null($request->exp_start) or is_null($request->exp_end) or $exp_start->gt($exp_end)) {
            if($exp_start->gt($exp_end)) {
                $status_err = 'Nie przefiltrowano terminu. Data "Termin od" nie może być później od daty "Termin do".';
            }
            else if(!(is_null($request->exp_start) and is_null($request->exp_end))) {
                $status_err = 'Nie przefiltrowano terminu. Aby filtrować po terminie podaj początek i koniec.';
            }
            $current_time = new DateTime();
            $current_time->add(new DateInterval('P14D'));
            $end_time = $current_time->format('Y-m-d');
            $current_time->sub(new DateInterval('P28D'));
            $start_time = $current_time->format('Y-m-d');
            $filt_start_time = $start_time;
            $filt_end_time = $end_time;
            $parent_cycles = ParentCycleView::whereBetween('expected_end_time', [$start_time." 00:00:00",$end_time." 23:59:59"]);
        }
        else {
            $filt_start_time = $request->exp_start;
            $filt_end_time = $request->exp_end;
            $parent_cycles = ParentCycleView::whereBetween('expected_end_time', [$filt_start_time." 00:00:00",$filt_end_time." 23:59:59"]);
        }
        return array(
            'filt_end_time' => $filt_end_time,
            'filt_start_time' => $filt_start_time,
            'status_err' => isset($status_err)? $status_err : null,
            'parent_cycles' => $parent_cycles);
    }

    private function filterParentCycles(Request $request, Builder $parent_cycles, array $where_clause): Builder
    {
        $where_in_clause = $where_clause['where_in'];
        $where_like_clause = $where_clause['where_like'];


        if(!empty($where_in_clause)) {
            foreach ($where_in_clause as $column => $in_clause) {
                $parent_cycles = $parent_cycles->whereIn($column, $in_clause);
            }
        }
        if(!empty($where_like_clause)) {
            if(array_key_exists('name', $where_like_clause)) {
                $parent_cycles = $parent_cycles->where('name', 'like', '%'.$where_like_clause['name'].'%');
            }
            if(array_key_exists('employees', $where_like_clause)) {
                foreach ($where_like_clause['employees'] as $employee){
                    $parent_cycles = $parent_cycles->where('assigned_employees', 'like', '%'.$employee.'%');
                }
            }
        }
        return  $parent_cycles;
    }

    private function filterParentCyclesByWork(Builder $parent_cycles): array
    {
        $work_cycles_id = Work::select('production_cycle_id')->distinct()->get();
        $work_cycles_id = collect($work_cycles_id)->map(function (Work $arr) { return $arr->production_cycle_id; })->toArray();
        $parent_cycles = $parent_cycles->whereIn('cycle_id', $work_cycles_id);
        $work_array = array();
        foreach ($parent_cycles->get() as $p_cycle) {
            $works = WorkView::where('cycle_id', $p_cycle->cycle_id)->orderBy('end_time', 'asc')->get();
            $work_array[$p_cycle->cycle_id] = $works;
        }
        return array($parent_cycles,$work_array);
    }
    private function orderParentCycles(Builder $parent_cycles, $order_table): Builder
    {
        $columns = explode(',',$order_table);
        foreach ($columns as $col) {
            $col_and_dir = explode(';',$col);
            if(count($col_and_dir) == 2) {
                $parent_cycles = $parent_cycles->orderBy($col_and_dir[0], $col_and_dir[1]);
            }
        }
        return $parent_cycles;
    }
    private function createWhereClause($request): array
    {
        $where_in_clause = array();
        $where_like_clause = array();
        if(!is_null($request->name)) {
            $where_like_clause['name'] = $request->name;
        }
        if(!is_null($request->status)) {
            $where_in_clause['status'] = explode(',',$request->status);
        }
        if(!is_null($request->category)) {
            $where_in_clause['category'] = explode(',',$request->category);
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
            'id_2' => ['required'],
            'exp_start_2' => ['date', 'required'],
            'exp_end_2' => ['date','required','after:yesterday', 'after_or_equal:exp_start'],
            'pack_prod_id_2' => ['nullable', 'integer'],
            'comment_2' => ['max:255'],
            'amount_2' => ['required','integer', 'gt:0']
        ],
            [
                'id_2.required' => 'Aby '.$keyword.' cykl wybierz produkt/materiał/zadanie.',
                'amount_2.required' => 'Aby '.$keyword.' cykl należy podać ilość sztuk.',
                'amount_2.integer' => 'Ilość sztuk musi być liczbą całkowitą.',
                'amount_2.gt' => 'Ilość sztuk musi być liczbą większa od 0.',
                'exp_start_2.required' => 'Aby '.$keyword.' cykl ustaw jego przewidywany start.',
                'exp_start_2.date' => "Pole 'Start' powinno być datą w formacie 'yyyy-mm-dd'.",
                'exp_end_2.after' => "'Termin' musi być ustawiony w przyszłości.",
                'exp_end_2.after_or_equal' => "'Termin' musi być później niż 'Start'",
                'exp_end_2.required' => 'Aby '.$keyword.' cykl ustaw termin wykonania.',
                'exp_end_2.date' => "Pole 'Termin' powinno być datą w formacie 'yyyy-mm-dd'.",
                'comment_2.max' => 'Pole uwagi zawiera zbyt dużo znaków.',
            ]);
    }
}
