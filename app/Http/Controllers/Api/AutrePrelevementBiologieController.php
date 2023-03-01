<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AutrePrelevementBiologie;
use App\Models\Patient;

class AutrePrelevementBiologieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $autres_prelevements_biologies = AutrePrelevementBiologie::where('patient_id', $patient)->get();
        return $autres_prelevements_biologies;
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

        $autre_prelevement        = new AutrePrelevementBiologie;
        $autre_prelevement->date  = $request->date;
        $autre_prelevement->value = $request->value;
        $patient->autres_prelevements_biologies()->save($autre_prelevement);

        return response(['error' => 0, 'message' => 'Autre prelevement biologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $autre_prelevement)
    {
        return AutrePrelevementBiologie::find($autre_prelevement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $autre_prelevement)
    {
        $autre_prelevement = AutrePrelevementBiologie::find($autre_prelevement);
        
        $autre_prelevement->date  = $request->date    ?? $autre_prelevement->date;
        $autre_prelevement->value = $request->value   ?? $autre_prelevement->value;

        $autre_prelevement->update();
        return response(['error' => 0, 'message' => 'AutrePrelevementBiologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $autre_prelevement)
    {
        $autre_prelevement = AutrePrelevementBiologie::find($autre_prelevement);
        $autre_prelevement->delete();
        return response(['error' => 0, 'message' => 'AutrePrelevementBiologie deleted']);
    }
}
