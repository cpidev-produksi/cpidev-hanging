<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlateNumber extends Model
{
    protected $fillable = ['plate_number', 'expedition_id'];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }
}