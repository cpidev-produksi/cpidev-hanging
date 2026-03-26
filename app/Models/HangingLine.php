<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HangingLine extends Model
{
    protected $fillable = [
        'hanging_form_id',
        'line_no',
        'shackle_label',
        'rule_min',
        'rule_max',
    ];

    public function form() { return $this->belongsTo(HangingForm::class, 'hanging_form_id'); }

    public function sets() { return $this->hasMany(HangingLineSet::class); }
}
