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

class InstructionController
{
    use HasEnsure;

    public static function insertInstruction(int $id, string $id_column, string $name, string $employee_no, $instr_pdf, $instr_video): array
    {
        $instr_id = DB::table('instruction')->insertGetId([
            $id_column => $id,
            'name' => $name,
            'instruction_pdf' => '',
            'video' => '',
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        $instr_pdf_name =  is_null($instr_pdf) ? null : '';
        if ($instr_pdf instanceof UploadedFile) {
            $instr_pdf_name = fileTrait::saveFile($instr_pdf, 'instructions', 'instr_doc_' . $instr_id . '_');
            //if failed to save instr file
            if (empty($instr_pdf_name)) {
                return array('ERROR' => 'błąd przy zapisie pliku "Instrukcja wykonania komponentu".');
            }
        } else if (is_string($instr_pdf)) {
            $new_instr_pdf_name = fileTrait::getFileName('instructions', $instr_pdf);
            if (!fileTrait::copyFile('instructions', $instr_pdf, 'instructions', $new_instr_pdf_name)) {
                return array('ERROR' => 'błąd przy kopiowaniu pliku "Instrukcja wykonania komponentu".');
            } else {
                $instr_pdf_name = $new_instr_pdf_name;
            }
        }
        else if(is_null($instr_pdf)) {
            if(!empty($instr_old->instruction_pdf) and fileTrait::fileExists('instructions', $instr_old->instruction_pdf)) {
                fileTrait::deleteFile('instructions', $instr_old->instruction_pdf);
            }
            $instr_pdf_name = null;
        }

        $instr_video_name = is_null($instr_video) ? null : '';
        if ($instr_video instanceof UploadedFile) {
            $instr_video_name = fileTrait::saveFile($instr_video, 'instructions', 'instr_vid' . $instr_id . '_');
            //if failed to save comp instr video file
            if (empty($instr_video_name)) {
                return array('ERROR' => 'błąd przy zapisie pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            }
        } else if (is_string($instr_video)) {
            $new_instr_video_name = fileTrait::getFileName('instructions', $instr_video);
            if (!fileTrait::copyFile('instructions', $instr_video, 'instructions', $new_instr_video_name)) {
                return array('ERROR' => 'błąd przy kopiowaniu pliku "Film instruktażowy".',
                    'SAVED_FILES' => [$instr_pdf_name]);
            } else {
                $instr_video_name = $new_instr_video_name;
            }
        }

        $saved_files = [];
        if (!empty($instr_pdf_name)) {
            $saved_files[] = $instr_pdf_name;
        }
        if (!empty($instr_video_name)) {
            $saved_files[] = $instr_video_name;
        }

        if (count($saved_files) > 0) {
            try {
                DB::table('instruction')
                    ->where('id', $instr_id)
                    ->update(['instruction_pdf' => $instr_pdf_name,
                        'video' => $instr_video_name,]);

            } catch (Exception $e) {
                Log::channel('error')->error('Error inserting instruction: ' . $e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'błąd przy zapisie nazwy plików "Instrukcja wykonania komponentu" oraz "Film instruktażowy" w bazie danych.',
                    'SAVED_FILES' => $saved_files);
            }
        }


        return array('SAVED_FILES' => $saved_files);
    }


    public static function updateInstruction(int $id, string $id_column, string $name, string $employee_no, $instr_pdf, $instr_video): array
    {
        $instr_old = Instruction::where($id_column,$id)->get();
        $instr_id = collect($instr_old)->map(function (Instruction $arr) { return $arr->id; })->toArray();

        $instr_old = count($instr_old) == 1 ? $instr_old[0] : null;

        if(count($instr_id) > 0) {
            $instr_id = $instr_id[0];

            DB::table('instruction')
                ->where('id',$instr_id)
                ->update([
                    'name' => $name,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
        }
        else {
            $instr_id = DB::table('instruction')->insertGetId([
                $id_column => $id,
                'name' => $name,
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
                return array('ERROR' => 'błąd przy zapisie pliku "Instrukcja wykonania komponentu" na dysku.');
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
                    return array('ERROR' => 'błąd przy zapisie pliku "Film instruktażowy" na dysku.');
                }
                return array('ERROR' => 'błąd przy zapisie pliku "Film instruktażowy" na dysku.',
                    'SAVED_FILES' => array($instr_pdf_name));
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
}
