<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            $table->foreignId('expedition_id')->nullable()->after('driver_name')->constrained('expeditions')->nullOnDelete();
            $table->foreignId('plate_number_id')->nullable()->after('expedition_id')->constrained('plate_numbers')->nullOnDelete();

            // truck_id sudah tidak dipakai jika Anda pindah ke plate numbers
            // biarkan dulu agar tidak merusak data lama, nanti bisa di-drop setelah migrasi data selesai
        });
    }

    public function down(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plate_number_id');
            $table->dropConstrainedForeignId('expedition_id');
        });
    }
};