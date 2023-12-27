<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Hematologie;
use LogActivity;
use PatientAccess;
use Auth;

class HematologieController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Hematologie data isn\'t available'], 404);
        }

        $hematologies = Hematologie::where('patient_id', $patient)->get();
        return ['data' =>  $hematologies, 'permission' => $permission] ;
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Hematologie data'], 404);
        }

        $patient = Patient::find($patient);
        $hematologie                = new Hematologie;
        $hematologie->date          = $request->date;
        $hematologie->value         = $request->value;
        $hematologie->conclusion    = $request->conclusion;
        $patient->hematologies()->save($hematologie);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Hematologie data '.' with date: '. $hematologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Hematologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $hematologie)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Hematologie data isn\'t available'], 404);
        }

        $hematologie = Hematologie::find($hematologie);
        return ['data' =>  $hematologie, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $hematologie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Hematologie data'], 404);
        }

        $hematologie = Hematologie::find($hematologie);
        $hematologie->date          = $request->date        ?? $hematologie->date;
        $hematologie->value         = $request->value       ?? $hematologie->value;
        $hematologie->conclusion    = $request->conclusion  ?? $hematologie->conclusion;
        $hematologie->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Hematologie data '.' with date: '. $hematologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Hematologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $hematologie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Hematologie data'], 404);
        }

        $hematologie = Hematologie::find($hematologie);
        $hematologie->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Hematologie data '.' with date: '. $hematologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Hematologie deleted']);
    }
}
