<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Models\Component;
use App\Models\ComponentProductionSchema;
use App\Models\Product;
use App\Models\ProductComponent;
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
        return view('product.product-details', [
            'productId' => $id,
        ]);

    }
    public function componentDetails(Request $request, string $id): View
    {
        $component = Component::find($id);
        $data = DB::select('select cps.production_schema_id,
                                       ps.production_schema,
                                       ps.description as prod_schema_desc,
                                       pst.task_id,
                                       pst.sequence_no as task_sequence_no,
                                       pst.amount_required,
                                       pst.additional_description,
                                       t.name,
                                       t.description as task_desc
                                from component_production_schema cps
                                join production_schema ps
                                    on ps.id = cps.production_schema_id
                                join production_schema_task pst
                                    on pst.production_schema_id = ps.id
                                join task t
                                    on t.id = pst.task_id
                                where cps.component_id = 2
                                order by ps.id asc, pst.sequence_no asc');

        if(!is_null($component) and count($data) > 0) {
            return view('product.component-details', [
                'comp' => $component,
                'data' => $data
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
