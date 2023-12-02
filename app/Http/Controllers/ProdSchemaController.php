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
use App\Models\Task;
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

class ProdSchemaController
{
    use HasEnsure;

    /**
     * Display the employee dashboard.
     */
    public function index(Request $request): View
    {
        $prod_schema_tasks = $this->getSchemaData();

        return view('prod-schema.prod-schema', [
            'user' => $request->user(),
            'schema_data' => $prod_schema_tasks,
            'storage_path_components' => 'components',
            'storage_path_products' => 'products'
        ]);
    }
    public function schemaDetails(Request $request, string $id): View
    {
        $prod_schema = ProductionSchema::where('id',$id)->select('id','production_schema', 'description', 'tasks_count')->first();
        $prod_schema_tasks = $this->getSchemaData($id);
        $instruction = Instruction::where('production_schema_id', $id)->select('id','name','instruction_pdf','video')->first();


        if (!empty($prod_schema_tasks) and array_key_exists($id, $prod_schema_tasks)) {
            $prod_schema_tasks = $prod_schema_tasks[$id];
            return view('prod-schema.prod-schema-details', [
                'prod_schema' => $prod_schema,
                'prod_schema_tasks' => $prod_schema_tasks,
                'instruction' => $instruction,
                'storage_path_components' => 'components',
                'storage_path_instructions' => 'instructions',
            ]);
        }

        return view('component.component-details', [
            'error_msg' => 'Brak danych dla zadania.',
        ]);

    }

    public function addSchema(Request $request): View
    {
        $data = $this->getAddSchemaData();

        return view('prod-schema.prod-schema-add', [
            'tasks' => $data['tasks'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
        ]);

    }

    public function editSchema(Request $request, string $id): View|RedirectResponse
    {

        if ($id != null) {
            $schema = ProductionSchema::where('id',$id)->select('id','production_schema', 'description', 'tasks_count', 'non_countable')->first();
            if ($schema instanceof ProductionSchema) {
                $data = $this->getAddSchemaData();
                $selected_schem_tasks = $this->getAddSchemaData($id);
                $selected_schem_tasks = $selected_schem_tasks['tasks'];
                $selected_schem_instr = Instruction::where('production_schema_id', $id)->select('id','name','instruction_pdf','video')->first();
                $selected_schem_prod_std = DB::select('select
                                                                pstd.id as production_standard_id,
                                                                pstd.amount,
                                                                pstd.duration_hours,
                                                                u.unit
                                                            from production_schema psh
                                                                join production_standard pstd
                                                                    on psh.id = pstd.production_schema_id
                                                                join unit u
                                                                    on pstd.unit_id = u.id
                                                            where psh.id = '.$id.'
                                                                    and pstd.component_id is null');
                $selected_schem_prod_std = count($selected_schem_prod_std) == 1? $selected_schem_prod_std[0] : null;

                $task_input = '';
                foreach ($selected_schem_tasks as $task) {
                    $task_input .= $task->task_id . '_';
                }
                $task_input = substr($task_input, 0, strlen($task_input) - 1);

                $update = str_contains($request->url(), 'edytuj');


                return view('prod-schema.prod-schema-add', [
                    'tasks' => $data['tasks'],
                    'units' => $data['units'],
                    'material_list' => $data['materials'],
                    'user' => $request->user(),
                    'selected_schem' => $schema,
                    'selected_schem_tasks' => $selected_schem_tasks,
                    'selected_schem_instr' => $selected_schem_instr,
                    'selected_schem_prod_std' => $selected_schem_prod_std,
                    'task_input' => $task_input,
                    'update' => $update,
                ]);

//                return view('component.component-add', [
//                    'prod_schemas' => $data['prod_schemas'],
//                    'schema_data' => $data['prod_schema_tasks'],
//                    'units' => $data['units'],
//                    'material_list' => $data['materials'],
//                    'user' => $request->user(),
//                    'prod_schema_errors' => $prod_schema_errors,
//                    'status' => $status,
//                    'selected_schem' => $schema,
//                    'selected_schem_tasks' => $selected_schem_tasks,
//                    'selected_schem_instr' => $selected_schem_instr,
//                    'task_input' => $task_input,
//                    'update' => $update,
//                ]);
            }
        }


        return redirect()->route('product.index')->with('status_err', 'Nie znaleziono komponentu');
    }

    public function storeSchema(Request $request): RedirectResponse
    {
        $this->validateAddSchemaForm($request, 'INSERT');
        if((is_null($request->amount) and !is_null($request->duration)) or (!is_null($request->amount) and is_null($request->duration))) {
            return back()->withErrors(['amount' => 'Aby dodać normę produkcji dla zadania należy podać czas trwania oraz ilość.'])->withInput();
        }

        $schema_arr = $this->validateTasks($request);

        if (array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('task_errors', $schema_arr)->withInput();
        } else if (array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];
        $non_countable = !empty($request->countable);
        try {

            DB::beginTransaction();
            $insert_result = $this->insertProdSchema($employee_no, $request->production_schema, $request->description, count($schema_arr), $non_countable);
            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting production_schema: error occurred in ProdSchema->insertProdSchema method.
    Error message: ' . $insert_result['ERROR']);
            }

            $schema_id = array_key_exists('ID', $insert_result) ? $insert_result['ID'] : 0;
            if ($schema_id == 0) {
                throw new Exception('Error inserting production_schema: after insert to production_schema table. Failed to evaluate id of inserted production_schema.');

            }
            if(!in_array(null,array($request->amount,$request->duration, $request->unit)) and !$non_countable) {
                $amount = floatval($request->amount);
                $duration = floatval($request->duration);
                $this->insertProdSchemaProdStd($schema_id, $amount,$duration, $request->unit, $employee_no);
            }

            $this->insertTasks($schema_id, $schema_arr, $employee_no);

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;

            $instr_name = 'Instrukcja wykonania zadania: '.$request->name;
            $insert_result = InstructionController::insertInstruction($schema_id, 'production_schema_id', $instr_name ,$employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                    $saved_files['instructions'] = $insert_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('Error inserting production_schema: error occurred in Instruction->insertInstruction method.
    Error message: ' . $insert_result['ERROR']);
            }

            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error inserting production_schema: ' . $e->getMessage(), [
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
            return back()->with('status', 'Nowe zadanie nie zostało dodane: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('schema.index')->with('status', 'Zadanie zostało dodane do systemu.');
    }

    public function storeUpdatedSchema(Request $request): RedirectResponse
    {
        $this->validateAddSchemaForm($request, 'UPDATE');

        $schema_arr = $this->validateTasks($request);

        if (array_key_exists('ERROR', $schema_arr)) {
            $schema_arr = $schema_arr['ERROR'];
            return back()->with('task_errors', $schema_arr)->withInput();
        } else if (array_key_exists('INSERT', $schema_arr)) {
            $schema_arr = $schema_arr['INSERT'];
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        if (!isset($request->schema_id) or empty($request->schema_id)) {
            Log::channel('error')->error('Error updating production_schema: error occurred in ProdSchema->storeUpdatedSchema method. ID of the production_schema not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować zadania - nie znaleziono ID.')->withInput();
        }
        if (!(ProductionSchema::find($request->schema_id) instanceof ProductionSchema)) {
            Log::channel('error')->error('Error updating production_schema: error occurred in ProdSchema->storeUpdatedSchema method. ProductionSchema with id ' . $request->schema_id . ' not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować zadania - nie znaleziono zadania o podanym ID.')->withInput();
        }

        $schema_id = $request->schema_id;
        $saved_files = [];
        $non_countable = !empty($request->countable);

        try {
            DB::beginTransaction();

            $this->updateProdSchema($schema_id, $employee_no, $request->production_schema,
                $request->description, count($schema_arr), $non_countable);

            $this->updateProdSchemaProdStd($schema_id, $request->amount,$request->duration, $request->unit, $employee_no, $non_countable);

            $this->updateTasks($schema_id, $schema_arr, $employee_no);

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;
            $instr_name = 'Instrukcja wykonania zadania: ' . $request->name;
            $update_result = InstructionController::updateInstruction($schema_id, 'production_schema_id', $instr_name, $employee_no, $instr_pdf, $instr_video);
            if (array_key_exists('SAVED_FILES', $update_result)) {
                $saved_files['instructions'] = $update_result['SAVED_FILES'];
            }
            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating production_schema: error occurred in ProdSchema->updateInstruction method.
    Error message: ' . $update_result['ERROR']);
            }

            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error updating production_schema: ' . $e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        fileTrait::deleteFile($path, $file);
                    }
                } else if (is_string($files)) {
                    fileTrait::deleteFile($path, $files);
                }
            }

            if (isset($update_result) and array_key_exists('ERROR', $update_result)) {
                return back()->with('status', $update_result['ERROR'])
                    ->withInput();
            }
            return back()->with('status', 'Zadanie nie zostało edytowane: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('schema.index')->with('status', 'Edytowano zadanie.');
    }



    public function destroySchema(Request $request): RedirectResponse
    {

        try {
            $request->validate([
                'confirmation' => ['regex:/^(usuń|usun)$/i'],
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
        $schema_id = $request->remove_id;
        $schema = ProductionSchema::find($schema_id);
        if($schema instanceof ProductionSchema) {
            try {

                DB::beginTransaction();

                Task::where('production_schema_id', $schema_id)->delete();
                ProductionStandard::where('production_schema_id', $schema_id)->delete();

                $instr= Instruction::where('production_schema_id',$schema_id)
                    ->select('instruction_pdf','video')
                    ->get();
                $instr = count($instr) == 1 ? $instr[0] : null;

                Instruction::where('production_schema_id', $schema_id)->delete();
                ProductionSchema::where('id', $schema_id)->delete();

                if($instr instanceof Instruction) {
                    if(fileTrait::fileExists('instructions', $instr->instruction_pdf)) {
                        fileTrait::deleteFile('instructions', $instr->instruction_pdf);
                    }
                    if(fileTrait::fileExists('instructions', $instr->video)) {
                        fileTrait::deleteFile('instructions', $instr->video);
                    }
                }

                DB::commit();

            } catch (Exception $e) {
                Log::channel('error')->error('Error deleting production_schema: ' . $e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                DB::rollBack();

                return back()->with('status_err', 'Zadanie nie zostało usunięte: błąd przy usuwaniu danych z systemu.')
                    ->withInput();
            }

            return  redirect()->route('schema.index')
                ->with('status', 'Usunięto zadanie: '.$schema->production_schema.'.')
                ->withInput();
        }

        return  redirect()->route('schema.index')
            ->with('status_err', 'Zadnie nie zostało usunięte: nie znaleziono zadania.')
            ->withInput();

    }

    ///////////////////////////////////////////////////////////
    ///  PRIVATE METHODS
    ///////////////////////////////////////////////////////////

    private function getSchemaData(int $schema_id = 0): array
    {

        if($schema_id > 0) {
            $data = DB::select('select
                                       ps.id as prod_schema_id,
                                       ps.production_schema as prod_schema,
                                       ps.description as prod_schema_desc,
                                       ps.non_countable,
                                       t.id as task_id,
                                       t.sequence_no as sequence_no,
                                       t.amount_required,
                                       t.name as task_name,
                                       t.description as task_desc,
                                       pstd.id as prod_std_id,
                                       pstd.name as prod_std_name,
                                       pstd.description as prod_std_desc,
                                       pstd.duration_hours prod_std_duration,
                                       pstd.amount as prod_std_amount,
                                       u.unit as prod_std_unit
                                from production_schema ps
                                join task t
                                    on t.production_schema_id = ps.id
                                left join production_standard pstd
                                       on pstd.production_schema_id = ps.id
                                        and pstd.component_id is null
                                left join unit u
                                    on u.id = pstd.unit_id
                                where ps.id = ' . $schema_id .
                ' order by ps.id, t.sequence_no');
        }
        else{
            $data = DB::select('select
                                    ps.id as prod_schema_id,
                                    ps.production_schema as prod_schema,
                                    ps.description as prod_schema_desc,
                                    ps.non_countable,
                                    t.id as task_id,
                                    t.sequence_no as sequence_no,
                                    t.amount_required,
                                    t.name as task_name,
                                    t.description as task_desc,
                                    pstd.id as prod_std_id,
                                    pstd.name as prod_std_name,
                                    pstd.description as prod_std_desc,
                                    pstd.duration_hours prod_std_duration,
                                    pstd.amount as prod_std_amount,
                                    u.unit as prod_std_unit
                                from production_schema ps
                                         join task t
                                              on t.production_schema_id = ps.id
                                         left join production_standard pstd
                                              on pstd.production_schema_id = ps.id
                                              and pstd.component_id is null
                                         left join unit u
                                              on u.id = pstd.unit_id
                                order by ps.id, t.sequence_no');
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

        return $prod_schema_tasks;
    }

    private function getAddSchemaData(int $schema_id = 0): array
    {
        $materials = StaticValue::where('type','material')->get();
        $units = Unit::select('unit','name')->get();

        if($schema_id > 0) {
//task_distinct.task_id is used, coz in the task section of the prod-schema.prod-schema-add view are diplayed only
// tasks grouped by name and desc, with lowest id. Thus to fit existing component's tasks to those tasks this join is done
            $data = DB::select("select
                                       ps.id as prod_schema_id,
                                       ps.production_schema as prod_schema,
                                       ps.description as prod_schema_desc,
                                       task_distinct.task_id,
                                       t.sequence_no as task_sequence_no,
                                       t.amount_required,
                                       t.name as task_name,
                                       t.description as task_desc
                                from production_schema ps
                                join task t
                                    on t.production_schema_id = ps.id
                                join (select
                                           min(t.id) as task_id,
                                           t.name as task_name,
                                           t.description as task_desc
                                        from task t
                                        group by t.name, t.description) task_distinct
                                on task_distinct.task_name = t.name
                                and IFNULL(task_distinct.task_desc,'') = IFNULL(t.description,'')
                                where ps.id = " . $schema_id .
                ' order by ps.id, task_distinct.task_id');
        }
        else{
            $data = DB::select('select
                                       min(t.id) as task_id,
                                       t.name as task_name,
                                       t.description as task_desc
                                from task t
                                group by t.name, t.description
                                order by task_id');
        }

        return array(
            'tasks' => $data,
            'materials' => $materials,
            'units' => $units
        );
    }
    private function validateTasks(Request $request): array
    {
        $error_arr = [];
        $insert_arr = [];
        if(is_null($request->task_input) and ($request->new_counter == 0 or is_null($request->new_counter))) {
            $error_arr[] = 'Zadanie musi mieć przypisane minimum 1 podzadanie.';
            return array('ERROR' => $error_arr);
        }

        $new_task_count = intval($request->new_counter);
        $new_task_id_arr = array();
        if($new_task_count > 0) {
            $new_task_id_arr = array_map(function($x) { return $x; }, range(1, $new_task_count));
        }


        $tasks = is_null($request->task_input) ? [] : explode('_', $request->task_input);
        $sequence_no_count = empty($tasks) ? $new_task_count : count($tasks) + $new_task_count;
        $sequence_no_arr = array_map(function($x) { return $x; }, range(1, $sequence_no_count));
        $taks_names_arr = array();
        foreach ($new_task_id_arr  as $new_task_id) {
            if($new_task_id > 0) {
                $sequence_no = 'new_sequence_no_'.$new_task_id;
                $amount_required = 'new_amount_required_'.$new_task_id;
                $name = 'new_name_'.$new_task_id;
                $desc = 'new_desc_'.$new_task_id;

                if($request->$sequence_no == null or !in_array($request->$sequence_no, $sequence_no_arr)) {
                    $error_arr[] = 'Niepoprawne wartości Kolejność wykonania. Podzadania powinny zawierać liczby od 1 do '.$sequence_no_count.' (w dowolnej kolejności).';
                } else {
                    array_splice($sequence_no_arr,array_search($request->$sequence_no, $sequence_no_arr),1);
                }
                if(empty($request->$name)) {
                    $error_arr[] = 'Aby dodać nowe podzadanie podaj jego nazwę.';
                }
                if(in_array($request->$name, $taks_names_arr)) {
                    $error_arr[] = 'Nie można dodać 2 zadań o identycznej nazwie.';
                } else {
                    $taks_names_arr[] = $request->$name;
                }

                $error_arr = array_unique($error_arr);
                if(count($error_arr) == 0) {
                    $insert_arr['new_'.$new_task_id] = array(
                        "name" => $request->$name,
                        "sequence_no" => $request->$sequence_no,
                        "description" => $request->$desc,
                        "amount_required" => $request->$amount_required,
                    );
                }
            }
        }

        if(!empty($tasks)) {
            //case when there are tasks existing tasks selected
            foreach ($tasks as $task) {
                $task_id = intval($task);
                if($task_id > 0) {
                    $name = Task::where('id', $task_id)->select('name')->first();
                    $name = $name instanceof Task ? $name->name : null;
                    $sequence_no = 'sequenceno_'.$task_id;
                    $amount_required = 'amount_required_'.$task_id;
                    if($request->$sequence_no == null or !in_array($request->$sequence_no, $sequence_no_arr)) {
                        $error_arr[] = 'Niepoprawne wartości Kolejność wykonania. Podzadania powinny zawierać liczby od 1 do '.$sequence_no_count.' (w dowolnej kolejności).';
                    } else {
                        array_splice($sequence_no_arr,array_search($request->$sequence_no, $sequence_no_arr),1);
                    }
                    if(in_array($name, $taks_names_arr)) {
                        $error_arr[] = 'Nie można dodać 2 zadań o identycznej nazwie.';
                    } else {
                        $taks_names_arr[] = $name;
                    }
                    $error_arr = array_unique($error_arr);
                    if(count($error_arr) == 0) {
                        $insert_arr[$task_id] = array(
                            "sequence_no" => $request->$sequence_no,
                            "amount_required" => $request->$amount_required,
                        );
                    }
                }
            }
        }

        if(count($error_arr) > 0) {
            return array('ERROR' => $error_arr);
        }
        return array('INSERT' => $insert_arr);
    }

    private function insertProdSchema(string $employee_no, string $prod_schema, string|null $description, int $tasks_count, bool $non_countable): array
    {

        $schema_id = DB::table('production_schema')->insertGetId([
            'production_schema' => $prod_schema,
            'description' => $description,
            'tasks_count' => $tasks_count,
            'non_countable' => $non_countable,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        return array('ID' => $schema_id);

    }

    private function updateProdSchema(int $schema_id, string $employee_no, string $prod_schema,
                                      string|null $description, int $tasks_count, bool $non_countable): void
    {

        DB::table('production_schema')
            ->where('id', $schema_id)
            ->update([
                'production_schema' => $prod_schema,
                'description' => $description,
                'tasks_count' => $tasks_count,
                'non_countable' => $non_countable,
                'updated_by' => $employee_no,
                'updated_at' => date('y-m-d h:i:s'),
                ]);

    }

    /**
     * @throws Exception
     */
    private function insertTasks(int $schema_id, array $schema_arr, string $employee_no): void
    {

        foreach ($schema_arr as $key => $schema) {
            if(is_int($key)) {
                $task = Task::where('id',$key)->select('name', 'description')->first();
                if($task instanceof Task) {
                    $schema['name'] = $task->name;
                    $schema['description'] = $task->description;
                }
                else {
                    throw new Exception('Error inserting production_schema: error occurred in ProdSchema->insertTasks method. Selected task not found in database.');
                }
            }
            else if(!(is_string($key) and str_contains($key, 'new'))) {
                throw new Exception('Error inserting production_schema: error occurred in ProdSchema->insertTasks method. Incorrect key occurred in $schema_arr array.');
            }
            $am_req = !empty($schema['amount_required']);
            DB::table('task')->insert([
                'production_schema_id' => $schema_id,
                'name' => $schema['name'],
                'description' => $schema['description'],
                'sequence_no' => $schema['sequence_no'],
                'amount_required' => $am_req,
                'created_by' => $employee_no,
                'updated_by' => $employee_no,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
            ]);
        }
    }

    /**
     * @throws Exception
     */
    private function updateTasks(int $schema_id, array $task_arr, string $employee_no): void
    {

        $ids = DB::select("select t.id as task_id, task_distinct.task_id as task_distinct_id
                                    from task t
                                        join (select
                                               min(t.id) as task_id,
                                               t.name as task_name,
                                               t.description as task_desc
                                           from task t
                                           group by t.name, t.description) task_distinct
                                             on task_distinct.task_name = t.name
                                                 and IFNULL(task_distinct.task_desc,'') = IFNULL(t.description,'')
                                    where t.production_schema_id = ".$schema_id.";");
        //array contains real task_id mapped with id from task section
        $id_map = array();
        foreach ($ids as $row) {
            $id_map[$row->task_id] = $row->task_distinct_id;
        }

        foreach ($task_arr as $key => $task) {

            $am_req = !empty($task['amount_required']);
            //if key is int then task may be added or updated if it is existing one
            if(is_int($key)) {
                //if task_id is in id_map array then update
                if(in_array($key, $id_map)) {
                    $real_task_id = array_search($key, $id_map);

                    DB::table('task')
                        ->where('id',$real_task_id)
                        ->update([
                            'sequence_no' => $task['sequence_no'],
                            'amount_required' => $am_req,
                            'updated_by' => $employee_no,
                            'updated_at' => date('y-m-d h:i:s'),
                        ]);
                    unset($id_map[$real_task_id]);
                }
                //if task_id isn't in id_map then it is inserted
                else {
                    $t = Task::where('id',$key)->select('name', 'description')->first();
                    if($t instanceof Task) {
                        $task['name'] = $t->name;
                        $task['description'] = $t->description;
                    }
                    else {
                        throw new Exception('Error updating production_schema: error occurred in ProdSchema->updateTasks method. Selected task not found in database.');
                    }
                    DB::table('task')->insert([
                        'production_schema_id' => $schema_id,
                        'name' => $task['name'],
                        'description' => $task['description'],
                        'sequence_no' => $task['sequence_no'],
                        'amount_required' => $am_req,
                        'created_by' => $employee_no,
                        'updated_by' => $employee_no,
                        'created_at' => date('y-m-d h:i:s'),
                        'updated_at' => date('y-m-d h:i:s'),
                    ]);
                }
            }
            else if(is_string($key) and str_contains($key, 'new')) {
                DB::table('task')->insert([
                    'production_schema_id' => $schema_id,
                    'name' => $task['name'],
                    'description' => $task['description'],
                    'sequence_no' => $task['sequence_no'],
                    'amount_required' => $am_req,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            }
            else {
                throw new Exception('Error updating production_schema: error occurred in ProdSchema->updateTasks method. Incorrect key occurred in $task_arr array.');
            }
        }

        foreach ($id_map as $real_task_id => $distinct_task_id) {
            Task::where('id', $real_task_id)->delete();
        }
    }
    /**
     * @throws Exception
     */
    private function insertProdSchemaProdStd(int $schema_id, float $amount, float $duration, string $unit, string $employee_no ): void
    {

        $unit_id = Unit::where('unit',$unit)->select('id')->first();
        if($unit_id instanceof Unit) {
            $unit_id = $unit_id->id;
        }
        else {
            throw new Exception('Error inserting production_schema: error occurred in ProdSchema->insertProdSchemaProdStd method. Id not found for provided unit.');
        }
        DB::table('production_standard')->insert([
            'production_schema_id' => $schema_id,
            'name' => '',
            'duration_hours' => $duration,
            'amount' =>$amount,
            'unit_id' => $unit_id,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

    /**
     * @throws Exception
     */
    private function updateProdSchemaProdStd(int $schema_id, float|null $amount,float|null $duration,string|null $unit, $employee_no, bool $non_countable ): void
    {
        $unit_id = Unit::where('unit',$unit)->select('id')->first();
        if($unit_id instanceof Unit) {
            $unit_id = $unit_id->id;
        }
        else {
            throw new Exception('Error inserting production_schema: error occurred in ProdSchema->insertProdSchemaProdStd method. Id not found for provided unit.');
        }
        if((is_null($duration) and is_null($amount)) or $non_countable) {
            if(ProductionStandard::where('production_schema_id', $schema_id)->first() instanceof ProductionStandard) {
                ProductionStandard::where('production_schema_id', $schema_id)->delete();
            }
        } else {
            $duration = floatval($duration);
            $amount = floatval($amount);
            if(ProductionStandard::where('production_schema_id', $schema_id)->first() instanceof ProductionStandard) {
                DB::table('production_standard')
                    ->where('production_schema_id', $schema_id)
                    ->update([
                    'duration_hours' => $duration,
                    'amount' =>$amount,
                    'unit_id' => $unit_id,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            } else {
                DB::table('production_standard')->insert([
                    'production_schema_id' => $schema_id,
                    'duration_hours' => $duration,
                    'amount' =>$amount,
                    'unit_id' => $unit_id,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            }
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

    private function validateAddSchemaForm(Request $request, string $action) : void
    {
        $units = Unit::all();

        $err_mess = '';
        $unit_in = 'in:';
        foreach ($units as $unit) {
            $unit_in .= $unit->unit.',';
            $err_mess .= $unit->unit.' ,';
        }
        $unit_in = rtrim($unit_in,',');
        $err_mess = rtrim($err_mess,',');


        $ext_instr_pdf = empty($request->file('instr_pdf')) ? '' : $request->file('instr_pdf')->extension();
        $ext_instr_video = empty($request->file('instr_video')) ? '' : $request->file('instr_video')->extension();

        $prod_schema_rules = ['required', 'string',  'min:1','max:100'];
        if($action == 'INSERT') {
            $prod_schema_rules[] =  'unique:'.ProductionSchema::class;
        }

        $request->validate([
            'production_schema' => $prod_schema_rules,
            'amount' => ['nullable', 'gt:0'],
            'duration' => ['nullable', 'gt:0'],
            'unit' => ['nullable', $unit_in],
            'instr_pdf' => ['mimes:pdf,docx', 'max:16384'],
            'instr_video' => ['mimes:mp4,mov,mkv,wmv', 'max:51300'],
            'description' => ['max:200'],
        ],
            [
                'production_schema.unique' => 'Nazwa zadania musi być unikalna.',
                'amount.gt' => 'Ilość musi być większa od 0',
                'duration.gt' => 'Czas trwania musi być większy od 0',
                'unit.in' => 'Niepoprawna jednostka. Wybierz jedną z: '.$err_mess,
                'instr_pdf.mimes' => 'Przesłany plik powinien mieć rozszerzenie: pdf. Rozszerzenie pliku: '.$ext_instr_pdf.'.',
                'instr_video.mimes' => 'Przesłany plik powinien mieć rozszerzenie: mp4,mov,mkv,wmv. Rozszerzenie pliku: '.$ext_instr_video.'.',
                'instr_pdf.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_video.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 50 MB.',
                'required' => 'To pole jest wymagane.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'min' => 'Wpisany tekst ma za mało znaków.',
            ]);
    }

}
