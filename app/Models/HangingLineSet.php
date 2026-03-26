<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HangingLineSet extends Model
{
    protected $fillable = [
        'hanging_line_id',
        'set_no',
        'empty_count',
    ];

    public function line() { return $this->belongsTo(HangingLine::class, 'hanging_line_id'); }
}
