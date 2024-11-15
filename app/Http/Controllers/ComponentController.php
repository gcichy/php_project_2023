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
    public function index(Request $request): View
    {
        $components = Component::all();

        return view('component.component', [
            'user' => $request->user(),
            'components' => $components,
            'storage_path_components' => 'components',
            'storage_path_products' => 'products'
        ]);
    }
    public function componentDetails(Request $request, string $id): View
    {
        $component = Component::find($id);

        $instruction = Instruction::where('component_id', $id)->select('name', 'instruction_pdf', 'video')->get();
        if (count($instruction) > 0) {
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
                                            where pstd.component_id = ' . $id
            . ' order by pstd.production_schema_id asc');
        $data = DB::select('select
                                    cps.component_id,
                                    cps.production_schema_id as prod_schema_id,
                                    ps.production_schema as prod_schema,
                                    ps.description as prod_schema_desc,
                                    t.id as task_id,
                                    t.sequence_no as task_sequence_no,
                                    t.amount_required,
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
                                         join task t
                                              on t.production_schema_id = ps.id
                                         left join production_standard pstd
                                                   on pstd.component_id = cps.component_id
                                                       and pstd.production_schema_id = cps.production_schema_id
                                         left join unit u
                                                   on u.id = pstd.unit_id
                                where cps.component_id = ' . $id .
            ' order by cps.sequence_no asc, t.sequence_no asc');

        $user = Auth::user();
        if (!is_null($component)) {
            return view('component.component-details', [
                'comp' => $component,
                'prod_standards' => $prod_standards,
                'data' => $data,
                'instruction' => $instruction,
                'user' => $user,
                'storage_path_components' => 'components',
                'storage_path_instructions' => 'instructions',
            ]);
        }

        return view('component.component-details', [
            'error_msg' => 'Brak danych dla materiału.',
        ]);

    }
    public function addComponent(Request $request, ?string $id = null): View
    {
        $data = $this->getAddComponentData(false);

        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $status = $request->session()->get('status');
        return view('component.component-add', [
            'prod_schemas' => $data['prod_schemas'],
            'schema_data' => $data['prod_schema_tasks'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
            'prod_schema_errors' => $prod_schema_errors,
            'status' => $status,

        ]);

    }
    public function editComponent(Request $request, string $id): View|RedirectResponse
    {
        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $status = $request->session()->get('status');

        if ($id != null) {
            $comp = Component::find($id);
            if ($comp instanceof Component) {

                $selected_comp_instr = Instruction::where('component_id', $comp->id)
                    ->select('instruction_pdf', 'video')->get();
                $selected_comp_instr = count($selected_comp_instr) > 0 ? $selected_comp_instr[0] : null;

                $data = $this->getAddComponentData(true, $comp->id);
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
                                        and pstd.component_id = ' . $comp->id . '
                                    left join unit u
                                        on u.id = pstd.unit_id
                                    where cps.component_id = ' . $comp->id . '
                                    order by cps.sequence_no');

                $prodschema_input = '';
                foreach ($selected_comp_schemas as $schema) {
                    $prodschema_input .= $schema->production_schema_id . '_';
                }
                $prodschema_input = substr($prodschema_input, 0, strlen($prodschema_input) - 1);

                $update = str_contains($request->url(), 'edytuj');

                return view('component.component-add', [
                    'prod_schemas' => $data['prod_schemas'],
                    'schema_data' => $data['prod_schema_tasks'],
                    'units' => $data['units'],
                    'material_list' => $data['materials'],
                    'user' => $request->user(),
                    'prod_schema_errors' => $prod_schema_errors,
                    'status' => $status,
                    'selected_comp' => $comp,
                    'selected_comp_schemas' => $selected_comp_schemas,
                    'selected_comp_instr' => $selected_comp_instr,
                    'prodschema_input' => $prodschema_input,
                    'update' => $update,
                ]);
            }
        }


        return redirect()->route('component.index')->with('status_err', 'Nie znaleziono materiału');
    }
    public function storeComponent(Request $request): RedirectResponse
    {

        $this->validateAddComponentForm($request, 'INSERT');

        $schema_arr = $this->validateProdSchemas($request);
        if (array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('prod_schema_errors', $schema_arr)->withInput();
        } else if (array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }

        $user = Auth::user();
        $independent = empty($request->independent) ? 0 : 1;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];

        try {

            DB::beginTransaction();

            $comp_image = !empty($request->file('comp_photo')) ? $request->file('comp_photo') : $request->comp_photo_file_to_copy;
            $insert_result = $this->insertComponent($employee_no, $request->name, $request->material, $request->description, $independent,
                $height, $length, $width, $comp_image);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                $saved_files['components'] = [$insert_result['SAVED_FILES']];
            }

            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting component: error occurred in Component->insertComponent method.
    Error message: ' . $insert_result['ERROR']);
            }


            $comp_id = array_key_exists('ID', $insert_result) ? $insert_result['ID'] : 0;
            if ($comp_id == 0) {
                throw new Exception('Error inserting component: after insert to component table. Failed to evaluate id of inserted component.');

            }

            $this->insertCompProdSchemaAndProdStd($comp_id, $schema_arr, $employee_no);

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;

            $instr_name = 'Instrukcja wykonania materiału: '.$request->name;
            $insert_result = InstructionController::insertInstruction($comp_id, 'component_id', $instr_name ,$employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                    $saved_files['instructions'] = $insert_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting component: error occurred in Component->insertInstruction method.
    Error message: ' . $insert_result['ERROR']);
            }
            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error inserting component: ' . $e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $files) {
                if(is_array($files)) {
                    foreach ($files as $file) {
                        fileTrait::deleteFile($path, $file);
                    }
                }
                else if(is_string($files)) {
                    fileTrait::deleteFile($path, $files);
                }
            }

            if (isset($insert_result) and array_key_exists('ERROR', $insert_result)) {
                return back()->with('status', $insert_result['ERROR'])
                    ->withInput();
            }
            return back()->with('status', 'Nowy materiał nie został dodany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('component.index')->with('status', 'Materiał został dodany do systemu.');
    }
    public function storeUpdatedComponent(Request $request): RedirectResponse
    {
        $this->validateAddComponentForm($request, 'UPDATE');

        $schema_arr = $this->validateProdSchemas($request);
        if (array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('prod_schema_errors', $schema_arr)->withInput();
        } else if (array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        if (!isset($request->component_id) or empty($request->component_id)) {
            Log::channel('error')->error('Error updating component: error occurred in Component->storeUpdatedComponent method. ID of the component not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować materiału - nie znaleziono ID.')->withInput();
        }
        if (!(Component::find($request->component_id) instanceof Component)) {
            Log::channel('error')->error('Error updating component: error occurred in Component->storeUpdatedComponent method. Component with id ' . $request->component_id . ' not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować materiału - nie znaleziono materiału o podanym ID.')->withInput();
        }

        $comp_id = $request->component_id;
        $independent = $request->independent == null ? 0 : 1;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $saved_files = [];

        try {

            DB::beginTransaction();

            $comp_image = !empty($request->file('comp_photo')) ? $request->file('comp_photo') : $request->comp_photo_file_to_copy;
            $update_result = $this->updateComponent($comp_id, $employee_no, $request->name, $request->material, $request->description, $independent,
                $height, $length, $width, $comp_image);

            if (array_key_exists('SAVED_FILES', $update_result)) {
                $saved_files['components'] = $update_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating component: error occurred in Component->updateComponent method.
    Error message: ' . $update_result['ERROR']);
            }

            $this->updateCompProdSchemaAndProdStd($comp_id, $schema_arr, $employee_no);

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;
            $instr_name = 'Instrukcja wykonania produktu: '.$request->name;
            $update_result = InstructionController::updateInstruction($comp_id, 'component_id', $instr_name, $employee_no, $instr_pdf, $instr_video);
            //$update_result = $this->updateInstruction($comp_id, $request->name, $employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $update_result)) {
                $saved_files['instructions'] = $update_result['SAVED_FILES'];

            }
            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating component: error occurred in Component->insertInstruction method.
    Error message: ' . $update_result['ERROR']);
            }
            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error updating component: ' . $e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $files) {
                if(is_array($files)) {
                    foreach ($files as $file) {
                        fileTrait::deleteFile($path, $file);
                    }
                }
                else if(is_string($files)) {
                    fileTrait::deleteFile($path, $files);
                }
            }

            if (isset($update_result) and array_key_exists('ERROR', $update_result)) {
                return back()->with('status', $update_result['ERROR'])
                    ->withInput();
            }
            return back()->with('status', 'Materiał nie został edytowany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('component.index')->with('status', 'Edytowano materiał.');
    }
    public function destroyComponent(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'confirmation' => ['regex:(usuń|usun)'],
            ],
                [
                    'confirmation.regex' => 'Nie można usunąć materiału: niepoprawna wartość. Wpisz "usuń".',
                ]);
        }
        catch (Exception $e) {
            return redirect()->back()->with('status_err', $e->getMessage());
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $comp_id = $request->remove_id;
        $comp = Component::find($comp_id);
        if($comp instanceof Component) {
            try {

                DB::beginTransaction();

                ProductComponent::where('component_id', $comp_id)->delete();
                ComponentProductionSchema::where('component_id', $comp_id)->delete();
                ProductionStandard::where('component_id', $comp_id)->delete();

                $instr= Instruction::where('component_id',$comp_id)
                    ->select('instruction_pdf','video')
                    ->get();
                $instr = count($instr) == 1 ? $instr[0] : null;

                Instruction::where('component_id', $comp_id)->delete();
                Component::where('id', $comp_id)->delete();

                if($instr instanceof Instruction) {
                    if(fileTrait::fileExists('instructions', $instr->instruction_pdf)) {
                        fileTrait::deleteFile('instructions', $instr->instruction_pdf);
                    }
                    if(fileTrait::fileExists('instructions', $instr->video)) {
                        fileTrait::deleteFile('instructions', $instr->video);
                    }
                }
                if(fileTrait::fileExists('components', $comp->image)) {
                    fileTrait::deleteFile('components', $comp->image);
                }

                DB::commit();

            } catch (Exception $e) {
                Log::channel('error')->error('Error deleting component: ' . $e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                DB::rollBack();

                return back()->with('status_err', 'Materiał nie został usunięty: błąd przy usuwaniu danych z systemu.')
                    ->withInput();
            }
        }

        return  redirect()->route('component.index')
            ->with('status', 'Usunięto materiał: '.$comp->name.'.')
            ->withInput();
    }

    ///////////////////////////////////////////////////////////
    ///  PRIVATE METHODS
    ///////////////////////////////////////////////////////////
    private function getAddComponentData(bool $adjusted_to_component, int $component_id = 0): array
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
                                        t.id as task_id,
                                        t.sequence_no as task_sequence_no,
                                        t.name as task_name,
                                        t.description as task_desc
                                    from production_schema psh
                                             left join task t
                                                       on t.production_schema_id = psh.id
                                             left join component_production_schema cps
                                                       on psh.id = cps.production_schema_id
                                                           and cps.component_id = '.$component_id.'
                                    order by cps.sequence_no, t.production_schema_id, t.sequence_no');
        } else {
            $data = DB::select('select
                                        psh.id as prod_schema_id,
                                        psh.production_schema as prod_schema,
                                        psh.description as prod_schema_desc,
                                        psh.tasks_count,
                                        t.id as task_id,
                                        t.sequence_no as task_sequence_no,
                                        t.name as task_name,
                                        t.description as task_desc
                                    from production_schema psh
                                         left join task t
                                             on t.production_schema_id = psh.id
                                    order by t.production_schema_id, t.sequence_no');
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
                    $error_arr[] = 'Niepoprawna wartość Czas [h] dla jednego z zadań.';
                }
                if($request->$amount == null or $request->$amount <= 0) {
                    $error_arr[] = 'Niepoprawna wartość Ilość dla jednego z zadań.';
                }
                if($request->$unit == null or !in_array($request->$unit, $units)) {
                    $error_arr[] = 'Niepoprawna wartość Jednostka dla jednego z zadań.';
                }
                if($request->$sequence_no == null or !in_array($request->$sequence_no, $sequence_no_arr)) {
                    $error_arr[] = 'Niepoprawne wartości Kolejność wyk. Zadania powinny zawierać liczby od 1 do '.count($schemas).' (w dowolnej kolejności).';
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
    private function insertComponent(string $employee_no, string $name, string|null $material, string|null $description, int $independent, float|null $height, float|null $length, float|null $width, $comp_image): array
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
            $image_name = fileTrait::saveFile($comp_image, 'components', 'comp_'.$comp_id.'_');
            //if failed to save comp image file
            if(empty($image_name)) {
                return array('ERROR' => 'Nowy materiał nie został dodany: błąd przy zapisie pliku "Zdjęcie materiału" na dysku.');
            }
        }
        else if(is_string($comp_image)) {
            $new_image_name = fileTrait::getFileName('components', $comp_image);
            if(!fileTrait::copyFile('components', $comp_image, 'components', $new_image_name)) {
                return array('ERROR' => 'Nowy materiał nie został dodany: błąd przy kopiowaniu pliku "Zdjęcie materiału" na dysku.');
            }
            else {
                $image_name = $new_image_name;
            }
        }

        if(!empty($image_name)) {
            try {
                DB::table('component')
                    ->where('id', $comp_id)
                    ->update(['image' => $image_name]);

            } catch(Exception $e) {
                Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'Nowy materiał nie został dodany: błąd przy zapisie nazwy pliku "Zdjęcie materiału" w bazie danych.',
                    'SAVED_FILES' => array($image_name));
            }
        }


        return array('SAVED_FILES' => array($image_name),
                     'ID' => $comp_id);

    }
    private function updateComponent(int $comp_id, string $employee_no, string $name, string|null $material, string|null $description, int $independent, float|null $height, float|null $length, float|null $width, $comp_image): array
    {
        $comp_old = Component::find($comp_id);

        DB::table('component')
            ->where('id', $comp_id)
            ->update([
            'name' => $name,
            'material' => $material,
            'description' => $description,
            'independent' => $independent,
            'height' => $height,
            'length' => $length,
            'width' => $width,
            'updated_by' => $employee_no,
            'updated_at' => date('y-m-d h:i:s'),
        ]);


        $image_name = '';
        if($comp_image instanceof UploadedFile) {
            $image_name = fileTrait::saveFile($comp_image, 'components', 'comp_'.$comp_id.'_');
            //if failed to save comp image file
            if(empty($image_name)) {
                return array('ERROR' => 'Materiał nie został edytowany: błąd przy zapisie pliku "Zdjęcie materiału" na dysku.');
            }
            if(!empty($comp_old->image) and fileTrait::fileExists('components', $comp_old->image)) {
                fileTrait::deleteFile('components', $comp_old->image);
            }

        }
        else if(is_null($comp_image)) {
            if(!empty($comp_old->image) and fileTrait::fileExists('components', $comp_old->image)) {
                fileTrait::deleteFile('components', $comp_old->image);
            }
            $image_name = null;
        }

        //if image name is null update occurs
        if(is_null($image_name) || !empty($image_name)) {
            try {
                DB::table('component')
                    ->where('id', $comp_id)
                    ->update(['image' => $image_name]);

            } catch(Exception $e) {
                Log::channel('error')->error('Error updating component: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'Materiał nie został edytowany: błąd przy zapisie nazwy pliku "Zdjęcie materiału" w bazie danych.',
                    'SAVED_FILES' => array($image_name));
            }
        }


        return array('SAVED_FILES' => array($image_name));

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
    private function updateCompProdSchemaAndProdStd(int $comp_id, array $schema_arr, string $employee_no ): void
    {
        $old_schemas_id = ComponentProductionSchema::where(['component_id' => $comp_id])
                            ->select('production_schema_id')->get();
        $old_schemas_id = collect($old_schemas_id)->map(function (ComponentProductionSchema $arr) { return $arr->production_schema_id; })->toArray();

        $old_standards_id = ProductionStandard::where(['component_id' => $comp_id])
            ->select('production_schema_id')->get();
        $old_standards_id = collect($old_standards_id)->map(function (ProductionStandard $arr) { return $arr->production_schema_id; })->toArray();

        foreach ($schema_arr as $schema_id => $value) {
            $unit_id = DB::select("select id from unit where unit = '".$value['unit']."'");
            $unit_id = collect($unit_id)->map(function (stdClass $arr) { return $arr->id; })->toArray();
            $unit_id = count($unit_id) > 0 ? $unit_id[0] : 0;

            if(in_array($schema_id, $old_schemas_id)) {

                DB::table('component_production_schema')
                    ->where(['component_id' => $comp_id,
                        'production_schema_id' => $schema_id])
                    ->update([
                        'sequence_no' => $value['sequence_no'],
                        'unit_id' => $unit_id,
                        'updated_by' => $employee_no,
                        'updated_at' => date('y-m-d h:i:s'),
                    ]);
                array_splice($old_schemas_id,array_search($schema_id, $old_schemas_id),1);
            }
            else {
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
            }

            if(in_array($schema_id, $old_standards_id)) {
                DB::table('production_standard')
                    ->where(['component_id' => $comp_id,
                        'production_schema_id' => $schema_id])
                    ->update([
                        'name' => '',
                        'duration_hours' => $value['duration'],
                        'amount' =>$value['amount'],
                        'unit_id' => $unit_id,
                        'updated_by' => $employee_no,
                        'updated_at' => date('y-m-d h:i:s'),
                    ]);
                array_splice($old_standards_id,array_search($schema_id, $old_standards_id),1);
            }
            else {
                DB::table('production_standard')->insert([
                    'component_id' => $comp_id,
                    'production_schema_id' => $schema_id,
                    'name' => '',
                    'duration_hours' => $value['duration'],
                    'amount' => $value['amount'],
                    'unit_id' => $unit_id,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            }
        }

        foreach ($old_schemas_id as $old_schema_id) {
            DB::table('component_production_schema')
                ->where(['component_id' => $comp_id,
                    'production_schema_id' => $old_schema_id,])
                ->delete();
        }
        foreach ($old_standards_id as $old_schema_id) {
            DB::table('production_standard')
                ->where(['component_id' => $comp_id,
                    'production_schema_id' => $old_schema_id,])
                ->delete();
        }
    }
    private function validateAddComponentForm(Request $request, string $action) : void
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

        $name_rules = ['required', 'string',  'min:1','max:100'];
        if($action == 'INSERT') {
            $name_rules[] =  'unique:'.Component::class;
        }
        $request->validate([
            'name' => $name_rules,
            'material' => ['required', 'string',  $mat_in],
            'comp_photo' => ['mimes:jpeg,gif,bmp,png,jpg,svg', 'max:16384'],
            'instr_pdf' => ['mimes:pdf', 'max:16384'],
            'instr_video' => ['mimes:mp4,mov,mkv,wmv', 'max:51300'],
            'height' => ['gte:0'],
            'length' => ['gte:0'],
            'width' => ['gte:0'],
            'description' => ['max:200'],
            'prodschema_input' => ['required'],
        ],
            [
                'name.unique' => 'Nazwa materiału musi być unikalna.',
                'material.in' => 'Wybierz jeden z surowców: '.$err_mess.'.',
                'comp_photo.mimes' => 'Przesłany plik powinien mieć rozszerzenie: jpeg,bmp,png,jpg,svg. Rozszerzenie pliku: '.$ext_comp_photo.'.',
                'instr_pdf.mimes' => 'Przesłany plik powinien mieć rozszerzenie: pdf. Rozszerzenie pliku: '.$ext_instr_pdf.'.',
                'instr_video.mimes' => 'Przesłany plik powinien mieć rozszerzenie: mp4,mov,mkv,wmv. Rozszerzenie pliku: '.$ext_instr_video.'.',
                'comp_photo.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_pdf.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_video.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 50 MB.',
                'height.gte' => 'Wysokość nie może być ujemna.',
                'length.gte' => 'Długość nie może być ujemna.',
                'width.gte' => 'Szerokość nie może być ujemna.',
                'prodschema_input.required' => 'Wybierz przynajmniej jedno zadanie.',
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'min' => 'Wpisany tekst ma za mało znaków.',
            ]);


    }

}
