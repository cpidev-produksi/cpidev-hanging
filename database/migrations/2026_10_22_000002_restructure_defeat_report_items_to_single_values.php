<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Form Efficiency Defeathering kini diisi PER FARM dengan SATU nilai per parameter
 * (bukan pembacaan berulang). Total & average dihitung dari seluruh farm dalam 1 tabel.
 *
 *  - kolom JSON `samples` diganti kolom nilai tunggal
 *  - tambah `farm_id` untuk mengambil riwayat farm sebelumnya (Speed Line & Time)
 *  - data lama (jika ada) dikonversi: rata-rata pembacaan (ATP = total)
 */
return new class extends Migration
{
    /** kolom desimal nilai tunggal */
    private const DECIMALS = [
        'amp', 'volt',
        'scalder1_temp', 'scalder2_temp', 'scalder3_temp',
        'after_plucker', 'after_sortir',
        'head_pulling', 'leg_cutting', 'leg_pulling',
    ];

    public function up(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $t) {
            if (! Schema::hasColumn('defeat_report_items', 'farm_id')) {
                $t->unsignedBigInteger('farm_id')->nullable()->index();
            }
            foreach (self::DECIMALS as $col) {
                if (! Schema::hasColumn('defeat_report_items', $col)) {
                    $t->decimal($col, 8, 2)->nullable();
                }
            }
            if (! Schema::hasColumn('defeat_report_items', 'atp')) {
                $t->unsignedInteger('atp')->nullable();
            }
        });

        $this->convertExistingRows();

        if (Schema::hasColumn('defeat_report_items', 'samples')) {
            Schema::table('defeat_report_items', function (Blueprint $t) {
                $t->dropColumn('samples');
            });
        }
    }

    private function convertExistingRows(): void
    {
        $hasSamples = Schema::hasColumn('defeat_report_items', 'samples');
        $hasMcFarm  = Schema::hasColumn('monitor_controls', 'farm_id');

        DB::table('defeat_report_items')->chunkById(200, function ($rows) use ($hasSamples, $hasMcFarm) {
            $farmByMc = $hasMcFarm
                ? DB::table('monitor_controls')
                    ->whereIn('id', $rows->pluck('monitor_control_id')->filter()->all())
                    ->pluck('farm_id', 'id')
                : collect();

            foreach ($rows as $row) {
                $upd = [];

                if ($row->monitor_control_id && $farmByMc->has($row->monitor_control_id)) {
                    $upd['farm_id'] = $farmByMc[$row->monitor_control_id];
                }

                if ($hasSamples && $row->samples) {
                    $samples = json_decode($row->samples, true) ?: [];

                    foreach (array_merge(self::DECIMALS, ['atp']) as $col) {
                        $vals = array_values(array_filter(
                            (array) ($samples[$col] ?? []),
                            fn ($v) => is_numeric($v)
                        ));
                        if (! count($vals)) {
                            continue;
                        }
                        $upd[$col] = $col === 'atp'
                            ? (int) round(array_sum($vals))                 // ATP: total
                            : round(array_sum($vals) / count($vals), 2);    // lainnya: rata-rata
                    }
                }

                if ($upd) {
                    DB::table('defeat_report_items')->where('id', $row->id)->update($upd);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $t) {
            if (! Schema::hasColumn('defeat_report_items', 'samples')) {
                $t->json('samples')->nullable();
            }
        });

        Schema::table('defeat_report_items', function (Blueprint $t) {
            $t->dropColumn(array_merge(['farm_id', 'atp'], self::DECIMALS));
        });
    }
};
