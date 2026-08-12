<?php

namespace App\Models;

use App\Models\DailyUniformityWeight;
use Illuminate\Database\Eloquent\Model;

class DailyUniformity extends Model
{
    protected $fillable = [
        'monitor_control_id',
        'process_date',
        'shift',
        'location',
        'avg_rpa',
        'berat_rpa',
    ];

    protected $casts = [
        'process_date' => 'date',
        'avg_rpa' => 'decimal:2',
        'berat_rpa' => 'decimal:2',
    ];

    public function monitorControl()
    {
        return $this->belongsTo(MonitorControl::class);
    }

    public function weights()
    {
        return $this->hasMany(DailyUniformityWeight::class)->orderBy('sequence');
    }

    public function driverName(): ?string
    {
        return optional(optional($this->monitorControl)->plateNumber)->driver_name;
    }

    protected function parsedSize(): array
    {
        $size = optional($this->monitorControl)->size;

        if (!$size || !str_contains($size, '-')) {
            return [];
        }

        [$low, $high] = array_map(
            fn ($v) => (float) str_replace(',', '.', trim($v)),
            explode('-', $size, 2)
        );

        return [$low, $high];
    }

    public function rangeLow(): ?float
    {
        return $this->parsedSize()[0] ?? null;
    }

    public function rangeHigh(): ?float
    {
        return $this->parsedSize()[1] ?? null;
    }

    public function summary(): array
    {
        $weights = $this->weights->pluck('weight_kg')->map(fn ($w) => (float) $w);
        $count = $weights->count();

        [$low, $high] = array_pad($this->parsedSize(), 2, null);

        $below = 0;
        $inRange = 0;
        $above = 0;

        foreach ($weights as $w) {
            if ($low !== null && $w < $low) {
                $below++;
            } elseif ($high !== null && $w > $high) {
                $above++;
            } else {
                $inRange++;
            }
        }

        $pct = fn ($n) => $count > 0 ? round(($n / $count) * 100, 1) : 0.0;

        return [
            'count' => $count,
            'total' => $count ? round($weights->sum(), 3) : 0,
            'min' => $count ? round($weights->min(), 3) : null,
            'max' => $count ? round($weights->max(), 3) : null,
            'avg' => $count ? round($weights->avg(), 3) : null,
            'range_low' => $low,
            'range_high' => $high,
            'below' => ['count' => $below, 'pct' => $pct($below)],
            'in_range' => ['count' => $inRange, 'pct' => $pct($inRange)],
            'above' => ['count' => $above, 'pct' => $pct($above)],
        ];
    }
}
