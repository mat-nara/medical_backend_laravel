<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'historique', 
        'antecedent_medical', 
        'antecedent_chirurgical', 
        'antecedent_gineco', 
        'antecedent_toxique', 
        'antecedent_allergique', 
        'exam_phys_signe_gen', 
        'exam_phys_signe_gen_score_indice', 
        'exam_phys_signe_fonc', 
        'exam_phys_signe_fonc_score_indice', 
        'exam_phys_signe_phys_cardio', 
        'exam_phys_signe_phys_cardio_score_indice', 
        'exam_phys_signe_phys_pleuro', 
        'exam_phys_signe_phys_pleuro_score_indice', 
        'exam_phys_signe_phys_neuro', 
        'exam_phys_signe_phys_neuro_score_indice', 
        'exam_phys_signe_phys_abdo', 
        'exam_phys_signe_phys_abdo_score_indice', 
        'conclusion', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'antecedent_medical'                        => 'array',
        'antecedent_chirurgical'                    => 'array',
        'antecedent_gineco'                         => 'array',
        'antecedent_toxique'                        => 'array',
        'antecedent_allergique'                     => 'array',
        'exam_phys_signe_gen_score_indice'          => 'array',
        'exam_phys_signe_fonc_score_indice'         => 'array',
        'exam_phys_signe_phys_cardio_score_indice'  => 'array',
        'exam_phys_signe_phys_pleuro_score_indice'  => 'array',
        'exam_phys_signe_phys_neuro_score_indice'   => 'array',
        'exam_phys_signe_phys_abdo_score_indice'    => 'array',
    ];

    /**
     * Get the patient that owns the article.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
