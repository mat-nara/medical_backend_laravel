<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Biochimie;
use LogActivity;
use PatientAccess;
use Auth;

class BiochimieController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Biochimie data isn\'t available'], 404);
        }

        $biochimies = Biochimie::where('patient_id', $patient)->get();
        return ['data' =>  $biochimies, 'permission' => $permission] ;
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Biochimie data'], 404);
        }

        $patient = Patient::find($patient);
        $biochimie              = new Biochimie;
        $biochimie->date        = $request->date;
        $biochimie->value       = $request->value;
        $biochimie->conclusion  = $request->conclusion;
        $patient->biochimies()->save($biochimie);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Biochimie data '.' with date: '. $biochimie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Biochimie stored', 'data' => $biochimie]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $biochimie)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Hematologie data isn\'t available'], 404);
        }

        $biochimie =  Biochimie::find($biochimie);
        return ['data' =>  $biochimie, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $biochimie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Biochimie data'], 404);
        }

        $biochimie = Biochimie::find($biochimie);
        $biochimie->date        = $request->date        ?? $biochimie->date;
        $biochimie->value       = $request->value       ?? $biochimie->value;
        $biochimie->conclusion  = $request->conclusion  ?? $biochimie->conclusion;
        $biochimie->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Biochimie data '.' with date: '. $biochimie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Biochimie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $biochimie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Biochimie data'], 404);
        }

        $biochimie = Biochimie::find($biochimie);
        $biochimie->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Biochimie data '.' with date: '. $biochimie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Biochimie deleted']);
    }
}
