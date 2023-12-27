<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'hopital_id',
        'head_id',
        'name'
    ];

    /**
     * Get the observation for the article.
     * 
     */
    public function head()
    {
        return $this->hasOne(User::class, 'id', 'head_id');
    }

    /**
     * Get the hopital that owns the service.
     * 
     */
    public function hopital()
    {
        return $this->belongsTo(Hopital::class, 'hopital_id', 'id' );
    }

    /**
     * Get the users for the service.
     * 
     */
    public function users()
    {
        return $this->hasMany(User::class, 'service_id', 'id');
    }

    /**
     * Get the users for the service.
     * 
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'service_id', 'id');
    }

}
