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

    /**
     * Display the employee dashboard.
     */

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
                                where cps.component_id = ' . $id .
            ' order by cps.sequence_no asc, pst.sequence_no asc');

        if (!is_null($component)) {
            return view('component.component-details', [
                'comp' => $component,
                'prod_standards' => $prod_standards,
                'data' => $data,
                'instruction' => $instruction,
                'storage_path_components' => 'components',
                'storage_path_instructions' => 'instructions',
            ]);
        }

        return view('component.component-details', [
            'error_msg' => 'Brak danych dla komponentu.',
        ]);

    }

    public function addComponent(Request $request, ?string $id = null): View
    {
        $data = $this->getAddComponentData(false);

        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $insert_error = $request->session()->get('insert_error');
        return view('component.component-add', [
            'prod_schemas' => $data['prod_schemas'],
            'schema_data' => $data['prod_schema_tasks'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
            'prod_schema_errors' => $prod_schema_errors,
            'insert_error' => $insert_error,

        ]);

    }

    public function editComponent(Request $request, string $id): View|RedirectResponse
    {
        $prod_schema_errors = $request->session()->get('prod_schema_errors');
        $insert_error = $request->session()->get('insert_error');

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
                    'insert_error' => $insert_error,
                    'selected_comp' => $comp,
                    'selected_comp_schemas' => $selected_comp_schemas,
                    'selected_comp_instr' => $selected_comp_instr,
                    'prodschema_input' => $prodschema_input,
                    'update' => $update,
                ]);
            }
        }


        return redirect()->route('product.index')->with('status_err', 'Nie znaleziono komponentu');
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
        $independent = $request->independent == null ? 0 : $request->independent;
        $desc = empty($request->description) ? '' : $request->description;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];

        try {

            DB::beginTransaction();

            $comp_image = !empty($request->file('comp_photo')) ? $request->file('comp_photo') : $request->comp_photo_file_to_copy;
            $insert_result = $this->insertComponent($employee_no, $request->name, $request->material, $desc, $independent,
                $height, $length, $width, $comp_image);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                $saved_files['components'] = $insert_result['SAVED_FILES'];
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
            $insert_result = $this->insertInstruction($comp_id, $request->name, $employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                foreach ($insert_result['SAVED_FILES'] as $file_name) {
                    $saved_files['instructions'] = $file_name;
                }
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

            foreach ($saved_files as $path => $file_name) {
                fileTrait::deleteFile($path, $file_name);
            }

            if (isset($insert_result) and array_key_exists('ERROR', $insert_result)) {
                return back()->with('insert_error', $insert_result['ERROR'])
                    ->withInput();
            }
            return back()->with('insert_error', 'Nowy komponent nie został dodany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('product.index')->with('status', 'Komponent został dodany do systemu.');
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
            Log::channel('error')->error('Error updating component: error occurred in Component->insertComponent method. ID of the component not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować komponentu - nie znaleziono ID.')->withInput();
        }
        if (!(Component::find($request->component_id) instanceof Component)) {
            Log::channel('error')->error('Error updating component: error occurred in Component->insertComponent method. Component with id ' . $request->component_id . ' not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować komponentu - komponent o podanym komponentu o podanym ID.')->withInput();
        }

        $comp_id = $request->component_id;
        $independent = $request->independent == null ? 0 : $request->independent;
        $desc = empty($request->description) ? '' : $request->description;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $saved_files = [];

        try {

            DB::beginTransaction();

            $comp_image = !empty($request->file('comp_photo')) ? $request->file('comp_photo') : $request->comp_photo_file_to_copy;
            $update_result = $this->updateComponent($comp_id, $employee_no, $request->name, $request->material, $desc, $independent,
                $height, $length, $width, $comp_image);

            if (array_key_exists('SAVED_FILES', $update_result)) {
                $saved_files['components'] = $update_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error inserting component: error occurred in Component->insertComponent method.
    Error message: ' . $update_result['ERROR']);
            }

            $this->updateCompProdSchemaAndProdStd($comp_id, $schema_arr, $employee_no);

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;
            $update_result = $this->updateInstruction($comp_id, $request->name, $employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $update_result)) {
                foreach ($update_result['SAVED_FILES'] as $file_name) {
                    $saved_files['instructions'] = $file_name;
                }
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

            foreach ($saved_files as $path => $file_name) {
                fileTrait::deleteFile($path, $file_name);
            }

            if (isset($update_result) and array_key_exists('ERROR', $update_result)) {
                return back()->with('insert_error', $update_result['ERROR'])
                    ->withInput();
            }
            return back()->with('insert_error', 'Komponent nie został edytowany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('product.index')->with('status', 'Edytowano komponent.');
    }


    public function destroyComponent(Request $request): RedirectResponse
    {

        try {
            $request->validate([
                'confirmation' => ['regex:(usuń|usun)'],
            ],
                [
                    'confirmation.regex' => 'Nie można usunąć komponentu: niepoprawna wartość. Wpisz "usuń".',
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

                return back()->with('status_err', 'Komponent nie został usunięty: błąd przy usuwaniu danych z systemu.')
                    ->withInput();
            }
        }

        return  redirect()->route('product.index')
            ->with('status', 'Nie można usunąć komponentu: nie znaleziono wybranego komponentu.')
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
            $image_name = fileTrait::saveFile($comp_image, 'components', 'comp_'.$comp_id.'_');
            //if failed to save comp image file
            if(empty($image_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Zdjęcie komponentu" na dysku.');
            }
        }
        else if(is_string($comp_image)) {
            $new_image_name = fileTrait::getFileName('components', $comp_image);
            if(!fileTrait::copyFile('components', $comp_image, 'components', $new_image_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy kopiowaniu pliku "Zdjęcie komponentu" na dysku.');
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
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie nazwy pliku "Zdjęcie komponentu" w bazie danych.',
                    'SAVED_FILES' => $image_name);
            }
        }


        return array('SAVED_FILES' => $image_name,
                     'ID' => $comp_id);

    }

    private function updateComponent(int $comp_id, string $employee_no, string $name, string $material, string $description, int $independent,
                                     float $height, float $length, float $width, $comp_image): array
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
                return array('ERROR' => 'Komponent nie został edytowany: błąd przy zapisie pliku "Zdjęcie komponentu" na dysku.');
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
                return array('ERROR' => 'Komponent nie został edytowany: błąd przy zapisie nazwy pliku "Zdjęcie komponentu" w bazie danych.',
                    'SAVED_FILES' => $image_name);
            }
        }


        return array('SAVED_FILES' => $image_name);

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
            $instr_pdf_name = fileTrait::saveFile($instr_pdf, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save instr file
            if(empty($instr_pdf_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Instrukcja wykonania komponentu".');
            }
        }
        else if(is_string($instr_pdf)) {
            $new_instr_pdf_name = fileTrait::getFileName('instructions', $instr_pdf);
            if(!fileTrait::copyFile('instructions', $instr_pdf, 'instructions', $new_instr_pdf_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy kopiowaniu pliku "Instrukcja wykonania komponentu".');
            }
            else {
                $instr_pdf_name = $new_instr_pdf_name;
            }
        }

        $instr_video_name = '';
        if($instr_video instanceof UploadedFile) {
            $instr_video_name = fileTrait::saveFile($instr_video, 'instructions', 'instr_vid'.$instr_id.'_');
            //if failed to save comp instr video file
            if(empty($instr_video_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            }
        }
        else if(is_string($instr_video)) {
            $new_instr_video_name = fileTrait::getFileName('instructions', $instr_video);
            if(!fileTrait::copyFile('instructions', $instr_video, 'instructions', $new_instr_video_name)) {
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy kopiowaniu pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            }
            else {
                $instr_video_name = $new_instr_video_name;
            }
        }

        $saved_files = [];
        if(!empty($instr_pdf_name)) {
            $saved_files[] = $instr_pdf_name;
        }
        if(!empty($instr_video_name)) {
            $saved_files[] = $instr_video_name;
        }

        if(count($saved_files) > 0) {
            try {
                DB::table('instruction')
                    ->where('id', $instr_id)
                    ->update(['instruction_pdf' => $instr_pdf_name,
                        'video' => $instr_video_name,]);

            } catch(Exception $e) {
                Log::channel('error')->error('Error inserting component: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'Nowy komponent nie został dodany: błąd przy zapisie nazwy plików "Instrukcja wykonania komponentu" oraz "Film instruktażowy" w bazie danych.',
                    'SAVED_FILES' => $saved_files);
            }
        }


        return array('SAVED_FILES' => $saved_files);
    }

    private function updateInstruction(int $comp_id, string $name, string $employee_no, $instr_pdf, $instr_video): array
    {
        $instr_name = 'Instrukcja wykonania komponentu: '.$name;

        $instr_old = Instruction::where('component_id',$comp_id)->get();
        $instr_id = collect($instr_old)->map(function (Instruction $arr) { return $arr->id; })->toArray();

        $instr_old = count($instr_old) == 1 ? $instr_old[0] : null;

        if(count($instr_id) > 0) {
            $instr_id = $instr_id[0];

            DB::table('instruction')
                ->where('id',$instr_id)
                ->update([
                    'name' => $instr_name,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
        }
        else {
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
        }

        $instr_pdf_name = '';
        if($instr_pdf instanceof UploadedFile) {
            $instr_pdf_name = fileTrait::saveFile($instr_pdf, 'instructions', 'instr_doc_'.$instr_id.'_');
            //if failed to save instr file
            if(empty($instr_pdf_name)) {
                return array('ERROR' => 'Komponent nie został edytowany: błąd przy zapisie pliku "Instrukcja wykonania komponentu" na dysku.');
            }
            if(!empty($instr_old->instruction_pdf) and fileTrait::fileExists('instructions', $instr_old->instruction_pdf)) {
                fileTrait::deleteFile('instructions', $instr_old->instruction_pdf);
            }
        }
        else if(is_null($instr_pdf)) {
            if(!empty($instr_old->instruction_pdf) and fileTrait::fileExists('instructions', $instr_old->instruction_pdf)) {
                fileTrait::deleteFile('instructions', $instr_old->instruction_pdf);
            }
            $instr_pdf_name = null;
        }


        $instr_video_name = '';
        if($instr_video instanceof UploadedFile) {
            $instr_video_name = fileTrait::saveFile($instr_video, 'instructions', 'instr_vid_'.$instr_id.'_');
            //if failed to save comp instr video file
            if(empty($instr_video_name)) {
                if(empty($instr_pdf_name)){
                    return array('ERROR' => 'Komponent nie został edytowany: błąd przy zapisie pliku "Film instruktażowy" na dysku.');
                }
                return array('ERROR' => 'Komponent nie został edytowany: błąd przy zapisie pliku "Film instruktażowy" na dysku.',
                             'SAVED_FILES' => [$instr_pdf_name]);
            }
            if(!empty($instr_old->video) and fileTrait::fileExists('instructions', $instr_old->video)) {
                fileTrait::deleteFile('instructions', $instr_old->video);
            }
        }
        else if(is_null($instr_video)) {
            if(!empty($instr_old->video) and fileTrait::fileExists('instructions', $instr_old->video)) {
                fileTrait::deleteFile('instructions', $instr_old->video);
            }
            $instr_video_name = null;
        }


        $saved_files = [];
        if(!empty($instr_pdf_name)) {
            $saved_files[] = $instr_pdf_name;
        }
        else if(!is_null($instr_pdf_name)) {
            $instr_pdf_name = $instr_old->instruction_pdf;
        }
        if(!empty($instr_video_name)) {
            $saved_files[] = $instr_video_name;
        }
        else if(!is_null($instr_video_name)) {
            $instr_video_name = $instr_old->video;
        }

        try {
            DB::table('instruction')
                ->where('id', $instr_id)
                ->update(['instruction_pdf' => $instr_pdf_name,
                    'video' => $instr_video_name,]);

        } catch(Exception $e) {
            Log::channel('error')->error('Error updating component: '.$e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            return array('ERROR' => 'Nowy komponent nie został edytowany: błąd przy zapisie nazwy plików "Instrukcja wykonania komponentu" oraz "Film instruktażowy" w bazie danych.',
                'SAVED_FILES' => $saved_files);
        }


        return array('SAVED_FILES' => $saved_files);
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


    }

}
