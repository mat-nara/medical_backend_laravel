<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    
    public $incrementing = false;

    protected $fillable = [
        'id', 
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
        'motif_entree', 
    ];

    /**
     * Get the observation for the article.
     * 
     */
    public function observation()
    {
        return $this->hasOne(Observation::class);
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
}
