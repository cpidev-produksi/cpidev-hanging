<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_yields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_yield_upload_id')
                ->constrained('daily_yield_uploads')
                ->cascadeOnDelete();
 
            // Nama plant disimpan sebagai string (tidak ada master tabel plants terpisah)
            $table->string('plant');
 
            // HO
            $table->decimal('yield_titik_0', 9, 4)->nullable(); // Yield Titik 0
            $table->decimal('ach_yield_h0', 9, 4)->nullable();  // Ach. Yield H0
 
            // H1 (GRILLER)
            $table->decimal('yield_h1', 9, 4)->nullable();      // Yield H1 thd Titik 0
            $table->decimal('ach_yield_h1', 9, 4)->nullable();  // Ach. Yield H1
 
            // H2 (PARTING)
            $table->decimal('yield_h2', 9, 4)->nullable();      // Yield H2 thd Titik 0
            $table->decimal('ach_yield_h2', 9, 4)->nullable();  // Ach. Yield H2
 
            // H3 (CUT-UP)
            $table->decimal('yield_h3', 9, 4)->nullable();      // Yield H3 thd Titik 0
            $table->decimal('ach_yield_h3', 9, 4)->nullable();  // Ach. Yield H3
 
            // H4 (BONELESS)
            $table->decimal('yield_h4', 9, 4)->nullable();      // Yield H4 thd Titik 0
            $table->decimal('ach_yield_h4', 9, 4)->nullable();  // Ach. Yield H4
 
            $table->decimal('yield_fg', 9, 4)->nullable();      // YIELD FG
            $table->decimal('total_fg_bp', 9, 4)->nullable();   // TOTAL FG + BP
            $table->decimal('sumpo', 9, 4)->nullable();         // SUMPO (RM+BP-SVUV)
            $table->decimal('lost', 9, 4)->nullable();          // LOST
            $table->date('tanggal_update_terakhir')->nullable();
 
            $table->timestamps();
 
            $table->index('plant');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('daily_yields');
    }
};