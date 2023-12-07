<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChildCycleView;
use App\Models\Component;
use App\Models\ParentCycleView;
use App\Models\Product;
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
        if($category == 1) {
            $category_name = 'Materiał';
            $elements = Component::where('indepentent', 1);
            if(is_string($request->filter_elem)) {
                $elements = $elements->where('name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('material', 'like', '%'.$request->filter_elem.'%');
            }
            $elements = $elements->paginate(10);
        } else if($category == 2 ) {
            $pack_product_id = StaticValue::where('type', 'pack_schema')->select('value')->first();
            $products = Product::select('id', 'name', 'image', 'material')->paginate(10);
            $category_name = 'Zadanie';
            $independent_schemas_id = DB::select('select distinct production_schema_id from production_standard pstd where component_id is null');
            $independent_schemas_id = collect($independent_schemas_id)->map(function ($arr) { return $arr->production_schema_id; })->toArray();
            $elements = ProductionSchema::whereIn('id',$independent_schemas_id);
            if(is_string($request->filter_elem)) {
                $elements = $elements->where('production_schema', 'like', '%' . $request->filter_elem . '%');
            }
            $elements = $elements->paginate(10);
        } else {
            $category_name = 'Produkt';
            if(is_string($request->filter_elem)) {
                $elements = Product::where('name', 'like', '%'.$request->filter_elem.'%')
                                    ->orWhere('material', 'like', '%'.$request->filter_elem.'%')
                                    ->paginate(10);
                ;
            } else {
                $elements = Product::paginate(10);
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
            'storage_path_products' => 'products',
            'storage_path_components' => 'components',
        ]);
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
