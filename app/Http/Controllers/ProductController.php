<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Helpers\fileTrait;
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
                'storage_path_products' => 'products',
                'storage_path_instructions' => 'instructions',
                'storage_path_components' => 'components',
            ]);
        }

        return view('product.product-details', [
            'error_msg' => 'Brak danych dla produktu.',
        ]);


    }


    public function addProduct(Request $request, ?string $id = null): View
    {
        $data = $this->getAddProductData(false);
        $components = Component::all();
        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $insert_error = $request->session()->get('insert_error');
        return view('product.product-add', [
            'components' => $components,
            'prod_schemas' => $data['prod_schemas'],
            'schema_data' => $data['prod_schema_tasks'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
            'prod_schema_errors' => $prod_schema_errors,
            'insert_error' => $insert_error,

        ]);
    }


    private function getAddProductData(bool $adjusted_to_component, int $component_id = 0): array
    {
        $materials = StaticValue::where('type','material')->get();
        $units = Unit::select('unit','name')->get();
        $prod_schemas = ProductionSchema::all();
        if($adjusted_to_component) {
            $data = DB::select('select
                                        psh.id as prod_schema_id,
                                        cps.sequence_no as prod_schema_sequence_no,
                                        psh.production_schema as prod_schema,
                                        psh.description as prod_schema_desc,
                                        psh.tasks_count,
                                        psht.task_id,
                                        psht.sequence_no as task_sequence_no,
                                        t.name as task_name,
                                        t.description as task_desc
                                    from production_schema psh
                                             left join production_schema_task psht
                                                on psh.id = psht.production_schema_id
                                             left join task t
                                                on t.id = psht.task_id
                                             left join component_production_schema cps
                                                on psh.id = cps.production_schema_id
                                                and cps.component_id = '.$component_id.'
                                    order by cps.sequence_no, psht.production_schema_id, psht.sequence_no');
        } else {
            $data = DB::select('select
                                        psh.id as prod_schema_id,
                                        psh.production_schema as prod_schema,
                                        psh.description as prod_schema_desc,
                                        psh.tasks_count,
                                        psht.task_id,
                                        psht.sequence_no as task_sequence_no,
                                        t.name as task_name,
                                        t.description as task_desc
                                    from production_schema psh
                                             left join production_schema_task psht
                                                  on psh.id = psht.production_schema_id
                                             left join task t
                                                  on t.id = psht.task_id
                                    order by production_schema_id, task_sequence_no');
        }


        $prod_schema_tasks = array();
        if(count($data) > 0) {
            $curr_schema_id = $data[0]->prod_schema_id;
            $temp = [];

            foreach ($data as $row) {
                if ($row->prod_schema_id != $curr_schema_id) {
                    $prod_schema_tasks[$curr_schema_id] = $temp;
                    $curr_schema_id = $row->prod_schema_id;
                    $temp = [];
                }
                $temp[] = $row;
            }
            $prod_schema_tasks[$curr_schema_id] = $temp;
        }

        return array('materials' => $materials,
            'units' => $units,
            'prod_schema_tasks' => $prod_schema_tasks,
            'prod_schemas' => $prod_schemas
        );
    }

}
