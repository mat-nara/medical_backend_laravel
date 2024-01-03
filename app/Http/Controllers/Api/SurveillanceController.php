<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Surveillance;
use App\Models\Evolution;
use Carbon\Carbon;
use LogActivity;
use Auth;


class SurveillanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        return Surveillance::where('patient_id', $patient)->orderBy('date', 'asc')->get();
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

        $surveillance               = new Surveillance;
        $surveillance->date         = $request->date;
        $surveillance->indicateurs  = $request->indicateurs;
        $patient->surveillances()->save($surveillance);

        //synchronise indicators with evolution
        $indicateurs    = $request->input('indicateurs', []);
        $indicateur     = end($indicateurs);
        $date           = $request->date;
        $this->updateEvolutionIndicators($patient, $date, $indicateur);
        
        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Surveillance data '.' with date: '. $surveillance->date.' for the patient: '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance stored', 'surveillance' => $surveillance]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find_by_date($patient, $date)
    {
        $array_date     = explode('-', $date);
        $ISO_str_date   = $array_date[2].'-'.$array_date[1].'-'.$array_date[0];  
        return Surveillance::where('patient_id', $patient)
                            ->where('date', $ISO_str_date)
                            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $surveillance)
    {
        return Surveillance::find($surveillance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $surveillance)
    {
        $surveillance = Surveillance::find($surveillance);

        $surveillance->date         = $request->date            ?? $surveillance->date;
        $surveillance->indicateurs  = $request->indicateurs     ?? $surveillance->indicateurs;
        $surveillance->update();

        $patient = Patient::find($patient);
        //synchronise indicators with evolution
        $indicateurs    = $request->input('indicateurs', []);
        $indicateur     = end($indicateurs);
        $date           = $request->date;
        $this->updateEvolutionIndicators($patient, $date, $indicateur);

        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Surveillance data '.' with date: '. $surveillance->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $surveillance)
    {
        $surveillance = Surveillance::find($surveillance);
        $surveillance->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Surveillance data '.' with date: '. $surveillance->date. ' for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Surveillance deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateEvolutionIndicators(Patient $patient, $date, $indicateur)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);
        $evolution  = Evolution::where('date', $date->format('Y-m-d') )->first();
        if($evolution){
            $indicateur = [
                "TA_PAD"=>       $indicateur['TA_PAD'], 
                "TA_PAS"=>       $indicateur['TA_PAS'], 
                "FC"=>           $indicateur['FC'], 
                "FR"=>           $indicateur['FR'], 
                "temperature"=>  $indicateur['T'],
                "Diurese"=>      $indicateur['diurese'], 
                "poids"=>        $indicateur['poids'], 
                "IMC"=>          $evolution->indicateur->IMC ?? null, 
                "SpO2"=>         $evolution->indicateur->SpO2 ?? null, 
                "glycemie"=>     $evolution->indicateur->glycemie ?? null
            ];
            $evolution->indicateur = $indicateur;
            $evolution->update();
        }else{
            $indicateur = [
                "TA_PAD"=>       $indicateur['TA_PAD'], 
                "TA_PAS"=>       $indicateur['TA_PAS'], 
                "FC"=>           $indicateur['FC'], 
                "FR"=>           $indicateur['FR'], 
                "temperature"=>  $indicateur['T'],
                "Diurese"=>      $indicateur['diurese'], 
                "poids"=>        $indicateur['poids'], 
                "IMC"=>          null, 
                "SpO2"=>         null,  
                "glycemie"=>     null
                
            ];
            $evolution = new Evolution;
            $evolution->date            = $date;
            $evolution->indicateur      = $indicateur;
            $evolution->cliniques       = [
                                            "neurologique"      => ["valeur"=> null, "score_indice"=> []], 
                                            "signe_generaux"    => ["valeur"=> null, "score_indice"=> []], 
                                            "abdomino_pelvien"  => ["valeur"=> null, "score_indice"=> []], 
                                            "cardiovasculaire"  => ["valeur"=> null, "score_indice"=> []], 
                                            "pleuro_pulmonaire" => ["valeur"=> null, "score_indice"=> []], 
                                            "signe_fonctionnel" => ["valeur"=> null, "score_indice"=> []]
                                        ];
            $evolution->paracliniques   = ["paraclinique_recent"    => null, "paraclinique_en_attente"  => null, "paraclinique_a_prescrire" => null];
            $evolution->traitement      = ["traitement_stop"        => null, "traitement_debut"         => null, "traitement_modification"  => null];
            $evolution->avis            = "";
            $evolution->conclusion      = "";
            $patient->evolutions()->save($evolution);
        }
    }
}
