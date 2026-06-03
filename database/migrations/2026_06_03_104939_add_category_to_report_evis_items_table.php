<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_evis_items', function (Blueprint $table) {
            $table->enum('category', ['fresh', 'frozen'])->default('fresh')->after('product_evis_id');
        });
    }

    public function down(): void
    {
        Schema::table('report_evis_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
