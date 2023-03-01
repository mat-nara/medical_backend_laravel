<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenFonctionnel extends Model
{
    use HasFactory;
    protected $fillable = ['patient_id', 'date', 'value']; 

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array'
    ];

    /**
     * get the date value.
     * 
     * @param string $value
     * @return string
     */
    public function getDateAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    /**
     * mutate the date to a valid date.
     * 
     * @param string $value
     * @return void
     */
    public function setDateAttribute($values)
    {
        return $this->attributes['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $values)));
    }

    /**
     * Get the patient that owns the article.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
