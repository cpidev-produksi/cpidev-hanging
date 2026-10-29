<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CutUpMdReport extends Model
{
    protected $table = 'cutup_md_reports';

    protected $guarded = [];

    protected $casts = [
        'report_date'     => 'date',
        'fe_diameter'     => 'float',
        'nonfe_diameter'  => 'float',
        'sus316_diameter' => 'float',
    ];

    public function checks(): HasMany
    {
        return $this->hasMany(CutUpMdCheck::class, 'report_id')->orderBy('check_time')->orderBy('id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CutUpMdVerification::class, 'report_id')->orderBy('check_time')->orderBy('id');
    }

    public function getDayNameAttribute(): string
    {
        return $this->report_date->copy()->locale('id')->translatedFormat('l');
    }

    public function getDateLongAttribute(): string
    {
        return $this->report_date->copy()->locale('id')->translatedFormat('l, d F Y');
    }
}
