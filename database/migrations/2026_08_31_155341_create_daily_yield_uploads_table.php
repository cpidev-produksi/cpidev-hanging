<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_yield_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');   // 1-12
            $table->unsignedSmallInteger('tahun');  // ex: 2026
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
 
            // Flag versi terbaru untuk kombinasi bulan+tahun tsb.
            // Setiap upload ulang periode yang sama TIDAK menimpa data lama,
            // tapi disimpan sebagai batch baru dan is_latest lama diset false.
            $table->boolean('is_latest')->default(true);
 
            $table->timestamps();
 
            $table->index(['bulan', 'tahun']);
            $table->index(['bulan', 'tahun', 'is_latest']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('daily_yield_uploads');
    }
};
