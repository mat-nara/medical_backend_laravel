<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Traitement;
use LogActivity;
use Auth;

class TraitementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return Traitement::where('patient_id', $patient)->get();
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
        
        $traitement = new Traitement;
        $traitement->value = $request->value;
        //$traitement->name        = $request->name;
        //$traitement->DCI         = $request->DCI;
        //$traitement->forme       = $request->forme;
        //$traitement->posologie   = $request->posologie;
        //$traitement->suivi_prise = $request->suivi_prise;

        $patient->traitements()->save($traitement);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Traitement data for the patient: '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $traitement)
    {
        return Traitement::find($traitement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $traitement)
    {
        $traitement = Traitement::find($traitement);
        $traitement->value  = $request->value ?? $traitement->value;
     
        $traitement->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Traitement data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $traitement)
    {
        $traitement = Traitement::find($traitement);
        $traitement->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Traitement data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement deleted']);
    }
}
