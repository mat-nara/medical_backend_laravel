<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\AutreBiologie;
use LogActivity;
use PatientAccess;
use Auth;

class AutreBiologieController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient AutreBiologie data isn\'t available'], 404);
        }

        $autre_biologies = AutreBiologie::where('patient_id', $patient)->get();
        return ['data' =>  $autre_biologies, 'permission' => $permission] ;
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutreBiologie data'], 404);
        }

        $patient = Patient::find($patient);
        $autre_biologie             = new AutreBiologie;
        $autre_biologie->date       = $request->date;
        $autre_biologie->value      = $request->value;
        $autre_biologie->conclusion = $request->conclusion;
        $patient->biochimies()->save($autre_biologie);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new AutreBiologie data '.' with date: '. $autre_biologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreBiologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $autre_biologie)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient AutreBiologie data isn\'t available'], 404);
        }

        $autre_biologie = AutreBiologie::find($autre_biologie);
        return ['data' =>  $autre_biologie, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $autre_biologie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutreBiologie data'], 404);
        }

        $autre_biologie = AutreBiologie::find($autre_biologie);
        $autre_biologie->date       = $request->date        ?? $autre_biologie->date;
        $autre_biologie->value      = $request->value       ?? $autre_biologie->value;
        $autre_biologie->conclusion = $request->conclusion  ?? $autre_biologie->conclusion;
        $autre_biologie->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the AutreBiologie data '.' with date: '. $autre_biologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreBiologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $autre_biologie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s AutreBiologie data'], 404);
        }

        $autre_biologie = AutreBiologie::find($autre_biologie);
        $autre_biologie->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the AutreBiologie data '.' with date: '. $autre_biologie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'AutreBiologie deleted']);
    }
}
