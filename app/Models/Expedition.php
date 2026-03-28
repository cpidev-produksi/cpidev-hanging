<?php

namespace App\Models;

use App\Models\PlateNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expedition extends Model
{
    protected $fillable = ['name'];

    public function plateNumbers(): HasMany
    {
        return $this->hasMany(PlateNumber::class);
    }
}
