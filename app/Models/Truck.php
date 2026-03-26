<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = ['no_truck','plate_number','expedition_id'];

    public function expedition() {
        return $this->belongsTo(Expedition::class);
    }
}
