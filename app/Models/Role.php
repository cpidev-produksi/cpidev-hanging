<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name','slug'];
    
    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function hasPerm(string $group, string $key): bool
    {
        return $this->permissions()
            ->where('group', $group)
            ->where('key', $key)
            ->where('allowed', true)
            ->exists();
    }
}
