<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    use HasFactory;
    
    public $incrementing = true;

    protected $fillable = [
        'id', 
        'service_id',
        'n_dossier', 
        'n_bulletin', 
        'nom', 
        'prenoms', 
        'genre', 
        'dob', 
        'age', 
        'lieu_dob', 
        'status', 
        'profession', 
        'adresse', 
        'ville', 
        'telephone', 
        'personne_en_charge', 
        'contact_pers_en_charge', 
        'date_entree', 
        'date_sortie',
        'heure_entree',
        'heure_sortie',
        'motif_entree', 
        'etat',
        'commentaire'
    ];
    
    /**
     * get the heure_entree value.
     * 
     * @param string $value
     * @return string
     */
    public function getHeureEntreeAttribute($value)
    {
        return date('H:i', strtotime($value));
    }

    /**
     * get the heure_sortie value.
     * 
     * @param string $value
     * @return string
     */
    public function getHeureSortieAttribute($value)
    {
        return date('H:i', strtotime($value));
    }

    /**
     * Get the observation for the article.
     * 
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Get the observation for the article.
     * 
     */
    public function observation()
    {
        return $this->hasOne(Observation::class, 'patient_id', 'id');
    }

    /**
     * Get the hematologies for the article.
     * 
     */
    public function hematologies()
    {
        return $this->hasMany(Hematologie::class);
    }

    /**
     * Get the biochimie for the article.
     * 
     */
    public function biochimies()
    {
        return $this->hasMany(Biochimie::class);
    }

    /**
     * Get the autre biologie for the article.
     * 
     */
    public function autresbiologies()
    {
        return $this->hasMany(AutreBiologie::class);
    }

    /**
     * Get the urine for the article.
     * 
     */
    public function urines()
    {
        return $this->hasMany(Urine::class);
    }

    /**
     * Get the autre autres_prelevements for the article.
     * 
     */
    public function autres_prelevements_biologies()
    {
        return $this->hasMany(AutrePrelevementBiologie::class);
    }

    /**
     * Get the autre autres_prelevements for the article.
     * 
     */
    public function antibiogrammes()
    {
        return $this->hasMany(Antibiogramme::class);
    }

    /**
     * Get the Radiologies for the article.
     * 
     */
    public function radiologies()
    {
        return $this->hasMany(Radiologie::class);
    }

    /**
     * Get the Echologies for the article.
     * 
     */
    public function echographies()
    {
        return $this->hasMany(Echographie::class);
    }

    /**
     * Get the Autre Imagerie for the article.
     * 
     */
    public function autres_imageries()
    {
        return $this->hasMany(AutreImagerie::class);
    }

    /**
     * Get the Examnen fonctionnel for the article.
     * 
     */
    public function examens_fonctionnels()
    {
        return $this->hasMany(ExamenFonctionnel::class);
    }

    /**
     * Get the evolutions for the article.
     * 
     */
    public function evolutions()
    {
        return $this->hasMany(Evolution::class);
    }
    
    /**
     * Get the traitment for the article.
     * 
     */
    public function traitements()
    {
        return $this->hasMany(Traitement::class);
    }

    /**
     * Get the surveillance for the patient.
     * 
     */
    public function surveillances()
    {
        return $this->hasMany(Surveillance::class);
    }


    /**
     * Get the users owner of the patient.
     * 
     */
    public function owners(): BelongsToMany {
        return $this->belongsToMany(User::class, 'accesses', 'patient_id', 'user_id',)->withPivot(['id', 'permission']);
    }

    /**
     * Get the Echologies for the article.
     * 
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the DossierAntecedent.
     * 
     */
    public function dossier()
    {
        return $this->hasOne(DossierAntecedent::class, 'patient_id', 'id');
    }

    /**
     * Get the observation for the article.
     * 
     */
    // public function antecedent()
    // {
    //     return $this->hasOne(Antecedent::class, 'patient_id', 'id');
    // }
}
