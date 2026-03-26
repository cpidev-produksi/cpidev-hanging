<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HangingForm extends Model
{
    protected $fillable = [
        'monitor_control_id',
        'unloading_time',
        'finish_time',
        'status',
    ];

    protected $casts = [
        'unloading_time' => 'datetime:H:i',
        'finish_time' => 'datetime:H:i',
    ];

    public function monitorControl() { return $this->belongsTo(MonitorControl::class); }

    public function lines() { return $this->hasMany(HangingLine::class); }
}
