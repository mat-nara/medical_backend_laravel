<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AutrePrelevementBiologie;
use App\Models\Patient;
use LogActivity;
use PatientAccess;
use Auth;

class AutrePrelevementBiologieController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient AutrePrelevementBiologie data isn\'t available'], 404);
        }

        $autres_prelevements_biologies = AutrePrelevementBiologie::where('patient_id', $patient)->get();
        return ['data' =>  $autres_prelevements_biologies, 'permission' => $permission];
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutrePrelevementBiologie data'], 404);
        }

        $patient = Patient::find($patient);
        $autre_prelevement              = new AutrePrelevementBiologie;
        $autre_prelevement->date        = $request->date;
        $autre_prelevement->value       = $request->value;
        $autre_prelevement->conclusion  = $request->conclusion;
        $patient->autres_prelevements_biologies()->save($autre_prelevement);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new AutrePrelevementBiologie data '.' with date: '. $autre_prelevement->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Autre prelevement biologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $autre_prelevement)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient AutrePrelevementBiologie data isn\'t available'], 404);
        }

        $autre_prelevement = AutrePrelevementBiologie::find($autre_prelevement);
        return ['data' =>  $autre_prelevement, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $autre_prelevement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutrePrelevementBiologie data'], 404);
        }

        $autre_prelevement = AutrePrelevementBiologie::find($autre_prelevement);
        $autre_prelevement->date        = $request->date        ?? $autre_prelevement->date;
        $autre_prelevement->value       = $request->value       ?? $autre_prelevement->value;
        $autre_prelevement->conclusion  = $request->conclusion  ?? $autre_prelevement->conclusion;
        $autre_prelevement->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the AutrePrelevementBiologie data '.' with date: '. $autre_prelevement->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutrePrelevementBiologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $autre_prelevement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutrePrelevementBiologie data'], 404);
        }

        $autre_prelevement = AutrePrelevementBiologie::find($autre_prelevement);
        $autre_prelevement->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the AutrePrelevementBiologie data '.' with date: '. $autre_prelevement->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutrePrelevementBiologie deleted']);
    }
}
