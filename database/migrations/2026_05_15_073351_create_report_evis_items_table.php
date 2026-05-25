<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('report_evis_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_evis_id');
            $table->unsignedBigInteger('product_evis_id');
            
            // 10 kolom nomor (1-10) untuk Bag & Kg
            // Frozen & Fresh dipisah berdasarkan product
            for ($i = 1; $i <= 10; $i++) {
                $table->decimal("bag_$i", 10, 2)->default(0);
                $table->decimal("kg_$i", 10, 2)->default(0);
            }
            
            $table->decimal('total_bag', 10, 2)->default(0);
            $table->decimal('total_kg', 10, 2)->default(0);
            
            $table->timestamps();
            
            $table->foreign('report_evis_id')->references('id')->on('report_evis')->cascadeOnDelete();
            $table->foreign('product_evis_id')->references('id')->on('product_evis');
        });
    }

    public function down(): void {
        Schema::dropIfExists('report_evis_items');
    }
};
