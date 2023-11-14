<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Observation;
use LogActivity;
use Auth;


class PatientController extends Controller
{

    /**
     * Generate Identification for new patient.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate_id()
    {
        $max_id = Patient::max('id');
        if($max_id){
            return intval($max_id) + 1;
        }
        return 1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUser = $request->user();

        // SUPER ADMIN: can view all patient
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            return Patient::all();
        }

        // OTHER USER: Only authorized patient
        return $loggedInUser->patients()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $patient = new Patient();

        #$patient->id                        = $this->generate_id();
        $patient->n_dossier                 = $request->n_dossier;              
        $patient->n_bulletin                = $request->n_bulletin;             
        $patient->nom                       = $request->nom;                    
        $patient->prenoms                   = $request->prenoms;                
        $patient->genre                     = $request->genre;                  
        $patient->dob                       = $request->dob;                    
        $patient->age                       = $request->age;                    
        $patient->lieu_dob                  = $request->lieu_dob;               
        $patient->status                    = $request->status;                 
        $patient->profession                = $request->profession;             
        $patient->adresse                   = $request->adresse;                
        $patient->ville                     = $request->ville;                  
        $patient->telephone                 = $request->telephone;              
        $patient->personne_en_charge        = $request->personne_en_charge;     
        $patient->contact_pers_en_charge    = $request->contact_pers_en_charge; 
        $patient->date_entree               = $request->date_entree;        
        $patient->date_sortie               = $request->date_sortie;            
        $patient->motif_entree              = $request->motif_entree;   
        
        $patient->save();

        $observation = new Observation();
          
        $observation->historique                                = $request->historique;                              
        $observation->antecedent_medical                        = $request->antecedent_medical;                      
        $observation->antecedent_chirurgical                    = $request->antecedent_chirurgical;                  
        $observation->antecedent_gineco                         = $request->antecedent_gineco;                       
        $observation->antecedent_toxique                        = $request->antecedent_toxique;                      
        $observation->antecedent_allergique                     = $request->antecedent_allergique;                   
        $observation->exam_phys_signe_gen                       = $request->exam_phys_signe_gen;                     
        $observation->exam_phys_signe_gen_score_indice          = $request->exam_phys_signe_gen_score_indice;        
        $observation->exam_phys_signe_fonc                      = $request->exam_phys_signe_fonc;                    
        $observation->exam_phys_signe_fonc_score_indice         = $request->exam_phys_signe_fonc_score_indice;       
        $observation->exam_phys_signe_phys_cardio               = $request->exam_phys_signe_phys_cardio;             
        $observation->exam_phys_signe_phys_cardio_score_indice  = $request->exam_phys_signe_phys_cardio_score_indice;
        $observation->exam_phys_signe_phys_pleuro               = $request->exam_phys_signe_phys_pleuro;             
        $observation->exam_phys_signe_phys_pleuro_score_indice  = $request->exam_phys_signe_phys_pleuro_score_indice;
        $observation->exam_phys_signe_phys_neuro                = $request->exam_phys_signe_phys_neuro;              
        $observation->exam_phys_signe_phys_neuro_score_indice   = $request->exam_phys_signe_phys_neuro_score_indice; 
        $observation->exam_phys_signe_phys_abdo                 = $request->exam_phys_signe_phys_abdo;               
        $observation->exam_phys_signe_phys_abdo_score_indice    = $request->exam_phys_signe_phys_abdo_score_indice;  
        $observation->conclusion                                = $request->conclusion;

        $patient->observation()->save($observation);

        //Grant access to all supervisor user
        $user = Auth::user();
        $user->patients()->attach($patient);
        while($user->parent){
            $user = $user->parent;
            $user->patients()->attach($patient);
        }

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Patient '.' with name: '. $patient->nom);
        return response(['error' => 0, 'message' => 'Patient has been created successfully', 'data' => $patient], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient)
    {
        $user       = Auth::user();
        $patient    = $user->patients()->where('patient_id', $patient)->first();

        if(!$patient){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you don\'t have access'], 404);
        }

        return $patient;
    }

    /**
     * Display Patient with observation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_with_observation($patient)
    {
        $user       = Auth::user();
        $patient    = $user->patients()->with('observation')->where('patient_id', $patient)->first();

        if(!$patient){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you don\'t have access'], 404);
        }


        $loggedInUser = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' viewed Patient '. $patient->nom.' '. $patient->prenoms. '\'s information');
        return $patient;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        if (! $patient) {
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $user       = Auth::user();
        $authorized = $user->patients()->where([['patient_id', $patient->id],['permission', 'write']])->first();

        if(!$authorized){
            return response(['error' => 1, 'message' => 'You don\'t have access to update this user'], 404);
        }

        
        $patient->n_dossier                 = $request->n_dossier               ?? $patient->n_dossier;
        $patient->n_bulletin                = $request->n_bulletin              ?? $patient->n_bulletin;
        $patient->nom                       = $request->nom                     ?? $patient->nom;
        $patient->prenoms                   = $request->prenoms                 ?? $patient->prenoms;
        $patient->genre                     = $request->genre                   ?? $patient->genre;
        $patient->dob                       = $request->dob                     ?? $patient->dob;
        $patient->age                       = $request->age                     ?? $patient->age;
        $patient->lieu_dob                  = $request->lieu_dob                ?? $patient->lieu_dob;
        $patient->status                    = $request->status                  ?? $patient->status;
        $patient->profession                = $request->profession              ?? $patient->profession;
        $patient->adresse                   = $request->adresse                 ?? $patient->adresse;
        $patient->ville                     = $request->ville                   ?? $patient->ville;
        $patient->telephone                 = $request->telephone               ?? $patient->telephone;
        $patient->personne_en_charge        = $request->personne_en_charge      ?? $patient->personne_en_charge;
        $patient->contact_pers_en_charge    = $request->contact_pers_en_charge  ?? $patient->contact_pers_en_charge;
        $patient->date_entree               = $request->date_entree             ?? $patient->date_entree;
        $patient->date_sortie               = $request->date_sortie             ?? $patient->date_sortie;
        $patient->motif_entree              = $request->motif_entree            ?? $patient->motif_entree;
        

        $patient->update();

        $observation = $patient->observation;
        
        $observation->historique                                = $request->historique                               ?? $observation->historique;
        $observation->antecedent_medical                        = $request->antecedent_medical                       ?? $observation->antecedent_medical;
        $observation->antecedent_chirurgical                    = $request->antecedent_chirurgical                   ?? $observation->antecedent_chirurgical;
        $observation->antecedent_gineco                         = $request->antecedent_gineco                        ?? $observation->antecedent_gineco;
        $observation->antecedent_toxique                        = $request->antecedent_toxique                       ?? $observation->antecedent_toxique;
        $observation->antecedent_allergique                     = $request->antecedent_allergique                    ?? $observation->antecedent_allergique;
        $observation->exam_phys_signe_gen                       = $request->exam_phys_signe_gen                      ?? $observation->exam_phys_signe_gen;
        $observation->exam_phys_signe_gen_score_indice          = $request->exam_phys_signe_gen_score_indice         ?? $observation->exam_phys_signe_gen_score_indice;
        $observation->exam_phys_signe_fonc                      = $request->exam_phys_signe_fonc                     ?? $observation->exam_phys_signe_fonc;
        $observation->exam_phys_signe_fonc_score_indice         = $request->exam_phys_signe_fonc_score_indice        ?? $observation->exam_phys_signe_fonc_score_indice;
        $observation->exam_phys_signe_phys_cardio               = $request->exam_phys_signe_phys_cardio              ?? $observation->exam_phys_signe_phys_cardio;
        $observation->exam_phys_signe_phys_cardio_score_indice  = $request->exam_phys_signe_phys_cardio_score_indice ?? $observation->exam_phys_signe_phys_cardio_score_indice;
        $observation->exam_phys_signe_phys_pleuro               = $request->exam_phys_signe_phys_pleuro              ?? $observation->exam_phys_signe_phys_pleuro;
        $observation->exam_phys_signe_phys_pleuro_score_indice  = $request->exam_phys_signe_phys_pleuro_score_indice ?? $observation->exam_phys_signe_phys_pleuro_score_indice;
        $observation->exam_phys_signe_phys_neuro                = $request->exam_phys_signe_phys_neuro               ?? $observation->exam_phys_signe_phys_neuro;
        $observation->exam_phys_signe_phys_neuro_score_indice   = $request->exam_phys_signe_phys_neuro_score_indice  ?? $observation->exam_phys_signe_phys_neuro_score_indice;
        $observation->exam_phys_signe_phys_abdo                 = $request->exam_phys_signe_phys_abdo                ?? $observation->exam_phys_signe_phys_abdo;
        $observation->exam_phys_signe_phys_abdo_score_indice    = $request->exam_phys_signe_phys_abdo_score_indice   ?? $observation->exam_phys_signe_phys_abdo_score_indice;
        $observation->conclusion                                = $request->conclusion                               ?? $observation->conclusion;

        $observation->update();

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update Patient '.' with name: '. $patient->nom);
        return response(['error' => 0, 'message' => 'Patient updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {

        $user   = Auth::user();
        if( ! ($user->roles[0]->slug == 'super_admin' || $user->roles[0]->slug == 'admin')){
            $authorized = $user->patients()->where([['patient_id', $patient],['permission', 'write']])->first();
            if(!$authorized){
                return response(['error' => 1, 'message' => 'You don\'t have access to delete this user'], 404);
            }
        }

        $patient->delete();

        LogActivity::addToLog($user->name . ' deleted Patient '.' with name: '. $patient->nom);
        return response(['error' => 0, 'message' => 'Patient deleted']);
    }
}
