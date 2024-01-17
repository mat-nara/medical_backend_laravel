<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Evolution;
use App\Models\Patient;
use PatientAccess;
use Auth;
use LogActivity;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient, $evolution)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Evolution data isn\'t available'], 404);
        }

        $loggedInUser = Auth::user();
    
        $notes = [
            'note_user'     => Note::where([['evolution_id', $evolution], ['user_id',  '=', $loggedInUser->id]])->first(),
            'note_autre'    => Note::with('author', 'author.roles', 'author.service')->where([['evolution_id', $evolution], ['user_id', '!=', $loggedInUser->id]])->get()
        ];
        return ['data' =>  $notes, 'permission' => $permission];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient, $evolution)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write' && $permission != 'read'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Note on Evolution data'], 404);
        }

        $evolution      = Evolution::find($evolution);
        $note           = new Note;
        $loggedInUser   = $request->user();

        $note->user_id         = $loggedInUser->id;
        $note->cliniques       = $request->cliniques;
        $note->paracliniques   = $request->paracliniques;
        $note->traitement      = $request->traitement;
        $note->avis            = $request->avis;
        $note->conclusion      = $request->conclusion;

        $evolution->notes()->save($note);
        
        $patient = Patient::find($patient); 
        LogActivity::addToLog($loggedInUser->name . ' create new Note on Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Note stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $evolution, $note)
    {
        //Check for permission access 
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient Evolution data isn\'t available'], 404);
        }

        $note = Note::find($note);
        return ['data' =>  $note, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $evolution, $note)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write' && $permission != 'read'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient Note on Evolution data'], 404);
        }

        $note           = Note::find($note);
        $loggedInUser   = $request->user();

        //Only Author can modify his note
        if($note->user_id != $loggedInUser->id){
            return response(['error' => 1, 'message' => 'Only the author can modify his Note on Evolutions data!'], 404);
        }
        
        $note->cliniques       = $request->cliniques       ?? $note->cliniques;
        $note->paracliniques   = $request->paracliniques   ?? $note->paracliniques;
        $note->traitement      = $request->traitement      ?? $note->traitement;
        $note->avis            = $request->avis            ?? $note->avis;
        $note->conclusion      = $request->conclusion      ?? $note->conclusion;
        $note->update();

        $patient    = Patient::find($patient);
        $evolution  = Evolution::find($evolution);
        LogActivity::addToLog($loggedInUser->name . ' update Note on the Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Note on Evolution updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $evolution, $note)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write' && $permission != 'read'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Note on Evolution data'], 404);
        }

        $note           = Note::find($note);
        $loggedInUser   = $request->user();

        //Only Author can delete his note
        if($note->user_id != $loggedInUser->id){
            return response(['error' => 1, 'message' => 'Only the author can delete his Note on Evolutions data!'], 404);
        }

        $note->delete();

        $patient    = Patient::find($patient);
        $evolution  = Evolution::find($evolution);
        LogActivity::addToLog($loggedInUser->name . ' deleted the Note on Evolution data '.' with date: '. $evolution->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Evolution deleted']);
    }
}
