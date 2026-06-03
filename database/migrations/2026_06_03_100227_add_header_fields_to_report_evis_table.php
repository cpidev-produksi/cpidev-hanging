<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_evis', function (Blueprint $table) {
            $table->string('location', 10)->nullable()->after('report_date');
            $table->enum('shift', ['pagi', 'malam'])->nullable()->after('location');

            $table->unsignedInteger('truck_count')->default(0)->after('shift');
            $table->unsignedInteger('received_chicken')->default(0)->after('truck_count');

            $table->decimal('yield_percent', 5, 2)->nullable()->after('received_chicken');

            $table->decimal('fresh_total_bag', 10, 2)->default(0)->after('yield_percent');
            $table->decimal('fresh_total_kg', 10, 2)->default(0)->after('fresh_total_bag');
            $table->decimal('frozen_total_bag', 10, 2)->default(0)->after('fresh_total_kg');
            $table->decimal('frozen_total_kg', 10, 2)->default(0)->after('frozen_total_bag');
        });
    }

    public function down(): void
    {
        Schema::table('report_evis', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'shift',
                'truck_count',
                'received_chicken',
                'yield_percent',
                'fresh_total_bag',
                'fresh_total_kg',
                'frozen_total_bag',
                'frozen_total_kg'
            ]);
        });
    }
};
