<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biologie;
use App\Models\Patient;

class BiologieController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient_id)
    {
        $patient = Patient::find($patient_id);

        if(!$patient){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $biologie = new Biologie();
          
        //$biologie->hematologie   = $request->hematologie;                              
        //$biologie->biochimie     = $request->biochimie;                      
        //$biologie->autres        = $request->autres;                  
        //$biologie->urine         = $request->urine;                       
        //$biologie->autre_type    = $request->autre_type;                      
        //$biologie->antibiogramme = $request->antibiogramme;                   

        $patient->biologie()->save($biologie);

        return response(['error' => 0, 'message' => 'Biologie has been updated successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient_id)
    {
        $patient = Patient::find($patient_id);

        if (! $patient) {
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $patient->biologie = $patient->biologie;
        return $patient;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::find($patient_id);

        if(!$patient){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $biologie = $patient->biologie;

        //$biologie->hematologie      = $request->hematologie     ?? $biologie->hematologie;
        //$biologie->biochimie        = $request->biochimie       ?? $biologie->biochimie;
        //$biologie->autres           = $request->autres          ?? $biologie->autres;
        //$biologie->urine            = $request->urine           ?? $biologie->urine;
        //$biologie->autre_type       = $request->autre_type      ?? $biologie->autre_type;
        //$biologie->antibiogramme    = $request->antibiogramme   ?? $biologie->antibiogramme;

        $biologie->update();

        return response(['error' => 0, 'message' => 'Patient updated']);
    }
}
