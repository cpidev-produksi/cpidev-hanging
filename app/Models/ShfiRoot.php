<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShfiRoot extends Model
{
    protected $table = 'shfi_roots';
    protected $fillable = ['name','slug'];
}
