<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Evolution;
use LogActivity;
use PatientAccess;
use Auth;

class EvolutionController extends Controller
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
            return response(['error' => 1, 'message' => 'Patient Evolution data isn\'t available'], 404);
        }

        $evolution = Evolution::where('patient_id', $patient)->get();
        return ['data' =>  $evolution, 'permission' => $permission];
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
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Evolution data'], 404);
        }

        $patient    = Patient::find($patient);
        $evolution  = new Evolution;
        
        $evolution->date        = $request->date;
        $evolution->indicateurs = $request->indicateurs;
        $patient->evolutions()->save($evolution);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution stored', 'data' => $evolution]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find_by_date($patient, $date)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Evolution data isn\'t available'], 404);
        }

        $array_date     = explode('-', $date);
        $ISO_str_date   = $array_date[2].'-'.$array_date[1].'-'.$array_date[0];  
        $evolution      = Evolution::where('patient_id', $patient)
                                    ->where('date', $ISO_str_date)
                                    ->first();
        return ['data' =>  $evolution, 'permission' => $permission];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $evolution)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Evolution data isn\'t available'], 404);
        }

        $evolution = Evolution::find($evolution);
        return ['data' =>  $evolution, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $evolution)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Evolution data'], 404);
        }

        $evolution = Evolution::find($evolution);
        $evolution->indicateurs = $request->indicateurs ?? $evolution->indicateurs;
        $evolution->update();

        $loggedInUser   = $request->user();
        $patient        = Patient::find($patient);
        LogActivity::addToLog($loggedInUser->name . ' update the Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $evolution)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Evolution data'], 404);
        }

        $evolution  = Evolution::find($evolution);
        $evolution->delete();

        $user       = Auth::user();
        $patient    = Patient::find($patient);
        LogActivity::addToLog($user->name . ' deleted the Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution deleted']);
    }
}
