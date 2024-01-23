<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'hierarchic_level', 'name', 'slug',
    ];

    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at',
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_roles');
    }


    /**
     * Get all childs of the role.
     * 
     */
    public function childs(){
        return $this->hasMany(Role::class,'parent_id','id') ;
    }


    /**
     * Get parent of the role.
     * 
     */
    public function parent(){
        return $this->belongsTo(Role::class,'parent_id','id');
    }
}
