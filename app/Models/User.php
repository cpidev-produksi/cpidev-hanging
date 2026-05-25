<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role_id',
        'signature_path',
    ];

    protected $hidden = ['password','remember_token'];

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    public function hasRole($slug)
    {
        return $this->role?->slug === $slug;
    }

    public function isForeman()
    {
        return $this->hasRole('foreman') || $this->hasRole('supervisor');
    }
}
