<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Antibiogramme;
use LogActivity;
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
        $antibiogrammes = Antibiogramme::where('patient_id', $patient)->get();
        return $antibiogrammes;
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

        $antibiogramme        = new Antibiogramme;
        $antibiogramme->date  = $request->date;
        $antibiogramme->value = $request->value;
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
        return Antibiogramme::find($antibiogramme);
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
        $antibiogramme = Antibiogramme::find($antibiogramme);
        
        $antibiogramme->date  = $request->date    ?? $antibiogramme->date;
        $antibiogramme->value = $request->value   ?? $antibiogramme->value;

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
        $antibiogramme = Antibiogramme::find($antibiogramme);
        $antibiogramme->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Antibiogram data '.' with date: '. $antibiogramme->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Antibiogramme deleted']);
    }
}
