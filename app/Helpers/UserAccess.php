<?php
namespace App\Helpers;
use Request;
use App\Models\User;
use Auth; 

class UserAccess
{
    public static function viewPermission($user)
    {
        $user = User::find($user);
        if (! $user) {
            return response(['error' => 1, 'message' => 'User doesn\'t exist'], 404);
        }

        $loggedInUser   = Auth::user();
        $permission     = 'unauthorized';

        //Grant write access to Super admin
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            $permission = 'write';
            return $permission; 
        }

        $hopital_id_loggedInUser    = $loggedInUser->service->hopital->id;
        $hopital_id_user            = $user->service->hopital_id;

        //Grant write access to Admin on same hospital
        if($loggedInUser->roles[0]->slug == 'admin'){
            if($hopital_id_loggedInUser == $hopital_id_user){
                $permission = 'write';
                return $permission; 
            }
        }

        return $permission;
    }
}