<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Patient;
use App\Models\Traitement;
use LogActivity;
use PatientAccess;
use Auth;

class TraitementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Traitements data isn\'t available'], 404);
        }

        $traitements = Traitement::with('suivies')
                                    ->where('patient_id', $patient)
                                    ->whereIn('id', function ($query) {
                                        $query->select(DB::raw('MIN(id)'))->from('traitements')->groupBy('code');
                                    })
                                    ->get();
    


        //$traitements = Traitement::with('suivies')->where('patient_id', $patient)->get(); 
        return ['data' =>  $traitements, 'permission' => $permission] ;

        //return Traitement::with('suivies')->where('patient_id', $patient)->latest()->first();
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
            return response(['error' => 1, 'message' => 'Not authorized to create Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        //Store
        $traitement = new Traitement;

        $traitement->patient_id         = $patient;
        $traitement->name               = $request->name;
        $traitement->DCI                = $request->DCI;
        $traitement->forme              = $request->forme;
        $traitement->posologie          = $request->posologie;
        $traitement->prise_journalier   = $request->prise_journalier;
        $traitement->etat               = 'actif';
        $traitement->created_by         = $request->user()->id;
        $traitement->updated_by         = $request->user()->id;

        $traitement->save();

        //set the code
        $traitement->code   = $traitement->id;
        $traitement->update();
        
        //$traitement->value = $request->value;
        //$traitement->suivi_prise = $request->suivi_prise;
        //$patient->traitements()->save($traitement);
        

        $loggedInUser = $request->user();
        $patient = Patient::find($patient);
        LogActivity::addToLog($loggedInUser->name . ' create new Traitement data for the patient: '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $traitement)
    {
        return Traitement::find($traitement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $traitement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        //Update
        $traitement = Traitement::find($traitement);
        //$traitement->value  = $request->value ?? $traitement->value;

        //save log of last traitement
        $log = new Traitement;

        $log->patient_id        = $traitement->patient_id;
        $log->code              = $traitement->code;
        $log->name              = $traitement->name;
        $log->DCI               = $traitement->DCI;
        $log->forme             = $traitement->forme;
        $log->posologie         = $traitement->posologie;
        $log->prise_journalier  = $traitement->prise_journalier;
        $log->etat              = $traitement->etat;
        $log->created_by        = $traitement->created_by;
        $log->created_at        = $traitement->created_at;
        $log->updated_by        = $traitement->updated_by;
        $log->updated_at        = $traitement->updated_at;

        $log->save();


        //update new traitement
        $traitement->code               = $traitement->code;
        $traitement->name               = $request->name                ?? $traitement->name;
        $traitement->DCI                = $request->DCI                 ?? $traitement->DCI;
        $traitement->forme              = $request->forme               ?? $traitement->forme;
        $traitement->posologie          = $request->posologie           ?? $traitement->posologie;
        $traitement->prise_journalier   = $request->prise_journalier    ?? $traitement->prise_journalier;
        $traitement->etat               = $request->etat                ?? $traitement->etat;
        $traitement->created_by         = $traitement->created_by;
        $traitement->created_at         = $traitement->created_at;
        $traitement->updated_by         = $request->user()->id;
     
        $traitement->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Traitement data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $traitement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to delete Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        //Delete
        $traitement = Traitement::find($traitement);
        $traitement->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Traitement data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement deleted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUpdateHistory($patient, $traitement)
    {
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Traitements data isn\'t available'], 404);
        }

        $traitement = Traitement::find($traitement);
        $histories  = Traitement::where('code', $traitement->code)
                                ->where('traitements.id', '!=', $traitement->id)
                                ->leftJoin('users as users_created_by', 'users_created_by.id', '=', 'traitements.created_by')
                                ->leftJoin('users as users_updated_by', 'users_updated_by.id', '=', 'traitements.updated_by')
                                ->select('traitements.*', 'users_created_by.name as creator', 'users_updated_by.name as updater')
                                ->get();
        return  ['data' =>  $histories, 'permission' => $permission] ;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePriseJournalier(Request $request, $patient, $traitement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update Traitement data'], 404);
        }
        
        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        //Action
        $traitement = Traitement::find($traitement);

        $traitement->prise_journalier = $request->prise_journalier ?? $traitement->prise_journalier;
        $traitement->update();
    
        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Prise Journalier of Traitement data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Traitement updated']);
    }

}
