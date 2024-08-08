<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierAntecedent extends Model
{
    use HasFactory;
    protected $fillable = ['patient_uuid', 'patient_id','service_id', 'date', 'event', 'description'];

    /**
     * Get the patient that owns the connexion.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get the service that owns the connexion.
     * 
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
