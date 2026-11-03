<?php

namespace App\Support;

use App\Models\HangingForm;
use App\Models\MonitorControl;

/**
 * Menyiapkan semua data untuk PDF "Form Perhitungan Ayam" (hal. 1)
 * dan "Form Perhitungan Hanging Ayam" (hal. 2).
 *
 * Class ini sengaja pure-PHP (tanpa query tambahan) supaya mudah dites:
 * cukup beri MonitorControl + HangingForm yang relasinya sudah di-load
 * (hangingForm.lines.sets, farm, expedition, plateNumber).
 */
class HangingReportBuilder
{
    public const DEFAULT_CAP = 50;

    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Kapasitas shackle per baris. Harus sama dengan
     * HangingFormController::getMaxCapacity().
     */
    public static function maxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            'SH02' => [30 => 17],
        ];

        return $custom[$location][$lineNo] ?? self::DEFAULT_CAP;
    }

    public static function build(MonitorControl $mc, HangingForm $form): array
    {
        $location = (string) ($mc->location ?? '');

        // ---- urutkan baris (line) berdasarkan line_no
        $lines = [];
        foreach ($form->lines as $line) {
            $lines[] = $line;
        }
        usort($lines, fn ($a, $b) => (int) $a->line_no <=> (int) $b->line_no);

        // ---- jumlah kolom (set): SH01 = 4, SH02 = 3 (ikut set_count di monitor control)
        $setCount = (int) ($mc->set_count ?? 0);
        foreach ($lines as $line) {
            foreach ($line->sets as $set) {
                $setCount = max($setCount, (int) $set->set_no);
            }
        }
        if ($setCount < 1) {
            $setCount = $location === 'SH01' ? 4 : 3;
        }

        $colTotals = [];
        for ($s = 1; $s <= $setCount; $s++) {
            $colTotals[$s] = ['empty' => 0, 'ayam' => 0, 'filled' => 0];
        }

        $rows = [];
        $groups = [];          // [cap => count/empty/ayam]
        $totalKosong = 0;
        $totalAyam = 0;

        foreach ($lines as $line) {
            $no = (int) $line->line_no;
            $cap = self::maxCapacity($location, $no);

            $cells = [];
            for ($s = 1; $s <= $setCount; $s++) {
                $cells[$s] = null;
            }

            foreach ($line->sets as $set) {
                if ($set->empty_count === null) {
                    continue; // belum diisi -> sel dikosongkan
                }

                $sn = (int) $set->set_no;
                if (!array_key_exists($sn, $cells)) {
                    continue;
                }

                $empty = min(max((int) $set->empty_count, 0), $cap);
                $ayam  = $cap - $empty;

                $cells[$sn] = ['empty' => $empty, 'ayam' => $ayam];

                $colTotals[$sn]['empty']  += $empty;
                $colTotals[$sn]['ayam']   += $ayam;
                $colTotals[$sn]['filled'] += 1;

                $totalKosong += $empty;
                $totalAyam   += $ayam;

                if (!isset($groups[$cap])) {
                    $groups[$cap] = ['cap' => $cap, 'count' => 0, 'empty' => 0, 'ayam' => 0];
                }
                $groups[$cap]['count'] += 1;
                $groups[$cap]['empty'] += $empty;
                $groups[$cap]['ayam']  += $ayam;
            }

            $min = $line->rule_min ?? (($no - 1) * self::DEFAULT_CAP);
            $max = $line->rule_max ?? ($no * self::DEFAULT_CAP);

            $rows[] = [
                'no'    => $no,
                'blok'  => $line->shackle_label ?: ($no . '-' . ($no + 1)),
                'range' => $min . ' - ' . $max,
                'cells' => $cells,
            ];
        }

        // ---- grup blok: kondisional (cap != 50) dulu, lalu penuh (cap 50)
        $condGroups = [];
        $fullGroups = [];
        ksort($groups);
        foreach ($groups as $cap => $g) {
            if ($cap === self::DEFAULT_CAP) {
                $fullGroups[] = $g;
            } else {
                $condGroups[] = $g;
            }
        }

        // ---- verifikasi terhadap data monitor control
        $dead      = (int) ($form->dead_count ?? 0);
        $retur     = (int) ($form->retur_count ?? 0);
        $totalEkor = (int) ($mc->total_chicken ?? 0);
        $ekorPlan  = max(0, $totalEkor - $dead - $retur);
        $selisih   = $totalAyam - $ekorPlan;

        if ($selisih === 0) {
            $status = 'Sesuai';
            $selisihText = '0 ekor';
        } elseif ($selisih > 0) {
            $status = 'Kelebihan';
            $selisihText = '+' . self::ribuan($selisih) . ' ekor (lebih)';
        } else {
            $status = 'Kekurangan';
            $selisihText = '-' . self::ribuan(abs($selisih)) . ' ekor (kurang)';
        }

        return [
            // header umum
            'tanggal'   => self::tanggal($mc->process_date),
            'location'  => $location,
            'shift'     => ucfirst((string) ($mc->shift ?? '')),
            'truck_no'  => $mc->truck_no,
            'form_code' => 'SH ' . (preg_replace('/\D/', '', $location) ?: '00') . ' / 01',

            // DTA
            'farm'       => $mc->farm?->name ?? '-',
            'ekspedisi'  => $mc->expedition?->name ?? '-',
            'no_polisi'  => $mc->plateNumber?->plate_number ?? '-',
            'no_segel'   => $mc->seal_no ?: '-',
            'size'       => $mc->size ?: '-',
            'abw'        => number_format((float) ($mc->abw ?? 0), 2, '.', ''),
            'total_ekor' => (string) $totalEkor,
            'total_kilo' => number_format((float) ($mc->total_kilo ?? 0), 2, ',', '.'),
            'sppa_no'    => $mc->sppa_no ?: '-',
            'sppa_date'  => $mc->sppa_date?->format('d/m/Y') ?? '-',
            'order_id'   => $mc->order_id ?: '-',
            'catch_date' => $mc->catch_date?->format('d/m/Y') ?? '-',
            'arrival'    => $mc->truck_arrival_time?->format('H:i') ?? '-',

            // hanging ayam (ringkasan)
            'ekor_plan'      => self::ribuanPlain($ekorPlan),
            'cond_groups'    => $condGroups,
            'full_groups'    => $fullGroups,
            'shackle_kosong' => self::ribuanPlain($totalKosong),
            'status'         => $status,
            'selisih_text'   => $selisihText,
            'diterima'       => self::ribuan($totalAyam),

            // retur & mati
            'dead'     => $dead,
            'retur'    => $retur,
            'retur_kg' => number_format((float) ($form->retur_total_kg ?? 0), 2, '.', ''),

            // QC kondisi
            'qc_basket'   => self::qcLabel($form->basket_condition),
            'qc_platform' => self::qcLabel($form->truck_platform_condition),
            'qc_feather'  => self::qcLabel($form->feather_condition),

            // halaman 2
            'unloading'   => $form->unloading_time?->format('H:i') ?? '-',
            'finish'      => $form->finish_time?->format('H:i') ?? '-',
            'set_count'   => $setCount,
            'rows'        => $rows,
            'col_totals'  => $colTotals,
            'total_ayam'  => $totalAyam,
            'total_kosong'=> $totalKosong,

            // tanda tangan supervisor (opsional)
            'sign_image' => $mc->supervisor_signature,
            'sign_name'  => $mc->supervisor_signed_name,
        ];
    }

    public static function ribuan(int $n): string
    {
        return number_format($n, 0, ',', '.');
    }

    /** Tanpa pemisah ribuan (sesuai form: "2153", "31"). */
    public static function ribuanPlain(int $n): string
    {
        return (string) $n;
    }

    public static function tanggal($date): string
    {
        if (!$date instanceof \DateTimeInterface) {
            return '-';
        }

        return (int) $date->format('j') . ' '
            . self::BULAN[(int) $date->format('n')] . ' '
            . $date->format('Y');
    }

    public static function qcLabel(?string $val): string
    {
        if ($val === null || $val === '') {
            return '-';
        }

        return match ($val) {
            'sangat_basah'   => 'Sangat Basah',
            'medium_basah'   => 'Medium Basah',
            'basah'          => 'Basah',
            'kering'         => 'Kering',
            'bak_berisi_air' => 'Bak Berisi Air',
            'bak_kering'     => 'Bak Kering',
            'benda_lain'     => 'Benda Lain-lain',
            default          => ucwords(str_replace('_', ' ', $val)),
        };
    }
}
