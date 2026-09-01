<?php

namespace App\Models;

use App\Models\DailyYield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyYieldUpload extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'bulan',
        'tahun',
        'file_name',
        'file_path',
        'uploaded_by',
        'is_latest',
    ];
 
    protected $casts = [
        'is_latest' => 'boolean',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];
 
    public const BULAN_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
 
    public function details(): HasMany
    {
        return $this->hasMany(DailyYield::class);
    }
 
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
 
    public function getPeriodeLabelAttribute(): string
    {
        return (self::BULAN_NAMES[$this->bulan] ?? $this->bulan) . ' ' . $this->tahun;
    }
}
