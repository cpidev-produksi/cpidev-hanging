<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Efficiency Defeathering:
 *  - tambah "Ayam Jatuh di Scalder" (scalder_fallen)
 *  - Head Pulling / Leg Cutting / Leg Pulling: satu jumlah (*_count) diganti dua input, *_ok dan *_not_ok.
 *    Kolom persentase (head_pulling, leg_cutting, leg_pulling) tetap dipakai dan kini berarti % OK = OK / sampling_count x 100.
 *
 * Data lama: *_count dulu adalah pembilang persentase, jadi dipindah ke *_ok (persentase lama tidak berubah)
 * dan *_not_ok diisi sisanya (sampling_count - *_ok).
 */
return new class extends Migration
{
    private const QC = ['head_pulling', 'leg_cutting', 'leg_pulling'];

    public function up(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $table) {
            $table->unsignedInteger('scalder_fallen')->nullable()->after('scalder3_time');

            foreach (self::QC as $key) {
                $table->unsignedInteger($key . '_ok')->nullable()->after($key . '_count');
                $table->unsignedInteger($key . '_not_ok')->nullable()->after($key . '_ok');
            }
        });

        foreach (self::QC as $key) {
            DB::table('defeat_report_items')->update([
                $key . '_ok' => DB::raw($key . '_count'),
                $key . '_not_ok' => DB::raw(
                    "CASE WHEN sampling_count IS NULL OR {$key}_count IS NULL THEN NULL "
                    . "WHEN sampling_count >= {$key}_count THEN sampling_count - {$key}_count ELSE 0 END"
                ),
            ]);
        }

        Schema::table('defeat_report_items', function (Blueprint $table) {
            $table->dropColumn(array_map(fn ($k) => $k . '_count', self::QC));
        });
    }

    public function down(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $table) {
            foreach (self::QC as $key) {
                $table->unsignedInteger($key . '_count')->nullable()->after($key);
            }
        });

        foreach (self::QC as $key) {
            DB::table('defeat_report_items')->update([$key . '_count' => DB::raw($key . '_ok')]);
        }

        Schema::table('defeat_report_items', function (Blueprint $table) {
            $cols = ['scalder_fallen'];
            foreach (self::QC as $key) {
                $cols[] = $key . '_ok';
                $cols[] = $key . '_not_ok';
            }
            $table->dropColumn($cols);
        });
    }
};
