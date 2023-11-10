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
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpParser\Node\Expr\Cast\Double;
use stdClass;

class ComponentController
{
    use HasEnsure;
    /**
     * Display the employee dashboard.
     */

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
            return view('component.component-details', [
                'comp' => $component,
                'prod_standards' => $prod_standards,
                'data' => $data,
                'instruction' => $instruction,
                'storage_path'=> 'components'
            ]);
        }

        return view('component.component-details', [
            'error_msg' => 'Brak danych dla komponentu.',
        ]);

    }
    public function addComponent(Request $request, ?string $id = null): View
    {
        $data = $this->getAddComponentData();

        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $insert_error = $request->session()->get('insert_error');
        return view('component.component-add',[
            'prod_schemas' => $data['prod_schemas'],
            'schema_data' => $data['prod_schema_tasks'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
            'prod_schema_errors' => $prod_schema_errors,
            'insert_error' => $insert_error,

        ]);

    }

    public function addSimilarComponent(Request $request, string $id): View | RedirectResponse
    {
        $data = $this->getAddComponentData();

        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $insert_error = $request->session()->get('insert_error');

        if($id != null) {
            $comp = Component::find($id);
            if($comp != null) {
                $selected_comp_schemas = DB::select('select
                                        cps.production_schema_id,
                                        ps.production_schema,
                                        pstd.duration_hours,
                                        pstd.amount,
                                        u.unit,
                                        cps.sequence_no
                                    from component_production_schema cps
                                    join production_schema ps
                                        on cps.production_schema_id = ps.id
                                    left join production_standard pstd
                                        on ps.id = pstd.production_schema_id
                                        and pstd.component_id = '.$id.'
                                    left join unit u
                                        on u.id = pstd.unit_id
                                    where cps.component_id = '.$id.'
                                    order by cps.sequence_no');

                $prodschema_input = '';
                foreach ($selected_comp_schemas as $schema) {
                    $prodschema_input .= $schema->production_schema_id.'_';
                }
                $prodschema_input = substr($prodschema_input, 0, strlen($prodschema_input)-1);

                return view('component.component-add',[
                    'prod_schemas' => $data['prod_schemas'],
                    'schema_data' => $data['prod_schema_tasks'],
                    'units' => $data['units'],
                    'material_list' => $data['materials'],
                    'user' => $request->user(),
                    'prod_schema_errors' => $prod_schema_errors,
                    'insert_error' => $insert_error,
                    'selected_comp' => $comp,
                    'selected_comp_schemas' => $selected_comp_schemas,
                    'prodschema_input' => $prodschema_input,
                ]);
            }
        }


        return redirect()->route('product.index')->with('status_err', 'Nie znaleziono komponentu');
    }

    private function getAddComponentData(): array
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

        return array('materials' => $materials,
                     'units' => $units,
                     'prod_schema_tasks' => $prod_schema_tasks,
                     'prod_schemas' => $prod_schemas
        );
    }
    public function storeComponent(Request $request): RedirectResponse
    {
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
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];

        try {

            DB::beginTransaction();

            $insert_result = $this->insertComponent($employee_no, $request->name, $request->material, $desc, $independent,
                                    $height, $length, $width, $request->file('comp_photo'),$request->file('instr_pdf'),
                                    $request->file('instr_video'), $schema_arr);

            if(array_key_exists('SAVED_FILES', $insert_result)) {
                $saved_files['components'] = $insert_result['SAVED_FILES'];
            }

            if(array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting component: error occurred in Component->insertComponent method.
    Error message: '.$insert_result['ERROR']);
            }


            $comp_id = array_key_exists('ID', $insert_result) ? $insert_result['ID'] : 0;
            if($comp_id == 0) {
                throw new Exception('Error inserting component: after insert to component table. Failed to evaluate id of inserted component.');

            }

            $this->insertCompProdSchemaAndProdStd($comp_id, $schema_arr, $employee_no);

            $insert_result = $this->insertInstruction($comp_id, $request->name, $employee_no,
                                                      $request->file('instr_pdf'),
                                                      $request->file('instr_video'));

            if(array_key_exists('SAVED_FILES', $insert_result)) {
                foreach ($insert_result['SAVED_FILES'] as $file_name) {
                    $saved_files['instructions'] = $file_name;
                }
            }

            if(array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting component: error occurred in Component->insertInstruction method.
    Error message: '.$insert_result['ERROR']);
            }

            //DB::commit();
            DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $file_name) {
                saveFile::deleteFile($path, $file_name);
            }

            if(isset($insert_result) and array_key_exists('ERROR', $insert_result)) {
                return back()->with('insert_error', $insert_result['ERROR'])
                    ->withInput();
            }
            return back()->with('insert_error', 'Nowy komponent nie został dodany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }



        return redirect()->route('product.index')->with('status', 'Komponent został dodany do systemu.');
    }




    ///////////////////////////////////////////////////////////
    ///  PRIVATE METHODS
    ///////////////////////////////////////////////////////////
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

    private function insertComponent(string $employee_no, string $name, string $material, string $description, int $independent,
                                     float $height, float $length, float $width, $comp_image): array
    {

        $comp_id = DB::table('component')->insertGetId([
            'name' => $name,
            'material' => $material,
            'description' => $description,
            'independent' => $independent,
            'image' => '',
            'height' => $height,
            'length' => $length,
            'width' => $width,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        $image_name = '';
        if($comp_image instanceof UploadedFile) {
            $image_name = saveFile::saveFile($comp_image, 'components', 'comp_'.$comp_id.'_');
            //if failed to save comp image file
            if(empty($image_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Zdjęcie komponentu" na dysku.');
            }
        }

        try {
            DB::table('component')
                ->where('id', $comp_id)
                ->update(['image' => $image_name]);

        } catch(Exception $e) {
            Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie nazwy pliku "Zdjęcie komponentu" w bazie danych.',
                         'SAVED_FILES' => $image_name);
        }

        return array('SAVED_FILES' => $image_name,
                     'ID' => $comp_id);

    }

    private function insertInstruction(int $comp_id, string $name, string $employee_no, $instr_pdf, $instr_video): array
    {

        $instr_name = 'Instrukcja wykonania komponentu: '.$name;
        $instr_id = DB::table('instruction')->insertGetId([
            'component_id' => $comp_id,
            'name' => $instr_name,
            'instruction_pdf' => '',
            'video' => '',
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        $instr_pdf_name = '';
        if($instr_pdf instanceof UploadedFile) {
            $instr_pdf_name = saveFile::saveFile($instr_pdf, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save instr file
            if(empty($instr_pdf_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Instrukcja wykonania komponentu".');
            }
        }

        $instr_video_name = '';
        if($instr_video instanceof UploadedFile) {
            $instr_video_name = saveFile::saveFile($instr_video, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save comp instr video file
            if(empty($instr_video_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            }
        }

        DB::table('instruction')
            ->where('id', $instr_id)
            ->update(['instruction_pdf' => $instr_pdf_name,
                      'video' => $instr_video_name,]);

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
