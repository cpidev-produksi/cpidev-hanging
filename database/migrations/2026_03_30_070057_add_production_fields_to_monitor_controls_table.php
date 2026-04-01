<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            
            $table->string('size', 20)->nullable()->change();

            // no urut truk: reset per lokasi+tanggal
            $table->unsignedInteger('truck_no')->nullable()->after('size');

            // Extra fields produksi
            $table->string('seal_no', 50)->nullable()->after('plate_number_id');
            $table->time('truck_arrival_time')->nullable()->after('seal_no');

            $table->date('catch_date')->nullable()->after('truck_arrival_time');

            $table->unsignedInteger('total_chicken')->nullable()->after('catch_date'); // total ekor
            $table->decimal('total_kilo', 10, 2)->nullable()->after('total_chicken');
            $table->decimal('abw', 10, 2)->nullable()->after('total_kilo');

            $table->string('sppa_no', 50)->nullable()->after('abw');
            $table->string('order_id', 100)->nullable()->after('sppa_no');
            $table->date('sppa_date')->nullable()->after('order_id');

            $table->unsignedInteger('farm_fee_amount')->default(0)->change();

            $table->index(['location', 'process_date', 'truck_no'], 'mc_location_date_truckno_idx');
        });
    }

    public function down(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            $table->dropIndex('mc_location_date_truckno_idx');

            $table->dropColumn([
                'truck_no',
                'seal_no',
                'truck_arrival_time',
                'catch_date',
                'total_chicken',
                'total_kilo',
                'abw',
                'sppa_no',
                'order_id',
                'sppa_date',
            ]);

            // rollback change type (optional)
            // $table->decimal('size', 3, 1)->nullable()->change();
            // $table->integer('farm_fee_amount')->default(0)->change();
        });
    }
};
