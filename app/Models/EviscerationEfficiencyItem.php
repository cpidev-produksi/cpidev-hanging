<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EviscerationEfficiencyItem extends Model
{
    protected $fillable = [
        'evisceration_efficiency_report_id',
        'monitor_control_id',
        'farm_id',
        'farm_name',
        'size',
        'time_start',
        'time_end',
        'sampling_count',
        'speed_evis',
        'vent_not_torn',
        'viscera_not_removed',
        'sprayer_status',
        'grit_findings',
        'water_flow_start',
        'water_flow_end',
        'sort',
    ];

    protected $casts = [
        'sampling_count' => 'integer',
        'speed_evis' => 'integer',
        'vent_not_torn' => 'integer',
        'viscera_not_removed' => 'integer',
        'grit_findings' => 'integer',
        'water_flow_start' => 'float',
        'water_flow_end' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(EviscerationEfficiencyReport::class, 'evisceration_efficiency_report_id');
    }

    public function monitorControl(): BelongsTo
    {
        return $this->belongsTo(MonitorControl::class);
    }

    public function getVentPercentAttribute(): ?float
    {
        return $this->percentage($this->vent_not_torn);
    }

    public function getVisceraPercentAttribute(): ?float
    {
        return $this->percentage($this->viscera_not_removed);
    }

    private function percentage(?int $count): ?float
    {
        return $count === null || ! $this->sampling_count
            ? null
            : round($count / $this->sampling_count * 100, 2);
    }

    public function getTimeRangeAttribute(): string
    {
        return str_replace(':', '.', (string) $this->time_start)
            .' - '
            .str_replace(':', '.', (string) $this->time_end);
    }

    public function getSizeLabelAttribute(): string
    {
        return $this->size ? str_replace('-', ' - ', $this->size) : '–';
    }

    public static function summarize(iterable $items): array
    {
        $items = collect($items);
        $summary = [];

        foreach (['vent_not_torn', 'viscera_not_removed', 'grit_findings'] as $field) {
            $values = $items->pluck($field)->filter(fn ($value) => $value !== null);
            $summary[$field] = [
                'total' => $values->isNotEmpty() ? $values->sum() : null,
                'average' => $values->isNotEmpty() ? $values->avg() : null,
            ];
        }

        return $summary;
    }
}
