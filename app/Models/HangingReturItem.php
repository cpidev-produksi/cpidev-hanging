<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HangingReturItem extends Model
{
    protected $fillable = [
        'hanging_form_id',
        'weight_kg',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    public function form()
    {
        return $this->belongsTo(HangingForm::class, 'hanging_form_id');
    }
}
