<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     *
     * @param String $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'parent_id',
        'name',
        'avatar',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the roles of the user.
     * 
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles');
    }


    /**
     * Get all childs of the user.
     * 
     */
    public function childs(){
        return $this->hasMany(User::class,'parent_id','id');
    }


    /**
     * Get parent of the user.
     * 
     */
    public function parent(){
        return $this->belongsTo(User::class,'parent_id','id');
    }

    
    /**
     * Get the service that owns the user.
     * 
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id' );
    }

    /**
     * Get the patients of the user.
     * 
     */
    public function patients() {
        return $this->belongsToMany(Patient::class, 'accesses', 'user_id', 'patient_id')->withPivot(['id','permission']);
    }


    /**
     * Get all note writen by the user.
     * 
     */
    public function notes(){
        return $this->hasMany(Note::class, 'user_id', 'id');
    }

}
