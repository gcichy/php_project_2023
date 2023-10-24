<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Models\Component;
use App\Models\ComponentProductionSchema;
use App\Models\Instruction;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductionStandard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController
{
    use HasEnsure;
    /**
     * Display the employee dashboard.
     */
    public function index(Request $request): View
    {
        $products = Product::all();
        $components = Component::all();
        $prod_comp_list = array();

        foreach ($products as $product) {
            $comps = DB::table('product_component')
                        ->select('component_id')
                        ->where('product_id', $product->id)
                        ->get()->toArray();
            for ($i = 0; $i < count($comps); $i++) {
                $comps[$i] = $comps[$i]->component_id;
            }
            $prod_comp_list[$product->id] = Component::whereIn('id', $comps)->get();
        }
        return view('product.product', [
            'user' => $request->user(),
            'products' => $products,
            'components' => $components,
            'prod_comp_list' => $prod_comp_list,
        ]);

    }

    public function productDetails(Request $request, string $id): View
    {
        $product = Product::find($id);

        $instruction = Instruction::where('product_id', $id)->select('name', 'instruction_pdf','video')->get();
        if(count($instruction) > 0){
            $instruction = $instruction[0];
        }

        $data = DB::select('select
                                       c.id,
                                       c.name,
                                       c.description,
                                       c.independent,
                                       c.material,
                                       c.image,
                                       c.height,
                                       c.length,
                                       c.width
                                from product_component pc
                                join component c
                                    on pc.component_id = c.id
                                where pc.product_id = '.$id.
                                ' order by pc.component_id asc');

        if(!is_null($product)) {
            return view('product.product-details', [
                'prod' => $product,
                'data' => $data,
                'instruction' => $instruction,
            ]);
        }

        return view('product.product-details', [
            'error_msg' => 'Brak danych dla produktu.',
        ]);


    }
    public function componentDetails(Request $request, string $id): View
    {
        $component = Component::find($id);

        $instruction = Instruction::where('component_id', $id)->select('name', 'instruction_pdf','video')->get();
        if(count($instruction) > 0){
            $instruction = $instruction[0];
        }

        $prod_standards = DB::select('select  pstd.id,
                                                    pstd.name,pstd.description,
                                                    pstd.duration_hours,
                                                    pstd.amount,
                                                    u.unit
                                            from production_standard pstd
                                            join unit u
                                                on u.id = pstd.id
                                            where pstd.component_id = '.$id
                                            .' order by pstd.production_schema_id asc');
        $data = DB::select('select
                                       cps.component_id,
                                       cps.production_schema_id as prod_schema_id,
                                       ps.production_schema as prod_schema,
                                       ps.description as prod_schema_desc,
                                       pst.task_id,
                                       pst.sequence_no as task_sequence_no,
                                       pst.amount_required,
                                       pst.additional_description,
                                       t.name as task_name,
                                       t.description as task_desc,
                                       pstd.name as prod_std_name,
                                       pstd.description as prod_std_desc,
                                       pstd.duration_hours prod_std_duration,
                                       pstd.amount as prod_std_amount,
                                       u.unit as prod_std_unit
                                from component_production_schema cps
                                join production_schema ps
                                    on ps.id = cps.production_schema_id
                                join production_schema_task pst
                                    on pst.production_schema_id = ps.id
                                join task t
                                    on t.id = pst.task_id
                                left join production_standard pstd
                                    on pstd.component_id = cps.component_id
                                    and pstd.production_schema_id = cps.production_schema_id
                                left join unit u
                                    on u.id = pstd.unit_id
                                where cps.component_id = '.$id.
                                ' order by cps.sequence_no asc, pst.sequence_no asc');

        if(!is_null($component)) {
            return view('product.component-details', [
                'comp' => $component,
                'prod_standards' => $prod_standards,
                'data' => $data,
                'instruction' => $instruction,
            ]);
        }

        return view('product.component-details', [
            'error_msg' => 'Brak danych dla komponentu.',
        ]);

    }

    public function addProduct(): View
    {
        return view('product.product-add');
    }

    public function addComponent(): View
    {
        return view('product.component-add');
    }

}
