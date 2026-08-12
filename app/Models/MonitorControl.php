<?php

namespace App\Models;

use App\Models\DailyUniformity;
use Illuminate\Database\Eloquent\Model;

class MonitorControl extends Model
{
    protected $fillable = [
        'report_code',
        'location',
        'process_date',
        'shift',
        'size',

        'truck_no',

        'farm_id',

        'status',
        'set_count',
        'shackle_count',

        'expedition_id',
        'plate_number_id',

        'seal_no',
        'truck_arrival_time',
        'catch_date',
        'total_chicken',
        'total_kilo',
        'abw',
        'sppa_no',
        'order_id',
        'sppa_date',

        'supervisor_signature',
        'supervisor_signed_name',
        'supervisor_signed_at',
    ];

    protected $casts = [
        'process_date' => 'date',
        'catch_date' => 'date',
        'sppa_date' => 'date',
        'supervisor_signed_at' => 'datetime',

        'total_kilo' => 'decimal:2',
        'abw' => 'decimal:2',

        'truck_arrival_time' => 'datetime',
    ];

    public function farm() { return $this->belongsTo(Farm::class); }
    public function expedition() { return $this->belongsTo(Expedition::class); }
    public function plateNumber() { return $this->belongsTo(PlateNumber::class); }

    public function hangingForm() { return $this->hasOne(HangingForm::class); }
    public function dailyUniformity() { return $this->hasOne(DailyUniformity::class); }

    public function getAyamDiterimaAttribute(): int
    {
        $this->loadMissing(['hangingForm.lines.sets']);

        $form = $this->hangingForm;
        if (!$form) {
            return 0;
        }

        $customCaps = ['SH02' => [30 => 16]];
        $location = $this->location ?? '';
        $totalAyam = 0;

        foreach ($form->lines as $line) {
            $cap = $customCaps[$location][$line->line_no] ?? 50;

            foreach ($line->sets as $set) {
                if ($set->empty_count === null) {
                    continue;
                }

                $empty = min((int) $set->empty_count, $cap);
                $totalAyam += ($cap - $empty);
            }
        }

        return (int) $totalAyam;
    }

    protected $appends = ['calculated_abw'];

    public function getCalculatedAbwAttribute()
    {
        if ($this->total_chicken && $this->total_chicken > 0 && $this->total_kilo) {
            return round($this->total_kilo / $this->total_chicken, 2);
        }
        return null;
    }
    
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->total_chicken && $model->total_chicken > 0 && $model->total_kilo) {
                $model->abw = round($model->total_kilo / $model->total_chicken, 2);
            }
        });
    }
}
