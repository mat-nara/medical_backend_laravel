<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Observation;
use LogActivity;
use PatientAccess;
use Auth;
use DB;
use Carbon\Carbon;


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
        $key_word       = $request->key_word;
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $row_number_max = $request->row_number_max;

        $loggedInUser = $request->user();
        $query = Patient::query();

        if($loggedInUser->roles[0]->slug == 'super_admin'){
            // SUPER ADMIN: can view all patient
        }else{
            // OTHER USER: can view all patient on the same hospital but readonly (Only authorized patient)
            $hopital_id = $loggedInUser->service->hopital->id;

            //Patient::whereHas('service', function($query) { $query->where('hopital_id', 2); } )->get()

            $query->whereHas('service', function ($query) use ($hopital_id) {
                $query->where('hopital_id', $hopital_id);
            });
        }
       
        if ($key_word !== null && $key_word != '') {
            $query->where('nom', 'like', '%'.$key_word.'%');
            $query->orWhere('prenoms', 'like', '%'.$key_word.'%');
        }

        $query->whereBetween('date_entree',     [$request->start_date, $request->end_date]);
        $query->orWhereBetween('date_sortie',   [$request->start_date, $request->end_date]);
        $query->orderBy('created_at', 'desc');
        $query->limit($request->row_number_max);
        
        $patients = $query->get();
        return $patients;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id'    => 'required',
            'nom'           => 'required',
            'prenoms'       => 'required',
            'date_entree'   => 'required',
        ]);

        $patient = new Patient();

        #$patient->id                        = $this->generate_id();
        $patient->service_id                = $request->service_id;
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
        $patient->heure_entree              = $request->heure_entree;
        $patient->heure_sortie              = $request->heure_sortie;            
        $patient->motif_entree              = $request->motif_entree;   

        if($request->date_entree){
            $patient->date_entree           = $request->date_entree;       
        } 
        
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
        $permission = PatientAccess::viewPermission($patient);

        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you don\'t have access'], 404);
        }

        $patient = Patient::find($patient);
        return ['data' => $patient, 'permission' => $permission];
    }

    /**
     * Display Patient with observation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_with_observation($patient)
    {
        $loggedInUser   = Auth::user();
        $permission     = PatientAccess::viewPermission($patient);

        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you don\'t have access'], 404);
        }

        $patient = Patient::with('observation')->find($patient);
        LogActivity::addToLog($loggedInUser->name . ' viewed Patient '. $patient->nom.' '. $patient->prenoms. '\'s information');

        return ['data' => $patient, 'permission' => $permission];;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient)
    {
        $loggedInUser   = $request->user();
        $permission     = PatientAccess::viewPermission($patient);

        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update this patient'], 404);
        }

        $patient = Patient::find($patient);

        $patient->service_id                = $request->service_id              ?? $patient->service_id;
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
        $patient->heure_entree              = $request->heure_entree            ?? $patient->heure_entree;
        $patient->heure_sortie              = $request->heure_sortie            ?? $patient->heure_sortie;            
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
        
        LogActivity::addToLog($loggedInUser->name . ' update Patient '.' with name: '. $patient->nom);
        return response(['error' => 0, 'message' => 'Patient updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient)
    {
        $loggedInUser   = $request->user();
        $permission     = PatientAccess::viewPermission($patient);

        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update this patient'], 404);
        }

        $patient = Patient::find($patient);
        $patient->delete();

        LogActivity::addToLog($user->name . ' deleted Patient '.' with name: '. $patient->nom);
        return response(['error' => 0, 'message' => 'Patient deleted']);
    }
}
