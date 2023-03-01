<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Urine;
use App\Models\Patient;

class UrineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $urines = Urine::where('patient_id', $patient)->get();
        return $urines;
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

        $urine        = new Urine;
        $urine->date  = $request->date;
        $urine->value = $request->value;
        $patient->urines()->save($urine);

        return response(['error' => 0, 'message' => 'Urine stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $urine)
    {
        return Urine::find($urine);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $urine)
    {
        $urine = Urine::find($urine);
        
        $urine->date  = $request->date    ?? $urine->date;
        $urine->value = $request->value   ?? $urine->value;

        $urine->update();
        return response(['error' => 0, 'message' => 'Urine updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $urine)
    {
        $urine = Urine::find($urine);
        $urine->delete();
        return response(['error' => 0, 'message' => 'Urine deleted']);
    }
}
