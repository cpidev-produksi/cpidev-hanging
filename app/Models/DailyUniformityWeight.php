<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyUniformityWeight extends Model
{
    protected $fillable = [
        'daily_uniformity_id',
        'sequence',
        'weight_kg',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
    ];

    public function dailyUniformity()
    {
        return $this->belongsTo(DailyUniformity::class);
    }
}
