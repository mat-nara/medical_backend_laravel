<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['patient_id','filename', 'MIME_type', 'path', 'description'];

    /**
     * Get the patient that owns the article.
     * 
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
