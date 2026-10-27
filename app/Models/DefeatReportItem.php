<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris = satu farm pada satu laporan (tanggal operasional + shift + lokasi).
 * Setiap parameter berisi SATU nilai; total & average dihitung dari seluruh farm
 * dalam 1 tabel (lihat summarize()).
 */
class DefeatReportItem extends Model
{
    protected $fillable = [
        'defeat_report_id',
        'monitor_control_id',
        'farm_id',
        'farm_name',
        'size',
        'time_start',
        'time_end',
        'sampling_count',
        'scalder_fallen',
        'head_pulling_ok',
        'head_pulling_not_ok',
        'leg_cutting_ok',
        'leg_cutting_not_ok',
        'leg_pulling_ok',
        'leg_pulling_not_ok',
        'avg_dta',
        'speed_live_bird',
        'speed_evis_c',
        'amp',
        'volt',
        'bleeding_time',
        'scalder1_temp',
        'scalder1_time',
        'scalder2_temp',
        'scalder2_time',
        'scalder3_temp',
        'scalder3_time',
        'after_plucker',
        'after_sortir',
        'head_pulling',
        'leg_cutting',
        'leg_pulling',
        'atp',
        'sort',
    ];

    protected $casts = [
        'sampling_count' => 'integer',
        'scalder_fallen' => 'integer',
        'head_pulling_ok' => 'integer',
        'head_pulling_not_ok' => 'integer',
        'leg_cutting_ok' => 'integer',
        'leg_cutting_not_ok' => 'integer',
        'leg_pulling_ok' => 'integer',
        'leg_pulling_not_ok' => 'integer',
        'avg_dta' => 'float',
        'speed_live_bird' => 'integer',
        'speed_evis_c' => 'integer',
        'amp' => 'float',
        'volt' => 'float',
        'scalder1_temp' => 'float',
        'scalder2_temp' => 'float',
        'scalder3_temp' => 'float',
        'after_plucker' => 'float',
        'after_sortir' => 'float',
        'head_pulling' => 'float',
        'leg_cutting' => 'float',
        'leg_pulling' => 'float',
        'atp' => 'integer',
    ];

    /**
     * Kelompok pemeriksaan kualitas. Tiap kelompok punya dua input angka (OK & Not OK)
     * dan satu persentase hasil hitung: % OK = OK / Jumlah Sampling x 100.
     */
    public const QC = [
        'head_pulling' => 'Head Pulling',
        'leg_cutting'  => 'Leg Cutting',
        'leg_pulling'  => 'Leg Pulling',
    ];

    /**
     * Definisi parameter (urutan = urutan kolom tabel & urutan baris form).
     *   group   : judul kelompok
     *   label   : judul kolom / label input
     *   name    : nama lengkap untuk pesan validasi (opsional)
     *   kind    : dec (desimal) | int (bulat) | time (format mm.ss)
     *   dec     : jumlah desimal tampilan
     *   avg_dec : jumlah desimal pada baris Average (default = dec)
     *   min/max : batas nilai (min default 0, max null = tanpa batas khusus)
     *   req     : wajib diisi
     *   total   : tampilkan Total di baris bawah tabel (default true)
     *   avg     : tampilkan Average di baris bawah tabel
     *   hist    : otomatis terisi dari riwayat farm sebelumnya (tetap bisa diubah)
     *   calc    : nilai hasil hitung, bukan input (% OK)
     */
    public const FIELDS = [
        'avg_dta'             => ['group' => 'AVG DTA',               'label' => 'kg',                    'kind' => 'dec',  'dec' => 2, 'max' => 99.99, 'req' => true,  'avg' => true],
        'speed_live_bird'     => ['group' => 'Speed Line',            'label' => 'Live Bird (BPH)',       'kind' => 'int',  'dec' => 0, 'max' => 99999, 'req' => true,  'avg' => true,  'hist' => true],
        'speed_evis_c'        => ['group' => 'Speed Line',            'label' => 'Evis C. (BPH)',         'kind' => 'int',  'dec' => 0, 'max' => 99999, 'req' => true,  'avg' => true,  'hist' => true],
        'amp'                 => ['group' => 'Stunner',               'label' => 'Amp',                   'kind' => 'dec',  'dec' => 2, 'max' => 99,    'req' => true,  'avg' => true],
        'volt'                => ['group' => 'Stunner',               'label' => 'Volt',                  'kind' => 'dec',  'dec' => 1, 'max' => 999,   'req' => true,  'avg' => true],
        'bleeding_time'       => ['group' => 'Bleeding',              'label' => 'Time',                  'kind' => 'time', 'dec' => 0, 'max' => null,  'req' => true,  'avg' => true,  'hist' => true],
        'scalder1_temp'       => ['group' => 'Scalder 1',             'label' => 'Suhu (°C)',             'kind' => 'dec',  'dec' => 1, 'max' => 150,   'req' => false, 'avg' => true],
        'scalder1_time'       => ['group' => 'Scalder 1',             'label' => 'Time',                  'kind' => 'time', 'dec' => 0, 'max' => null,  'req' => true,  'avg' => true,  'hist' => true],
        'scalder2_temp'       => ['group' => 'Scalder 2',             'label' => 'Suhu (°C)',             'kind' => 'dec',  'dec' => 1, 'max' => 150,   'req' => true,  'avg' => true],
        'scalder2_time'       => ['group' => 'Scalder 2',             'label' => 'Time',                  'kind' => 'time', 'dec' => 0, 'max' => null,  'req' => true,  'avg' => true,  'hist' => true],
        'scalder3_temp'       => ['group' => 'Scalder 3',             'label' => 'Suhu (°C)',             'kind' => 'dec',  'dec' => 1, 'max' => 150,   'req' => true,  'avg' => true],
        'scalder3_time'       => ['group' => 'Scalder 3',             'label' => 'Time',                  'kind' => 'time', 'dec' => 0, 'max' => null,  'req' => true,  'avg' => true,  'hist' => true],
        'scalder_fallen'      => ['group' => 'Ayam Jatuh di Scalder', 'label' => 'Ekor',                  'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Ayam Jatuh di Scalder'],
        'after_plucker'       => ['group' => 'Defeathering',          'label' => 'After Plucker',         'kind' => 'dec',  'dec' => 1, 'max' => 999,   'req' => true,  'avg' => true],
        'after_sortir'        => ['group' => 'Defeathering',          'label' => 'After Sortir Karyawan', 'kind' => 'dec',  'dec' => 1, 'max' => 999,   'req' => true,  'avg' => true],
        'sampling_count'      => ['group' => 'Jumlah Sampling',       'label' => 'Ekor',                  'kind' => 'int',  'dec' => 0, 'min' => 1, 'max' => 999999, 'req' => true, 'avg' => true, 'avg_dec' => 1, 'name' => 'Jumlah Sampling'],

        'head_pulling_ok'     => ['group' => 'Head Pulling',          'label' => 'OK',                    'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Head Pulling OK'],
        'head_pulling_not_ok' => ['group' => 'Head Pulling',          'label' => 'Not OK',                'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Head Pulling Not OK'],
        'head_pulling'        => ['group' => 'Head Pulling',          'label' => '% OK',                  'kind' => 'dec',  'dec' => 1, 'max' => 100,   'req' => false, 'avg' => true,  'total' => false, 'calc' => true],

        'leg_cutting_ok'      => ['group' => 'Leg Cutting',           'label' => 'OK',                    'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Leg Cutting OK'],
        'leg_cutting_not_ok'  => ['group' => 'Leg Cutting',           'label' => 'Not OK',                'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Leg Cutting Not OK'],
        'leg_cutting'         => ['group' => 'Leg Cutting',           'label' => '% OK',                  'kind' => 'dec',  'dec' => 1, 'max' => 100,   'req' => false, 'avg' => true,  'total' => false, 'calc' => true],

        'leg_pulling_ok'      => ['group' => 'Leg Pulling',           'label' => 'OK',                    'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Leg Pulling OK'],
        'leg_pulling_not_ok'  => ['group' => 'Leg Pulling',           'label' => 'Not OK',                'kind' => 'int',  'dec' => 0, 'max' => 999999, 'req' => true, 'avg' => true,  'avg_dec' => 1, 'name' => 'Leg Pulling Not OK'],
        'leg_pulling'         => ['group' => 'Leg Pulling',           'label' => '% OK',                  'kind' => 'dec',  'dec' => 1, 'max' => 100,   'req' => false, 'avg' => true,  'total' => false, 'calc' => true],

        'atp'                 => ['group' => 'ATP',                   'label' => 'Ekor',                  'kind' => 'int',  'dec' => 0, 'max' => 9999,  'req' => false, 'avg' => false],
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DefeatReport::class, 'defeat_report_id');
    }

    public function monitorControl(): BelongsTo
    {
        return $this->belongsTo(MonitorControl::class);
    }

    /* ---------- Perhitungan ---------- */

    /** Nilai numerik parameter. Parameter bertipe time (mm.ss) dikembalikan dalam detik. */
    public function numeric(string $key): ?float
    {
        $v = $this->getAttribute($key);

        if ($v === null || $v === '') {
            return null;
        }

        if ((self::FIELDS[$key]['kind'] ?? null) === 'time') {
            return preg_match('/^(\d{1,2})\.(\d{2})$/', (string) $v, $m)
                ? ((int) $m[1] * 60) + (int) $m[2]
                : null;
        }

        return is_numeric($v) ? (float) $v : null;
    }

    /**
     * Total & average dari SELURUH farm dalam satu tabel (bukan per farm).
     *
     * @return array{total: array<string, ?float>, average: array<string, ?float>}
     */
    public static function summarize(iterable $items): array
    {
        $items = collect($items);
        $total = [];
        $average = [];

        foreach (self::FIELDS as $key => $f) {
            $vals = $items
                ->map(fn (self $i) => $i->numeric($key))
                ->filter(fn ($v) => $v !== null)
                ->values();

            $total[$key] = ($f['total'] ?? true) && $vals->isNotEmpty() ? $vals->sum() : null;
            $average[$key] = $f['avg'] && $vals->isNotEmpty() ? $vals->avg() : null;
        }

        return compact('total', 'average');
    }

    /* ---------- Format tampilan ---------- */

    /** Format nilai parameter: koma desimal, tanpa pemisah ribuan; time -> mm.ss. */
    public static function format(string $key, ?float $value, ?int $dec = null): string
    {
        if ($value === null) {
            return '–';
        }

        $f = self::FIELDS[$key];

        if ($f['kind'] === 'time') {
            $s = (int) round($value);

            return sprintf('%02d.%02d', intdiv($s, 60), $s % 60);
        }

        return number_format($value, $dec ?? $f['dec'], ',', '');
    }

    /** Persentase OK = OK / Jumlah Sampling x 100 (null bila belum bisa dihitung). */
    public static function percentOk(?int $ok, ?int $sampling): ?float
    {
        if ($ok === null || ! $sampling) {
            return null;
        }

        return round($ok / $sampling * 100, 2);
    }

    public function display(string $key): string
    {
        return self::format($key, $this->numeric($key));
    }

    /** Angka gaya Indonesia (dipakai dashboard). */
    public static function fmt($value, int $dec = 2): string
    {
        if ($value === null || $value === '') {
            return '–';
        }

        return number_format((float) $value, $dec, ',', '');
    }

    /** "08.05 - 08.27" */
    public function getTimeRangeAttribute(): string
    {
        return str_replace(':', '.', (string) $this->time_start)
            . ' - '
            . str_replace(':', '.', (string) $this->time_end);
    }

    /** "2.00-2.20" -> "2.00 - 2.20" */
    public function getSizeLabelAttribute(): string
    {
        return $this->size ? str_replace('-', ' - ', $this->size) : '–';
    }
}