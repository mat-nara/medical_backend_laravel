<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traitement extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'code',
        'name',
        'DCI',
        'forme',
        'posologie',
        'prise_journalier',
        'etat',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'

        
        //'value'
        //'suivi_prise',
    ]; 


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        //'value' => 'array',
        'prise_journalier' => 'json'
        //'suivi_prise'    => 'array',
    ];

    /**
     * get the date value.
     * 
     * @param string $value
     * @return string
     */
    // public function getDateAttribute($value)
    // {
    //     return date('d/m/Y', strtotime($value));
    // }

    /**
     * mutate the date to a valid date.
     * 
     * @param string $value
     * @return void
     */
    // public function setDateAttribute($values)
    // {
    //     return $this->attributes['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $values)));
    // }

    /**
     * Get the patient that owns the article.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the suivies.
     * 
     */
    public function suivies()
    {
        return $this->hasMany(SuivieTraitement::class);
    }
}
