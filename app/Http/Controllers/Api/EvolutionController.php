<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Evolution;
use LogActivity;
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
        return Evolution::where('patient_id', $patient)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient)
    {
        $patient = Patient::find($patient);
        if(!$patient){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $evolution                  = new Evolution;

        $evolution->date            = $request->date;
        $evolution->indicateur      = $request->indicateur;
        $evolution->cliniques       = $request->cliniques;
        $evolution->paracliniques   = $request->paracliniques;
        $evolution->traitement      = $request->traitement;
        $evolution->avis            = $request->avis;
        $evolution->conclusion      = $request->conclusion;

        $patient->evolutions()->save($evolution);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find_by_date($patient, $date)
    {
        $array_date     = explode('-', $date);
        $ISO_str_date   = $array_date[2].'-'.$array_date[1].'-'.$array_date[0];  
        return Evolution::where('patient_id', $patient)
                        ->where('date', $ISO_str_date)
                        ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $evolution)
    {
        return Evolution::find($evolution);
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
        $evolution = Evolution::find($evolution);

        $evolution->date            = $request->date            ?? $evolution->date;
        $evolution->indicateur      = $request->indicateur      ?? $evolution->indicateur;
        $evolution->cliniques       = $request->cliniques       ?? $evolution->cliniques;
        $evolution->paracliniques   = $request->paracliniques   ?? $evolution->paracliniques;
        $evolution->traitement      = $request->traitement      ?? $evolution->traitement;
        $evolution->avis            = $request->avis            ?? $evolution->avis;
        $evolution->conclusion      = $request->conclusion      ?? $evolution->conclusion;
     
        $evolution->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
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
        $evolution = Evolution::find($evolution);
        $evolution->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution deleted']);
    }
}
