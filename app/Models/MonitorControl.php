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
        'driver_name',
        'truck_id',
        'farm_id',
        'farm_fee_amount',
        'status',
        'set_count',
        'shackle_count',
    ];

    protected $casts = [
        'process_date' => 'date',
        'size' => 'decimal:1',
        'farm_fee_amount' => 'decimal:2',
    ];

    public function truck() { return $this->belongsTo(Truck::class); }
    public function farm() { return $this->belongsTo(Farm::class); }

    public function hangingForm() {
        return $this->hasOne(HangingForm::class);
    }
}
