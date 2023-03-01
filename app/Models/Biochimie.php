<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biochimie extends Model
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
     * Get the patient that owns the article.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
