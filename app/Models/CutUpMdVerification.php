<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutUpMdVerification extends Model
{
    protected $table = 'cutup_md_verifications';

    protected $guarded = [];

    public const RESULT_COLUMNS = [
        'fe_d', 'fe_t', 'fe_b',
        'nonfe_d', 'nonfe_t', 'nonfe_b',
        'sus316_d', 'sus316_t', 'sus316_b',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CutUpMdReport::class, 'report_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CutUpProduct::class, 'product_id');
    }

    public function getTimeHmAttribute(): string
    {
        return substr((string) $this->check_time, 0, 5);
    }
}
