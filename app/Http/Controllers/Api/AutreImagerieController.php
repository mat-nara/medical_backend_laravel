<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\AutreImagerie;
use LogActivity;
use PatientAccess;
use Auth;

class AutreImagerieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient AutresImagerie data isn\'t available'], 404);
        }

        $autre_imagerie = AutreImagerie::where('patient_id', $patient)->get();
        return ['data' =>  $autre_imagerie, 'permission' => $permission];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutresImagerie data'], 404);
        }

        $patient = Patient::find($patient);
        $autre_imagerie        = new AutreImagerie;
        $autre_imagerie->date  = $request->date;
        $autre_imagerie->value = $request->value;
        $patient->autres_imageries()->save($autre_imagerie);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new AutreImagerie data '.' with date: '. $autre_imagerie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreImagerie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $autre_imagerie)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Echographie data isn\'t available'], 404);
        }

        $autre_imagerie = AutreImagerie::find($autre_imagerie);
        return ['data' =>  $autre_imagerie, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $autre_imagerie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Echographie data'], 404);
        }

        $autre_imagerie = AutreImagerie::find($autre_imagerie);
        $autre_imagerie->date  = $request->date    ?? $autre_imagerie->date;
        $autre_imagerie->value = $request->value   ?? $autre_imagerie->value;
        $autre_imagerie->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the AutreImagerie data '.' with date: '. $autre_imagerie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreImagerie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $autre_imagerie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Echographie data'], 404);
        }

        $autre_imagerie = AutreImagerie::find($autre_imagerie);
        $autre_imagerie->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the AutreImagerie data '.' with date: '. $autre_imagerie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreImagerie deleted']);
    }
}
