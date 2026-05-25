<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductEvis extends Model
{
    protected $table = 'product_evis';
    protected $fillable = ['material_number', 'name'];

    public function reportItems()
    {
        return $this->hasMany(ReportEvisItem::class, 'product_evis_id', 'id');
    }
}
