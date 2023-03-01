<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Biochimie;

class BiochimieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $biochimies = Biochimie::where('patient_id', $patient)->get();
        return $biochimies;
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

        $biochimie        = new Biochimie;
        $biochimie->date  = $request->date;
        $biochimie->value = $request->value;
        $patient->biochimies()->save($biochimie);

        return response(['error' => 0, 'message' => 'Biochimie stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $biochimie)
    {
        return Biochimie::find($biochimie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $biochimie)
    {
        $biochimie = Biochimie::find($biochimie);
        
        $biochimie->date  = $request->date    ?? $biochimie->date;
        $biochimie->value = $request->value   ?? $biochimie->value;

        $biochimie->update();
        return response(['error' => 0, 'message' => 'Biochimie updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $biochimie)
    {
        $biochimie = Biochimie::find($biochimie);
        $biochimie->delete();
        return response(['error' => 0, 'message' => 'Biochimie deleted']);
    }
}
