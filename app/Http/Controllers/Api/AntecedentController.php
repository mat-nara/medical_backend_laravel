<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Antecedent;

class AntecedentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Antecedent data isn\'t available'], 404);
        }

        $antecedents = Antecedent::where('patient_id', $patient)->get();
        return ['data' =>  $antecedent, 'permission' => $permission];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Antibiogramme data'], 404);
        }

        $patient = Patient::find($patient);
        $antecedent = new Antecedent;
        $antecedent->antecedent_medical     = $request->antecedent_medical;
        $antecedent->antecedent_chirurgical = $request->antecedent_chirurgical;
        $antecedent->antecedent_gineco      = $request->antecedent_gineco;
        $patient->antecedent()->save($antecedent);

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Antecedent data '.' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Antecedent stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $antecedent)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Antibiogramme data isn\'t available'], 404);
        }

        $antecedent = Antecedent::find($antibiogramme);
        return ['data' =>  $antecedent, 'permission' => $permission];
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
