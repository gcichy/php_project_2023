<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Models\Component;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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


        return view('product.product', [
            'user' => $request->user(),
            'products' => $products,
            'components' => $components
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
