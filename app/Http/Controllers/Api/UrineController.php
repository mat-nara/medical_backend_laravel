<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Urine;
use App\Models\Patient;
use LogActivity;
use PatientAccess;
use Auth;

class UrineController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Urine data isn\'t available'], 404);
        }

        $urines = Urine::where('patient_id', $patient)->get();
        return ['data' =>  $urines, 'permission' => $permission];
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Urine data'], 404);
        }

        $patient = Patient::find($patient);
        $urine              = new Urine;
        $urine->date        = $request->date;
        $urine->value       = $request->value;
        $urine->conclusion  = $request->conclusion;
        $patient->urines()->save($urine);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Urine data '.' with date: '. $urine->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Urine stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $urine)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Urine data isn\'t available'], 404);
        }

        $urine = Urine::find($urine);
        return ['data' =>  $urine, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $urine)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Urine data'], 404);
        }

        $urine = Urine::find($urine);
        $urine->date        = $request->date        ?? $urine->date;
        $urine->value       = $request->value       ?? $urine->value;
        $urine->conclusion  = $request->conclusion  ?? $urine->conclusion;
        $urine->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Urine data '.' with date: '. $urine->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Urine updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $urine)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Urine data'], 404);
        }

        $urine = Urine::find($urine);
        $urine->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Urine data '.' with date: '. $urine->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Urine deleted']);
    }
}
