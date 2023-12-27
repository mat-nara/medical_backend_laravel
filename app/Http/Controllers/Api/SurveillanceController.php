<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Surveillance;
use LogActivity;
use Auth;


class SurveillanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return Surveillance::where('patient_id', $patient)->orderBy('date', 'asc')->get();
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

        $surveillance               = new Surveillance;

        $surveillance->date         = $request->date;
        $surveillance->indicateurs  = $request->indicateurs;

        $patient->surveillances()->save($surveillance);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Surveillance data '.' with date: '. $surveillance->date.' for the patient: '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance stored', 'surveillance' => $surveillance]);
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
        return Surveillance::where('patient_id', $patient)
                            ->where('date', $ISO_str_date)
                            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $surveillance)
    {
        return Surveillance::find($surveillance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $surveillance)
    {
        $surveillance = Surveillance::find($surveillance);

        $surveillance->date         = $request->date            ?? $surveillance->date;
        $surveillance->indicateurs  = $request->indicateurs     ?? $surveillance->indicateurs;
     
        $surveillance->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Surveillance data '.' with date: '. $surveillance->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $surveillance)
    {
        $surveillance = Surveillance::find($surveillance);
        $surveillance->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Surveillance data '.' with date: '. $surveillance->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance deleted']);
    }
}
