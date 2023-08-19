<?php

namespace App\Helpers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

/**
 * Provides helpers static analysers.
 */
trait getUserData
{

    public static function getUserData(User $user): array
    {
        $userData = [];
        try {
            if(is_null($user)) {
                throw new Exception("User is not defined");
            }
            $name = (!is_null($user->firstName) ? $user->firstName : '').' '.
                (!is_null($user->lastName) ? $user->lastName : '');
            $userData = [
                'Imię i Nazwisko' => $name,
                'Stanowisko' => !is_null($user->role) ? $user->role : '-',
                'Nazwa Użytkownika' => !is_null($user->employeeNo) ? $user->employeeNo : '-',
                'E-mail' => !is_null($user->email) ? $user->email : '-',
                'Nr Telefonu' => !is_null($user->phoneNr) ? $user->phoneNr : '-',
                'Wynagrodzenie' => !is_null($user->salary) ? $user->salary : '-',
            ];


        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

        return $userData;
    }

    public static function getEditUserData(): array
    {
        return [
            'Imię' => 'firstName',
            'Nazwisko' => 'lastName',
            'Stanowisko' => 'role',
            'Nazwa Użytkownika' => 'employeeNo',
            'Wynagrodzenie' => 'salary',
            'Nr Telefonu' => 'phoneNr',
        ];
    }
}
