<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Helpers\saveFile;
use App\Models\Component;
use App\Models\ComponentProductionSchema;
use App\Models\Instruction;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductionSchema;
use App\Models\ProductionStandard;
use App\Models\StaticValue;
use App\Models\Unit;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpParser\Node\Expr\Cast\Double;
use stdClass;

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
            'storage_path_components' => 'components',
            'storage_path_products' => 'products'
        ]);

    }

    public function productDetails(Request $request, string $id): View
    {
        $product = Product::find($id);

        $instruction = Instruction::where('product_id', $id)->select('name', 'instruction_pdf', 'video')->get();
        if (count($instruction) > 0) {
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
                                where pc.product_id = ' . $id .
            ' order by pc.component_id asc');

        if (!is_null($product)) {
            return view('product.product-details', [
                'prod' => $product,
                'data' => $data,
                'instruction' => $instruction,
                'storage_path' => 'products',
            ]);
        }

        return view('product.product-details', [
            'error_msg' => 'Brak danych dla produktu.',
        ]);


    }


    public function addProduct(Request $request, ?string $id = null): View
    {
        return view('product.product-add');
    }
}
