<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'url',
        'method',
        'ip',
        'agent',
        'user_id'
    ];

    /**
     * get the date value.
     * 
     * @param string $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y h:i:s', strtotime($value));
    }
}
