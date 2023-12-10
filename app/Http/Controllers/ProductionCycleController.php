<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChildCycleView;
use App\Models\Component;
use App\Models\ParentCycleView;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductionCycle;
use App\Models\ProductionSchema;
use App\Models\StaticValue;
use App\Models\User;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
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
            $parent_cycles = $parent_cycles->paginate(10);
        } catch(Exception $e) {
            Log::channel('error')->error('Error filtering parent cycles grid: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            if(isset($parent_cycles) and $parent_cycles instanceof Builder) {
                $parent_cycles = $parent_cycles->paginate(10);
            } else {
                $parent_cycles = ParentCycleView::paginate(10);
            }
            $status_err = 'Nie udało się przefiltrować - błąd systemu.';
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


        return view('production.production', [
            'parent_cycles' => $parent_cycles,
            'user' => $user,
            'users' => $users,
            'order' => $order_table,
            'status' => isset($status)? $status : null,
            'status_err' => isset($status_err)? $status_err : null,
            'filt_start_time' => $filt_start_time,
            'filt_end_time' => $filt_end_time,
            'filt_items' => $filt_items,
            'order_items' => $order_items,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function cycleDetails(Request $request, $id): View
    {
        $user = Auth::user();

        $child_cycles = ChildCycleView::where('parent_id', $id)->get();
        $parent_cycle = ParentCycleView::where('cycle_id', $id)->first();
        return view('production.cycle-details', [
            'p_cycle' => $parent_cycle,
            'child_cycles' => $child_cycles,
            'user' => $user,
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
    }

    public function cycleAddWrapper(Request $request): RedirectResponse
    {
        return redirect()->route('production.add-cycle',['category' => $request->category]);
    }

    public function cycleAdd(Request $request, $category): View
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
                $elements = $elements->where('name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('material', 'like', '%'.$request->filter_elem.'%');
            }
            $elements = $elements->paginate(10);
        } else if($category == 3) {
            $pack_product_id = StaticValue::where('type', 'pack_schema')->select('value')->first();
            $pack_prod_show = (bool) $request->query('pak-prod');

            $products = Product::select('product.id', 'product.name', 'product.image', 'product.material', DB::raw('truncate(60/(amount/duration_hours),2) as minutes_per_pcs'))
                ->join('production_standard', 'product.id', '=','production_standard.product_id' )
                ->orderBy('name')->paginate(2,['*'],'pak-prod');
            $category_name = 'Zadanie';
            $union = ProductionSchema::select('id','production_schema', 'description', DB::raw('null as minutes_per_pcs'))
                ->where('id', $pack_product_id->value);
            $elements = ProductionSchema::join('production_standard', 'production_schema.id', '=','production_standard.production_schema_id')
                ->where(['production_standard.component_id' => null,
                         'production_standard.product_id' => null])
                ->select('production_schema.id','production_schema.production_schema', 'production_schema.description', DB::raw('truncate(60/(amount/duration_hours),2) as minutes_per_pcs'))
                ->union($union);
            if(is_string($request->filter_elem)) {
                $elements = $elements->where('production_schema', 'like', '%' . $request->filter_elem . '%');
            }
            $elements = $elements->paginate(10,['*'],'dodaj-cykl');
        } else {
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
                $elements = $elements->where('name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('material', 'like', '%'.$request->filter_elem.'%')
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

    public function cycleStore(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        $request->validate([
            'id' => ['required'],
            'exp_start' => ['date', 'required'],
            'exp_end' => ['date','required','after:yesterday', 'after_or_equal:exp_start'],
            'pack_prod_id' => ['nullable', 'integer'],
            'comment' => ['max:255'],
        ],
            [
                'id.required' => 'Aby dodać cykl wybierz produkt/materiał/zadanie.',
                'exp_start.required' => 'Aby dodać cykl ustaw jego przewidywany start.',
                'exp_start.date' => "Pole 'Start' powinno być datą w formacie 'yyyy-mm-dd'.",
                'exp_end.after' => "'Termin' musi być ustawiony w przyszłości.",
                'exp_end.after_or_equal' => "'Termin' musi być później niż 'Start'",
                'exp_end.required' => 'Aby dodać cykl ustaw termin wykonania.',
                'exp_end.date' => "Pole 'Termin' powinno być datą w formacie 'yyyy-mm-dd'.",
                'comment.max' => 'Pole uwagi zawiera zbyt dużo znaków.',
            ]);


        $insert_result = $this->insertProductionCycle($request, $employee_no);
        if (array_key_exists('ERROR', $insert_result)) {
            $insert_result = $insert_result['ERROR'];
            return back()->with('status_err', $insert_result)->withInput();
        }
        dd($insert_result);
        return back();
    }


    private function insertProductionCycle($request, string $employee_no) : array
    {
        $category = intval($request->category);
        $id = intval($request->id);
        if(!in_array($category, [1,2,3])) {
            Log::channel('error')->error('Error inserting cycle: incorrect $request->category value: '.$request->category, [
                'employeeNo' => $employee_no,
            ]);
            return array('ERROR' => 'Nie udało się dodać cyklu. Błąd systemu przy ustalaniu kategorii.');
        }
        try {
            //product
            if($category == 1) {
                if(!Product::find($id) instanceof Product) {
                    Log::channel('error')->error('Error inserting cycle: incorrect $request->id value: '.$request->id.'. Product with provided id not found.', [
                        'employeeNo' => $employee_no,
                    ]);
                    return array('ERROR' => 'Nie udało się dodać cyklu. Błąd systemu.');
                }

                $parent_id = DB::table('production_cycle')->insertGetId([
                    'level' => 1,
                    'product_id' => $id,
                    'expected_start_time' => $request->exp_start.' 00:00:00',
                    'expected_end_time' => $request->exp_end.' 23:59:59',
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),]

                );
                $components = ProductComponent::where('product_id', $id);

            }
        }
        catch(Exception $e) {

        }

        return [];
    }
    private function filterByExpectedEndTimeParentCycles(Request $request): array
    {
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
}
