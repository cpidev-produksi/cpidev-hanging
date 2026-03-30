<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorControl extends Model
{
    protected $fillable = [
        'report_code',
        'location',
        'process_date',
        'shift',
        'size',
        'farm_id',
        'farm_fee_amount',
        'status',
        'set_count',
        'shackle_count',
        'expedition_id',
        'plate_number_id',
    ];

    protected $casts = [
        'process_date' => 'date',
        'size' => 'decimal:1',
        'farm_fee_amount' => 'decimal:2',
    ];

    public function farm() { return $this->belongsTo(Farm::class); }
    public function expedition() { return $this->belongsTo(Expedition::class); }
    public function plateNumber() { return $this->belongsTo(PlateNumber::class); }

    public function hangingForm() {
        return $this->hasOne(HangingForm::class);
    }
}
