<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Echographie;
use LogActivity;
use PatientAccess;
use Auth;

class EchographieController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Echographie data isn\'t available'], 404);
        }

        $echographie = Echographie::where('patient_id', $patient)->get();
        return ['data' =>  $echographie, 'permission' => $permission];
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Echographie data'], 404);
        }

        $patient = Patient::find($patient);
        $echographie        = new Echographie;
        $echographie->date  = $request->date;
        $echographie->value = $request->value;
        $patient->echographies()->save($echographie);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Echographie data '.' with date: '. $echographie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Echographie stored', 'data' => $echographie]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $echographie)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Echographie data isn\'t available'], 404);
        }

        $echographie = Echographie::find($echographie);
        return ['data' =>  $echographie, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $echographie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Echographie data'], 404);
        }

        $echographie = Echographie::find($echographie);
        $echographie->date  = $request->date    ?? $echographie->date;
        $echographie->value = $request->value   ?? $echographie->value;
        $echographie->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Echographie data '.' with date: '. $echographie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Echographie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $echographie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Echographie data'], 404);
        }

        $echographie = Echographie::find($echographie);
        $echographie->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Echographie data '.' with date: '. $echographie->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Echographie deleted']);
    }
}
