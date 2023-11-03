<?php

namespace App\Helpers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait saveFile
{
    public static function saveFile(UploadedFile $file, $path): string
    {
        $file_name = $file->getClientOriginalName();

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

        return 'DirectoryNotFound';
    }


}
