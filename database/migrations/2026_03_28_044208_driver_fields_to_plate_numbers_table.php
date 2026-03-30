<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plate_numbers', function (Blueprint $table) {
            $table->string('driver_name', 150)->nullable()->after('plate_number');
            $table->string('driver_phone', 30)->nullable()->after('driver_name');
        });
    }

    public function down(): void
    {
        Schema::table('plate_numbers', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_phone']);
        });
    }
};
