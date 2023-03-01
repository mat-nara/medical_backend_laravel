<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Echographie;

class EchographieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return Echographie::where('patient_id', $patient)->get();
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

        $echographie        = new Echographie;
        $echographie->date  = $request->date;
        $echographie->value = $request->value;

        $patient->echographies()->save($echographie);

        return response(['error' => 0, 'message' => 'Echographie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $echographie)
    {
        return Echographie::find($echographie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $echographie)
    {
        $echographie = Echographie::find($echographie);
        
        $echographie->date  = $request->date    ?? $echographie->date;
        $echographie->value = $request->value   ?? $echographie->value;

        $echographie->update();
        return response(['error' => 0, 'message' => 'Echographie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $echographie)
    {
        $echographie = Echographie::find($echographie);
        $echographie->delete();
        return response(['error' => 0, 'message' => 'Echographie deleted']);
    }
}
