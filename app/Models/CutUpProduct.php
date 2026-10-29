<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutUpProduct extends Model
{
    protected $table = 'cutup_products';

    protected $fillable = ['name'];
}
