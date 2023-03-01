<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Radiologie;

class RadiologieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return Radiologie::where('patient_id', $patient)->get();
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

        $radiologie        = new Radiologie;
        $radiologie->date  = $request->date;
        $radiologie->value = $request->value;

        $patient->radiologies()->save($radiologie);

        return response(['error' => 0, 'message' => 'Radiologie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $radiologie)
    {
        return Radiologie::find($radiologie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $radiologie)
    {
        $radiologie = Radiologie::find($radiologie);
        
        $radiologie->date  = $request->date    ?? $radiologie->date;
        $radiologie->value = $request->value   ?? $radiologie->value;

        $radiologie->update();
        return response(['error' => 0, 'message' => 'Radiologie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $radiologie)
    {
        $radiologie = Radiologie::find($radiologie);
        $radiologie->delete();
        return response(['error' => 0, 'message' => 'Radiologie deleted']);
    }
}
