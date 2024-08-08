<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antecedent extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_uuid',
        'antecedent_medical', 
        'antecedent_chirurgical', 
        'antecedent_gineco'
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'antecedent_medical'        => 'array',
        'antecedent_chirurgical'    => 'array',
        'antecedent_gineco'         => 'array'
    ];

    /**
     * Get the patient that owns the article.
     * 
     */
    // public function patient()
    // {
    //     return $this->belongsTo(Patient::class, 'patient_id', 'id');
    // }
}
