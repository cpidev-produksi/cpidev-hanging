<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftCompletion extends Model
{
    use HasFactory;

    protected $table = 'shift_completions';
    protected $fillable = [
        'location',
        'shift',
        'process_date',
        'finished_at',
        'total_target',
        'total_completed',
        'remaining_target',
        'remaining_units',
        'notes',
    ];

    protected $casts = [
        'process_date' => 'date',
        'finished_at' => 'datetime',
        'total_target' => 'integer',
        'total_completed' => 'integer',
        'remaining_target' => 'integer',
        'remaining_units' => 'integer',
    ];

    public function scopeForLocationAndDate($query, $location, $date)
    {
        return $query->where('location', $location)
                     ->whereDate('process_date', $date);
    }

    public function scopeForShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    public static function isShiftCompleted($location, $shift, $date)
    {
        return self::where([
            'location' => $location,
            'shift' => $shift,
            'process_date' => $date,
        ])->exists();
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->total_target <= 0) {
            return 0;
        }
        return round(($this->total_completed / $this->total_target) * 100, 2);
    }

    public function getShiftNameAttribute()
    {
        return $this->shift === 'pagi' ? 'Shift 1' : 'Shift 3';
    }

    public function getLocationNameAttribute()
    {
        return $this->location;
    }
}