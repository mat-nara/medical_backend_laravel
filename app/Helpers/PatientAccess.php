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

        $hopital_id_user    = $loggedInUser->service->hopital->id;
        $hopital_id_patient = $patient->service->hopital_id;

        //Grant write access to Admin on same hospital
        if($loggedInUser->roles[0]->slug == 'admin'){
            if($hopital_id_user == $hopital_id_patient){
                $permission = 'write';
                return $permission; 
            }
        }

        //For Owners user, grant access according to shared access
        $patient    =  $loggedInUser->patients()->where('patients.id', $patient->id)->first();
        if($patient){
            $permission = $patient->pivot->permission; 
            return $permission;
        }

        //Grand read access for user on same hospital
        if($hopital_id_user == $hopital_id_patient){
            $permission = 'read';
            return $permission; 
        }

        return $permission;
    }
}