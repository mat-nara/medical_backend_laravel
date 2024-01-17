<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Evolution;
use PatientAccess;



class RecapitulationController extends Controller
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
            return response(['error' => 1, 'message' => 'Not authorized to update this patient'], 404);
        }
        
        $recap = Patient::with('observation', 'traitements')->find($patient);
        $recap['antecedents']   = $this->antecedent($recap->observation);
        $recap['evolutions']    = Evolution::where('patient_id', $patient)
                                                //->where('date', date("Y-m-d"))
                                                ->orderBy('date', 'desc')
                                                ->first();

        $recap['biologies']     = $this->biologie($patient);
        $recap['imageries']     = $this->imagerie($patient);

        return $recap; 
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function antecedent($observation)
    {
        $antecedent = array_merge($observation->antecedent_medical, $observation->antecedent_chirurgical, $observation->antecedent_gineco);
        usort($antecedent, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        return $antecedent;
    }

    public function imagerie($patient)
    {
        $patient = Patient::where('id', $patient)
                            ->with('radiologies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('echographies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('autres_imageries', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->get();
        
        if(count($patient) > 0 ){
            
            //Check if no data imagerie information is associated to patient on database
            $patient = $patient[0];
            $imagerie = [
                'radiologies'       => count($patient->radiologies)         > 0 ? $patient->radiologies[0]      : [],
                'echographies'      => count($patient->echographies)        > 0 ? $patient->echographies[0]     : [],
                'autres_imageries'  => count($patient->autres_imageries)    > 0 ? $patient->autres_imageries[0] : []
            ];

            return $imagerie;
        }

        return [
            'radiologies'      =>  null,
            'echographies'     =>  null,
            'autres_imageries' =>  null
        ]; 
        
    }

    public function biologie($patient)
    {
        $patient = Patient::where('id', $patient)
                            ->with('hematologies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('biochimies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('autresbiologies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('urines', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('autres_prelevements_biologies', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->with('antibiogrammes', function($query){
                                $query->orderBy('date', 'desc')->first();
                            })
                            ->get();
        
        if(count($patient) > 0 ){

            //Check if no data biologie information is associated to patient on database
            $patient = $patient[0];
            $biologie = [
                'hematologies'                  => count($patient->hematologies) > 0                    ? $patient->hematologies[0] : [],
                'biochimies'                    => count($patient->biochimies) > 0                      ? $patient->biochimies[0] : [],
                'autresbiologies'               => count($patient->autresbiologies) > 0                 ? $patient->autresbiologies[0] : [],
                'urines'                        => count($patient->urines) > 0                          ? $patient->urines[0] : [],
                'autres_prelevements_biologies' => count($patient->autres_prelevements_biologies) > 0   ? $patient->autres_prelevements_biologies[0] : [],
                'antibiogrammes'                => count($patient->antibiogrammes) > 0                  ? $patient->antibiogrammes[0] : []
            ];

            //Hematologie data to simple string
            $hematologie = '';

            if($biologie['hematologies'] != []){

                $exclude_key_hematologie    = ['Autres', 'Conclusion'];
                $hematologie_value          = $biologie['hematologies']->value;
                krsort($hematologie_value);
                
                foreach ($hematologie_value as $key => $value) {
                    if(!is_array($value)){
                        if(!in_array($key, $exclude_key_hematologie) && !is_null($value)){
                            $hematologie .= str_contains($key, 'unite') ? $value.', ' : str_replace('_valeur', '', $key).': '.$value;
                        }
                    }
                } 
                $hematologie = rtrim($hematologie, ", ");
            }
            
            

            //biochimie data to simple string
            $biochimies = '';

            if($biologie['biochimies'] != []){

                $exclude_key_biochimie      = ['Autres', 'Conclusion'];
                $biochimie_value            = $biologie['biochimies']->value;
                krsort($biochimie_value);
                
                foreach ($biochimie_value as $key => $value) {
                    if(!is_array($value)){
                        if(!in_array($key, $exclude_key_biochimie) && !is_null($value)){
                            $biochimies .= str_contains($key, 'unite') ? $value.', ' : str_replace('_valeur', '', $key).': '.$value;
                        }
                    }
                } 
                $biochimies = rtrim($biochimies, ", ");
            }

            //Autres prélevement data to simple string

            //urines data to simple string
            $urines                     = '';

            if($biologie['urines'] != []){

                $exclude_key_urine          = ['Autres', 'GERME', 'Ionogramme', 'Hormonologie', 'Conclusion'];
                $urine_value                = $biologie['urines']->value;
                ksort($urine_value);
                
                foreach ($urine_value as $key => $value) {
                    if(!is_array($value)){
                        if(!in_array($key, $exclude_key_urine) && !is_null($value)){
                            $urines .= str_contains($key, 'value') ? $value : ', '.$key.': '.$value;
                        }
                    }
                } 
                $urines = ltrim($urines, ", ");
            }

            //Autres prelevement
            //$autre_prelevement                     = '';
            //$exclude_key_urine          = ['Autres', 'GERME', 'Ionogramme', 'Hormonologie', 'Conclusion'];
            //$urine_value                = $biologie['urines']->value;
            //ksort($urine_value);
            //
            //foreach ($urine_value as $key => $value) {
            //    if(!is_array($value)){
            //        if(!in_array($key, $exclude_key_urine) && !is_null($value)){
            //            $urines .= str_contains($key, 'value') ? $value : ', '.$key.': '.$value;
            //        }
            //    }
            //} 
            //$urines = ltrim($urines, ", ");

            $biologie = [
                'hematologies'                  => $hematologie,
                'biochimies'                    => $biochimies,
                'urines'                        => $urines
            ];

            return $biologie;
        }
        return [
            'hematologies'                  =>  '',
            'biochimies'                    =>  '',
            'autresbiologies'               =>  '',
            'urines'                        =>  '',
            'autres_prelevements_biologies' =>  '',
            'antibiogrammes'                =>  ''
        ];
    }
}
