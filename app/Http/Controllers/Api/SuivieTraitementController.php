<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SuivieTraitement;
use App\Models\Traitement;
use App\Models\Patient;
use LogActivity;
use PatientAccess;
use Auth;



class SuivieTraitementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient, $traitement, $date)
    {
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Traitements data isn\'t available'], 404);
        }

        $date               = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
        $suivie_traitements = SuivieTraitement::where('traitement_id', $traitement)
                                                ->where('date', $date)
                                                ->join('users', 'users.id', '=', 'suivie_traitements.user_id')
                                                ->select('suivie_traitements.*', 'users.name as provider')
                                                ->get();
        return ['data' =>  $suivie_traitements, 'permission' => $permission] ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient, $traitement)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to create suivie Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }


        $suivie_traitement = new SuivieTraitement;

        $suivie_traitement->traitement_id   = $traitement;
        $suivie_traitement->user_id         = $request->user()->id;
        $suivie_traitement->date            = $request->date;
        $suivie_traitement->heure           = $request->heure;
        $suivie_traitement->heure_finale    = $request->heure_finale;
        $suivie_traitement->commentaire     = $request->commentaire;

        $suivie_traitement->save();

        $loggedInUser = $request->user();
        $patient = Patient::find($patient);
        LogActivity::addToLog($loggedInUser->name . ' create new Suivie Traitement data for the patient: '. $patient->nom . '  ' . $patient->prenoms.'. Details of treatment: '.$suivie_traitement->traitement->name.' at: '.$suivie_traitement->date.' '.$suivie_traitement->heure. ' - '.$suivie_traitement->heure_finale );
        return response(['error' => 0, 'message' => 'Suivie Traitement stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $traitement, $suivie)
    {
        return SuivieTraitement::find($suivie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $traitement, $suivie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update suivie Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        $suivie_traitement = SuivieTraitement::find($suivie);

        $suivie_traitement->date            = $request->date            ?? $suivie_traitement->date;
        $suivie_traitement->heure           = $request->heure           ?? $suivie_traitement->heure;
        $suivie_traitement->heure_finale    = $request->heure_finale    ?? $suivie_traitement->heure_finale;
        $suivie_traitement->commentaire     = $request->commentaire     ?? $suivie_traitement->commentaire;

        $suivie_traitement->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $traitement, $suivie)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to delete suivie Traitement data'], 404);
        }

        //Check for password 
        $user = Auth::user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Mot de passe incorrecte!"], 401);
        }

        $suivie_traitement = SuivieTraitement::find($suivie);
        $suivie_traitement->delete();
    }
}
