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

    public function addComponent(Request $request): View
    {
        $materials = StaticValue::where('type','material')->get();
        $units = Unit::select('unit','name')->get();
        $prod_schemas = ProductionSchema::all();
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
        }

        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        return view('product.component-add',[
            'prod_schemas' => $prod_schemas,
            'schema_data' => $prod_schema_tasks,
            'units' => $units,
            'user' => $request->user(),
            'material_list' => $materials,
            'prod_schema_errors' => $prod_schema_errors

        ]);
    }

    public function storeComponent(Request $request): RedirectResponse
    {
        //dd($request);
        //$request->file('dropzone-file')->extension()
        $materials = StaticValue::where('type','material')->select('value', 'value_full')->get();

        $err_mess = '';
        $mat_in = 'in:';
        foreach ($materials as $mat) {
            $mat_in .= $mat->value.',';
            $err_mess .= $mat->value_full.' ,';
        }
        $mat_in = rtrim($mat_in,',');
        $err_mess = rtrim($err_mess,',');

        $file_ext = empty($request->file('dropzone-file')) ? '' : $request->file('dropzone-file')->extension();

        //independent tu nie dajemy tylko osobno bo odwala ten button...
        $request->validate([
            'name' => ['required', 'string',  'min:1','max:100'],
            'material' => ['required', 'string',  $mat_in],
            'dropzone_file' => ['mimes:jpeg,gif,bmp,png,jpg,svg'],
            'height' => ['gt:-1'],
            'length' => ['gt:-1'],
            'width' => ['gt:-1'],
            'description' => ['max:200'],
            'prodschema_input' => ['required'],
        ],
            [
                'material.in' => 'Wybierz jeden z materiałów: '.$err_mess,
                'dropzone_file.mimes' => 'Przesłany plik powinien mieć rozszerzenie: jpeg,bmp,png,jpg,svg. Rozszerzenie pliku: '.$file_ext,
                'height.gt' => 'Wysokość nie może być ujemna',
                'length.gt' => 'Długość nie może być ujemna',
                'width.gt' => 'Szerokość nie może być ujemna',
                'prodschema_input.required' => 'Wybierz przynajmniej jeden schemat produkcji',
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'min' => 'Wpisany tekst ma za mało znaków.',
            ]);

        $schema_arr = $this->validateProdSchemas($request);
        if(array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('prod_schema_errors', $schema_arr);
        }
        else if(array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }
        $file_name = '';
        if(!empty($request->file('dropzone-file'))) {
            $file_name = saveFile::saveFile($request->file('dropzone-file'), 'component_images');
        }

        $independent = $request->independent == null ? 0 : $request->independent;
        $desc = empty($request->description) ? '' : $request->description;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $this->InsertComponent($request->name, $request->material, $desc, $independent,
                                $height, $length, $width, $file_name);




        return redirect()->route('product.index');
    }

    private function validateProdSchemas(Request $request): array
    {
        $units = DB::select('select unit from unit');
        //CAST DB::select result to simple array
        $units = collect($units)->map(function (stdClass $arr) { return $arr->unit; })->toArray();

        $error_arr = [];
        $insert_arr = [];
        $schemas = explode('_',$request->prodschema_input);
        foreach ($schemas as $schema) {
            $schema_id = intval($schema);
            if($schema_id > 0) {
                $duration = 'duration_'.$schema_id;
                $amount = 'amount_'.$schema_id;
                $unit = 'unit_'.$schema_id;

                if($request->$duration == null or $request->$duration <= 0) {
                    $error_arr[0] = 'Niepoprawna wartość Czas [h] dla jednego ze schematów produkcji';
                }
                if($request->$amount == null or $request->$amount <= 0) {
                    $error_arr[1] = 'Niepoprawna wartość Ilość dla jednego ze schematów produkcji';
                }
                if($request->$unit == null or !in_array($request->$unit, $units)) {
                    $error_arr[2] = 'Niepoprawna wartość Jednostka dla jednego ze schematów produkcji';
                }

                $error_arr = array_values($error_arr);
                if(count($error_arr) == 0) {
                    $insert_arr[$schema_id] = array(
                        "duration" => $request->$duration,
                        "amount" => $request->$amount,
                        "unit" => $request->$unit
                    );
                }
            }
        }

        if(count($error_arr) > 0) {
            return array('ERROR' => $error_arr);
        }
        return array('INSERT' => $insert_arr);

    }

    private function InsertComponent(string $name, string $material, string $description, int $independent,
                                     float $height, float $length, float $width, string $image): string
    {
        $employee_no = 'unknown';
        $user = Auth::user();
        if( ($user instanceof User) and !empty($user->employeeNo)) {
            $employee_no = $user->employeeNo;
        }

        try {
            DB::table('component')->insert([
                'name' => $name,
                'material' => $material,
                'description' => $description,
                'independent' => $independent,
                'image' => $image,
                'height' => $height,
                'length' => $length,
                'width' => $width,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);
        } catch (Exception $e) {
            return 'INSERT ERROR: failed to insert into Component table:'.$e->getMessage();
        }

        return true;
    }

}
