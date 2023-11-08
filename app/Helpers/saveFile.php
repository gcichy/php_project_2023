<?php

namespace App\Helpers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait saveFile
{
    public static function saveFile(UploadedFile $file,string $path,string $file_prefix): string
    {
        $user = Auth::user();

        try {
            $file_name = $file_prefix.$file->getClientOriginalName();
            if(File::isDirectory('storage/'.$path)) {
                if(File::exists('storage/'.$path.'/'.$file_name)) {
                    $pattern = explode('.',$file_name);
                    $pat = '';
                    if(count($pattern) > 0) $pat = $pattern[0];
                    $i = 1;
                    while(File::exists('storage/'.$path.'/'.$file_name)) {
                        $file_name = $pat.'('.$i.')';
                        if(count($pattern) == 2) $file_name .= '.'.$pattern[1];
                        $i += 1;
                    }
                    $file->storeAs('public/'.$path,$file_name);
                } else {
                    $file->storeAs('public/'.$path,$file_name);
                }
                return $file_name;
            }
            Log::channel('error')->error('Error saving file: directory "storage/'.$path.'" has not been found', [
                    'employeeNo' => $user instanceof User ? $user->employeeNo : '',
                ]);
            return '';
        }
        catch(Exception $e) {
            Log::channel('error')->error('Error saving file: '.$e->getMessage(), [
                'employeeNo' => $user instanceof User ? $user->employeeNo : '',
            ]);
            return '';
        }


    }

    public static function deleteFile(string $path,string $file_name): string
    {
        $user = Auth::user();

        try {
            if(File::isDirectory('storage/'.$path)) {
                if(File::exists('storage/'.$path.'/'.$file_name)) {
                    if(!File::delete('storage/'.$path.'/'.$file_name)) {
                        Log::channel('error')->error('Error deleting file: file "'.$file_name.'" has not been deleted for unspecified reason.', [
                            'employeeNo' => $user instanceof User ? $user->employeeNo : '',
                        ]);
                        return false;
                    }
                    return true;
                } else {
                    Log::channel('error')->error('Error deleting file: file "'.$file_name.'" does not exist in specified path: "storage/'.$path.'/'.'".', [
                        'employeeNo' => $user instanceof User ? $user->employeeNo : '',
                    ]);
                }
                return false;
            }
            Log::channel('error')->error('Error deleting file: directory "storage/'.$path.'" has not been found.', [
                'employeeNo' => $user instanceof User ? $user->employeeNo : '',
            ]);
            return false;
        }
        catch(Exception $e) {
            Log::channel('error')->error('Error deleting file: '.$e->getMessage(), [
                'employeeNo' => $user instanceof User ? $user->employeeNo : '',
            ]);
            return false;
        }


    }


}
