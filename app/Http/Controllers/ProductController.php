<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Models\Component;
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

    public function details(Request $request, string $id): View
    {


        return view('product.product_details', [
            'productId' => $id,
        ]);

    }


    public function add(): View
    {
        return view('product.product_add');
    }

}
