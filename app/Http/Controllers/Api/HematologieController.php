<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Hematologie;

class HematologieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $hematologies = Hematologie::where('patient_id', $patient)->get();
        return $hematologies;
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

        $hematologie        = new Hematologie;
        $hematologie->date  = $request->date;
        $hematologie->value = $request->value;
        $patient->hematologies()->save($hematologie);

        return response(['error' => 0, 'message' => 'Hematologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $hematologie)
    {
        return Hematologie::find($hematologie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $hematologie)
    {
        $hematologie = Hematologie::find($hematologie);
        
        $hematologie->date  = $request->date    ?? $hematologie->date;
        $hematologie->value = $request->value   ?? $hematologie->value;

        $hematologie->update();
        return response(['error' => 0, 'message' => 'Hematologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $hematologie)
    {
        $hematologie = Hematologie::find($hematologie);
        $hematologie->delete();
        return response(['error' => 0, 'message' => 'Hematologie deleted']);
    }
}
