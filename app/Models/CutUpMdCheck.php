<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutUpMdCheck extends Model
{
    protected $table = 'cutup_md_checks';

    protected $guarded = [];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CutUpMdReport::class, 'report_id');
    }

    public function getTimeHmAttribute(): string
    {
        return substr((string) $this->check_time, 0, 5);
    }
}
