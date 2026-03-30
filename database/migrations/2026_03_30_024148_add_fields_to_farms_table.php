<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->string('address', 255)->nullable()->after('name');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('vendor_code', 50)->nullable()->index()->after('city');
            $table->unsignedTinyInteger('area_category')->nullable()->after('vendor_code');
            $table->string('distance', 100)->nullable()->after('area_category');
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'vendor_code', 'area_category', 'distance']);
        });
    }
};
