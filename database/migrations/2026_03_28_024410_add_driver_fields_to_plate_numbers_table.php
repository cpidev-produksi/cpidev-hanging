<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            // aman jika FK ada
            if (Schema::hasColumn('monitor_controls', 'truck_id')) {
                $table->dropConstrainedForeignId('truck_id');
            }

            if (Schema::hasColumn('monitor_controls', 'driver_name')) {
                $table->dropColumn('driver_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            // NOTE: restore truck_id but WITHOUT trucks table it will fail.
            // Kalau kamu benar-benar hapus trucks table, sebaiknya down() dibiarkan minimal.
            // Jika tetap mau, uncomment di bawah dan pastikan trucks table ada.
            //
            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->string('driver_name', 50);
        });
    }
};
