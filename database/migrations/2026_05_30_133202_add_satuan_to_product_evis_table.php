<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_evis', function (Blueprint $table) {
            $table->decimal('satuan', 10, 2)->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('product_evis', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
