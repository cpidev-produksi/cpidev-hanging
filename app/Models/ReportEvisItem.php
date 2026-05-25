<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportEvisItem extends Model
{
    protected $table = 'report_evis_items';
    protected $fillable = [
        'report_evis_id',
        'product_evis_id',
        'total_bag',
        'total_kg'
    ];

    protected $casts = [
        'total_bag' => 'decimal:2',
        'total_kg' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ReportEvis::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductEvis::class, 'product_evis_id', 'id');
    }

    public function setBagKgColumn($index, $bag, $kg)
    {
        $this->setAttribute("bag_$index", $bag);
        $this->setAttribute("kg_$index", $kg);
    }

    public function getBagKgColumns()
    {
        $columns = [];
        for ($i = 1; $i <= 10; $i++) {
            $columns[$i] = [
                'bag' => $this->getAttribute("bag_$i") ?? 0,
                'kg' => $this->getAttribute("kg_$i") ?? 0,
            ];
        }
        return $columns;
    }

    public function calculateTotals()
    {
        $totalBag = 0;
        $totalKg = 0;
        for ($i = 1; $i <= 10; $i++) {
            $totalBag += (float)($this->getAttribute("bag_$i") ?? 0);
            $totalKg += (float)($this->getAttribute("kg_$i") ?? 0);
        }
        $this->total_bag = $totalBag;
        $this->total_kg = $totalKg;
    }
}
