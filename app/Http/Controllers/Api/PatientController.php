<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Observation;



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
    public function index()
    {
        return Patient::all();
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

        return response(['error' => 0, 'message' => 'Patient has been created successfully', 'data' => $patient], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        return $patient;
    }

    /**
     * Display Patient with observation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_with_observation(Patient $patient)
    {
        if (! $patient) {
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist'], 404);
        }

        $patient->observation = $patient->observation;
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
        $patient->delete();
        return response(['error' => 0, 'message' => 'Patient deleted']);
    }
}
