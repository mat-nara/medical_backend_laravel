<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hopital extends Model
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'nom', 
        'adresse', 
        'ville'
    ];

    /**
     * Get the services for the hopital.
     * 
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'hopital_id', 'id');
    }

}
