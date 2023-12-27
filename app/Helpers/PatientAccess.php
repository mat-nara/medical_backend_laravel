<?php
namespace App\Helpers;
use Request;
use App\Models\Patient;
use Auth; 

class PatientAccess
{
    public static function viewPermission($patient)
    {
        $patient = Patient::find($patient);
        if (! $patient) {
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $loggedInUser   = Auth::user();
        $permission     = 'unauthorized';

        //Grant write access to Super admin
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            $permission = 'write';
            return $permission; 
        }

        //Grant write access to Admin
        if($loggedInUser->roles[0]->slug == 'admin'){

            $hopital_id_user    = $loggedInUser->service->hopital->id;
            $hopital_id_patient = $patient->service->hopital_id;

            if($hopital_id_user == $hopital_id_patient){
                $permission = 'write';
                return $permission; 
            }
        }

        //For Owners user
        $patient    =  $loggedInUser->patients()->where('patients.id', $patient->id)->first();
        if($patient){
            $permission = $patient->pivot->permission; 
        }

        return $permission;
    }
}