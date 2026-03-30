<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'vendor_code',
        'area_category',
        'distance'
    ];
}
