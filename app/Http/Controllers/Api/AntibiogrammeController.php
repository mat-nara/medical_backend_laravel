<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Antibiogramme;
use LogActivity;
use PatientAccess;
use Auth;

class AntibiogrammeController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Antibiogramme data isn\'t available'], 404);
        }

        $antibiogrammes = Antibiogramme::where('patient_id', $patient)->get();
        return ['data' =>  $antibiogrammes, 'permission' => $permission];
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Antibiogramme data'], 404);
        }

        $patient = Patient::find($patient);
        $antibiogramme              = new Antibiogramme;
        $antibiogramme->date        = $request->date;
        $antibiogramme->value       = $request->value;
        $antibiogramme->conclusion  = $request->conclusion;
        $patient->biochimies()->save($antibiogramme);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Antibiogram data '.' with date: '. $request->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Antibiogramme stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $antibiogramme)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Antibiogramme data isn\'t available'], 404);
        }

        $antibiogramme = Antibiogramme::find($antibiogramme);
        return ['data' =>  $antibiogramme, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $antibiogramme)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Antibiogramme data'], 404);
        }

        $antibiogramme = Antibiogramme::find($antibiogramme);
        $antibiogramme->date        = $request->date        ?? $antibiogramme->date;
        $antibiogramme->value       = $request->value       ?? $antibiogramme->value;
        $antibiogramme->conclusion  = $request->conclusion  ?? $antibiogramme->conclusion;
        $antibiogramme->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Antibiogram data '.' with date: '. $antibiogramme->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Antibiogramme updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $antibiogramme)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Antibiogramme data'], 404);
        }

        $antibiogramme = Antibiogramme::find($antibiogramme);
        $antibiogramme->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Antibiogram data '.' with date: '. $antibiogramme->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Antibiogramme deleted']);
    }
}
