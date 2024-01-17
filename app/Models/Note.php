<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'evolution_id',
        'user_id',
        'cliniques',
        'paracliniques',
        'traitement',
        'avis',
        'conclusion'
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'paracliniques' => 'json',
        'traitement'    => 'json',
    ];


    /**
     * Get the evolution of the note.
     * 
     */
    public function evolution()
    {
        return $this->belongsTo(Evolution::class);
    }

    /**
     * Get the author the evolution.
     * 
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
