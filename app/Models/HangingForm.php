<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HangingForm extends Model
{
    protected $fillable = [
        'monitor_control_id',
        'unloading_time',
        'finish_time',
        'status',

        'dead_count',
        'retur_count',
        'retur_total_kg',

        'basket_condition',
        'truck_platform_condition',
        'feather_condition',
        
        'creator_name',
        'creator_user_id',
        'known_name',
        'known_user_id',
        'known_at',
        'approved_name',
        'approved_user_id',
        'approved_at',
    ];
 
    protected $casts = [
        'unloading_time' => 'datetime:H:i',
        'finish_time' => 'datetime:H:i',
        'retur_total_kg' => 'decimal:2',
        'known_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function monitorControl() { return $this->belongsTo(MonitorControl::class); }

    public function lines() { return $this->hasMany(HangingLine::class); }

    public function returItems() { return $this->hasMany(HangingReturItem::class); }

    public function missingQcFields(): array
    {
        $fields = [
            'basket_condition' => 'Keranjang',
            'truck_platform_condition' => 'Platform Truck',
            'feather_condition' => 'Bulu Ayam',
        ];

        return array_values(array_filter(
            $fields,
            fn ($field) => empty($this->{$field}),
            ARRAY_FILTER_USE_KEY
        ));
    }

    public function hasCompleteQc(): bool
    {
        return $this->missingQcFields() === [];
    }

    public function scopeQcComplete(Builder $query): Builder
    {
        return $query
            ->whereNotNull('basket_condition')->where('basket_condition', '<>', '')
            ->whereNotNull('truck_platform_condition')->where('truck_platform_condition', '<>', '')
            ->whereNotNull('feather_condition')->where('feather_condition', '<>', '');
    }
}
