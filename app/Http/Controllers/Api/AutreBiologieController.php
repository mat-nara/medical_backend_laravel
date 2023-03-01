<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\AutreBiologie;

class AutreBiologieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $autre_biologies = AutreBiologie::where('patient_id', $patient)->get();
        return $autre_biologies;
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

        $autre_biologie        = new AutreBiologie;
        $autre_biologie->date  = $request->date;
        $autre_biologie->value = $request->value;
        $patient->biochimies()->save($autre_biologie);

        return response(['error' => 0, 'message' => 'AutreBiologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $autre_biologie)
    {
        return AutreBiologie::find($autre_biologie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $autre_biologie)
    {
        $autre_biologie = AutreBiologie::find($autre_biologie);
        
        $autre_biologie->date  = $request->date    ?? $autre_biologie->date;
        $autre_biologie->value = $request->value   ?? $autre_biologie->value;

        $autre_biologie->update();
        return response(['error' => 0, 'message' => 'AutreBiologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $autre_biologie)
    {
        $autre_biologie = AutreBiologie::find($autre_biologie);
        $autre_biologie->delete();
        return response(['error' => 0, 'message' => 'AutreBiologie deleted']);
    }
}
