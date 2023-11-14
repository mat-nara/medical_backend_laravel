<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\ExamenFonctionnel;
use LogActivity;
use Auth;

class ExamenFonctionnelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return ExamenFonctionnel::where('patient_id', $patient)->get();
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

        $examen_fonctionnel        = new ExamenFonctionnel;
        $examen_fonctionnel->date  = $request->date;
        $examen_fonctionnel->value = $request->value;

        $patient->examens_fonctionnels()->save($examen_fonctionnel);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new ExamenFonctionnel data '.' with date: '. $examen_fonctionnel->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'ExamenFonctionnel stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $examen_fonctionnel)
    {
        return ExamenFonctionnel::find($examen_fonctionnel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $examen_fonctionnel)
    {
        $examen_fonctionnel = ExamenFonctionnel::find($examen_fonctionnel);
        
        $examen_fonctionnel->date  = $request->date    ?? $examen_fonctionnel->date;
        $examen_fonctionnel->value = $request->value   ?? $examen_fonctionnel->value;

        $examen_fonctionnel->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the ExamenFonctionnel data '.' with date: '. $examen_fonctionnel->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'ExamenFonctionnel updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $examen_fonctionnel)
    {
        $examen_fonctionnel = ExamenFonctionnel::find($examen_fonctionnel);
        $examen_fonctionnel->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the ExamenFonctionnel data '.' with date: '. $examen_fonctionnel->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'ExamenFonctionnel deleted']);
    }
}
