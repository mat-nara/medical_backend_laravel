<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuivieTraitement extends Model
{
    use HasFactory;

    protected $fillable = [
        'traitement_id',
        'user_id',
        'date',
        'heure',
        'heure_finale',
        'commentaire'
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
     * Get the traitement that owns the suivi.
     * 
     */
    public function traitement()
    {
        return $this->belongsTo(Traitement::class);
    }

}
