<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyYield extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'daily_yield_upload_id',
        'plant',
        'yield_titik_0',
        'ach_yield_h0',
        'yield_h1',
        'ach_yield_h1',
        'yield_h2',
        'ach_yield_h2',
        'yield_h3',
        'ach_yield_h3',
        'yield_h4',
        'ach_yield_h4',
        'yield_fg',
        'total_fg_bp',
        'sumpo',
        'lost',
        'tanggal_update_terakhir',
    ];
 
    protected $casts = [
        'yield_titik_0' => 'float',
        'ach_yield_h0' => 'float',
        'yield_h1' => 'float',
        'ach_yield_h1' => 'float',
        'yield_h2' => 'float',
        'ach_yield_h2' => 'float',
        'yield_h3' => 'float',
        'ach_yield_h3' => 'float',
        'yield_h4' => 'float',
        'ach_yield_h4' => 'float',
        'yield_fg' => 'float',
        'total_fg_bp' => 'float',
        'sumpo' => 'float',
        'lost' => 'float',
        'tanggal_update_terakhir' => 'date',
    ];
 
    // Kolom-kolom yang jika bernilai 0 dianggap "data kosong/belum terisi"
    // dan ditandai secara visual di dashboard (bukan disembunyikan).
    public const ZERO_FLAG_COLUMNS = [
        'yield_titik_0', 'ach_yield_h0',
        'yield_h1', 'ach_yield_h1',
        'yield_h2', 'ach_yield_h2',
        'yield_h3', 'ach_yield_h3',
        'yield_h4', 'ach_yield_h4',
        'yield_fg', 'total_fg_bp', 'sumpo',
    ];
 
    public function upload(): BelongsTo
    {
        return $this->belongsTo(DailyYieldUpload::class, 'daily_yield_upload_id');
    }
}
