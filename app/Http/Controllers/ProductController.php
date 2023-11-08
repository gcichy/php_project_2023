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
        $insert_error = $request->session()->get('insert_error');
        return view('product.component-add',[
            'prod_schemas' => $prod_schemas,
            'schema_data' => $prod_schema_tasks,
            'units' => $units,
            'user' => $request->user(),
            'material_list' => $materials,
            'prod_schema_errors' => $prod_schema_errors,
            'insert_error' => $insert_error,

        ]);
    }

    public function storeComponent(Request $request): RedirectResponse
    {
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

        $ext_comp_photo = empty($request->file('comp_photo')) ? '' : $request->file('comp_photo')->extension();
        $ext_instr_pdf = empty($request->file('instr_pdf')) ? '' : $request->file('instr_pdf')->extension();
        $ext_instr_video = empty($request->file('instr_video')) ? '' : $request->file('instr_video')->extension();
        //independent tu nie dajemy tylko osobno bo odwala ten button...
        $request->validate([
            'name' => ['required', 'string',  'min:1','max:100', 'unique:'.Component::class],
            'material' => ['required', 'string',  $mat_in],
            'comp_photo' => ['mimes:jpeg,gif,bmp,png,jpg,svg', 'max:16384'],
            'instr_pdf' => ['mimes:pdf,docx', 'max:16384'],
            'instr_video' => ['mimes:mp4,mov,mkv,wmv', 'max:51300'],
            'height' => ['gt:-1'],
            'length' => ['gt:-1'],
            'width' => ['gt:-1'],
            'description' => ['max:200'],
            'prodschema_input' => ['required'],
        ],
            [
                'name.unique' => 'Nazwa komponentu musi być unikalna.',
                'material.in' => 'Wybierz jeden z materiałów: '.$err_mess.'.',
                'comp_photo.mimes' => 'Przesłany plik powinien mieć rozszerzenie: jpeg,bmp,png,jpg,svg. Rozszerzenie pliku: '.$ext_comp_photo.'.',
                'instr_pdf.mimes' => 'Przesłany plik powinien mieć rozszerzenie: pdf,docx. Rozszerzenie pliku: '.$ext_instr_video.'.',
                'instr_video.mimes' => 'Przesłany plik powinien mieć rozszerzenie: mp4,mov,mkv,wmv. Rozszerzenie pliku: '.$ext_instr_pdf.'.',
                'comp_photo.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_pdf.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_video.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 50 MB.',
                'height.gt' => 'Wysokość nie może być ujemna.',
                'length.gt' => 'Długość nie może być ujemna.',
                'width.gt' => 'Szerokość nie może być ujemna.',
                'prodschema_input.required' => 'Wybierz przynajmniej jeden schemat produkcji.',
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'min' => 'Wpisany tekst ma za mało znaków.',
            ]);

        $schema_arr = $this->validateProdSchemas($request);
        if(array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('prod_schema_errors', $schema_arr)->withInput();
        }
        else if(array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }


        $user = Auth::user();
        $independent = $request->independent == null ? 0 : $request->independent;
        $desc = empty($request->description) ? '' : $request->description;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);

        $insert_result = $this->insertComponent($user, $request->name, $request->material, $desc, $independent,
                                $height, $length, $width, $request->file('comp_photo'),$request->file('instr_pdf'),
                                $request->file('instr_video'), $schema_arr);


        if(array_key_exists('ERROR', $insert_result)) {
            $insert_result = $insert_result['ERROR'];
            return back()->with('insert_error', $insert_result)->withInput();
        }




        return redirect()->route('product.index');
    }

    private function validateProdSchemas(Request $request): array
    {
        $units = DB::select('select unit from unit');
        //CAST DB::select result to simple array
        $units = collect($units)->map(function (stdClass $arr) { return $arr->unit; })->toArray();

        //errors to be displayed on page are stored here
        $error_arr = [];
        $insert_arr = [];
        $schemas = explode('_',$request->prodschema_input);

        //each prod schema values validation
        $sequence_no_arr = array_map(function($x) { return $x; }, range(1, count($schemas)));
        foreach ($schemas as $schema) {
            $schema_id = intval($schema);
            if($schema_id > 0) {
                $duration = 'duration_'.$schema_id;
                $amount = 'amount_'.$schema_id;
                $unit = 'unit_'.$schema_id;
                $sequence_no = 'sequenceno_'.$schema_id;

                if($request->$duration == null or $request->$duration <= 0) {
                    $error_arr[] = 'Niepoprawna wartość Czas [h] dla jednego ze schematów produkcji.';
                }
                if($request->$amount == null or $request->$amount <= 0) {
                    $error_arr[] = 'Niepoprawna wartość Ilość dla jednego ze schematów produkcji.';
                }
                if($request->$unit == null or !in_array($request->$unit, $units)) {
                    $error_arr[] = 'Niepoprawna wartość Jednostka dla jednego ze schematów produkcji.';
                }
                if($request->$sequence_no == null or !in_array($request->$sequence_no, $sequence_no_arr)) {
                    $error_arr[] = 'Niepoprawne wartości Kolejność wyk. Schematy powinny zawierać liczby od 1 do '.count($schemas).' (w dowolnej kolejności).';
                } else {
                    array_splice($sequence_no_arr,array_search($request->$sequence_no, $sequence_no_arr),1);
                }

                $error_arr = array_unique($error_arr);
                if(count($error_arr) == 0) {
                    $insert_arr[$schema_id] = array(
                        "duration" => $request->$duration,
                        "amount" => $request->$amount,
                        "unit" => $request->$unit,
                        "sequence_no" => $request->$sequence_no
                    );
                }
            }
        }

        if(count($error_arr) > 0) {
            return array('ERROR' => $error_arr);
        }
        return array('INSERT' => $insert_arr);

    }

    private function insertComponent(User $user, string $name, string $material, string $description, int $independent,
                                     float $height, float $length, float $width, $comp_image, $instr_pdf,
                                     $instr_video, array $schema_arr): array
    {
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        try {
            $comp_id = DB::select('select id from component order by id desc limit 1');
            //convert comp_id to simple array
            $comp_id = collect($comp_id)->map(function (stdClass $arr) { return $arr->id; })->toArray();
            //if no records in component table, then first will be added
            $comp_id = count($comp_id) > 0 ? $comp_id[0] : 1;
            $comp_id++;
        }
        catch(Exception $e) {
            Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy nadawaniu id komponentu.');
        }



        try {
            $image_name = '';
            if($comp_image instanceof UploadedFile) {
                $image_name = saveFile::saveFile($comp_image, 'component_images', 'comp_'.$comp_id.'_');
                //if failed to save comp image file
                if(empty($image_name)) {
                    return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Zdjęcie komponentu".');
                }
            }

            DB::beginTransaction();

            $comp_id = DB::table('component')->insertGetId([
                'name' => $name,
                'material' => $material,
                'description' => $description,
                'independent' => $independent,
                'image' => $image_name,
                'height' => $height,
                'length' => $length,
                'width' => $width,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);

            $this->insertCompProdSchemaAndProdStd($comp_id, $schema_arr, $employee_no);

            //od tego miejsca jest nieprzetestowane
            $instr_files_save_status = $this->saveInstructionFiles($instr_pdf, $instr_video);
            if(array_key_exists('ERROR', $instr_files_save_status)) {
                if(array_key_exists('SAVED_FILES', $instr_files_save_status)) {
                    foreach ($instr_files_save_status['SAVED_FILES'] as $file_name) {
                        saveFile::deleteFile('instructions', $file_name);
                    }

                }
                return array('ERROR',$instr_files_save_status['ERROR']);
            }


            $this->insertInstruction($comp_id, $name, $employee_no, $instr_files_save_status['SAVED_FILES'][0], $instr_files_save_status['SAVED_FILES'][1]);


            DB::rollBack();
            return array('ERROR' => 'Tu byłby commit.');
        } catch (Exception $e) {
            Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            if(isset($instr_files_save_status) and array_key_exists('SAVED_FILES', $instr_files_save_status)) {
                foreach ($instr_files_save_status['SAVED_FILES'] as $file_name) {
                    saveFile::deleteFile('instructions', $file_name);
                }
            }

            return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy wprowadzaniu danych do systemu.');
        }


    }

    private function insertInstruction(int $comp_id, string $name, string $employee_no, string $instr_pdf_name, string $instr_video_name): void
    {

        $instr_name = 'Instrukcja wykonania komponentu: '.$name;
        DB::table('instruction')->insert([
            'component_id' => $comp_id,
            'name' => $instr_name,
            'instr_pdf' => $instr_pdf_name,
            'instr_video' => $instr_video_name,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

    private function saveInstructionFiles( $instr_pdf, $instr_video): array
    {
        $instr_id = DB::select('select id from instruction order by id desc limit 1');
        //convert comp_id to simple array
        $instr_id = collect($instr_id)->map(function (stdClass $arr) { return $arr->id; })->toArray();
        //if no records in component table, then first will be added
        $instr_id = count($instr_id) > 0 ? $instr_id[0] : 1;
        $instr_id++;

        $instr_pdf_name = '';
        if($instr_pdf instanceof UploadedFile) {
            $instr_pdf_name = saveFile::saveFile($instr_pdf, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save comp image file
            if(empty($instr_pdf_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Instrukcja wykonania komponentu".');
            }
        }

        $instr_video_name = '';
        if($instr_video instanceof UploadedFile) {
            $instr_video_name = saveFile::saveFile($instr_video, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save comp image file
            if(empty($instr_video_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            }
        }

        return array('SAVED_FILES' => [$instr_pdf_name, $instr_video_name]);
    }

    private function insertCompProdSchemaAndProdStd(int $comp_id, array $schema_arr, string $employee_no ): void
    {

        foreach ($schema_arr as $schema_id => $value) {

            $unit_id = DB::select("select id from unit where unit = '".$value['unit']."'");
            $unit_id = collect($unit_id)->map(function (stdClass $arr) { return $arr->id; })->toArray();
            $unit_id = count($unit_id) > 0 ? $unit_id[0] : 0;

            DB::table('component_production_schema')->insert([
                'component_id' => $comp_id,
                'production_schema_id' => $schema_id,
                'sequence_no' => $value['sequence_no'],
                'unit_id' => $unit_id,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);

            DB::table('production_standard')->insert([
                'component_id' => $comp_id,
                'production_schema_id' => $schema_id,
                'name' => '',
                'duration_hours' => $value['duration'],
                'amount' =>$value['amount'],
                'unit_id' => $unit_id,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);
        }
    }



}
