<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master nama produk (dipakai di Verifikasi Sensitivitas)
        Schema::create('cutup_products', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150)->unique();
            $t->timestamps();
        });

        // Header laporan (1 laporan = tanggal + lokasi + shift)
        Schema::create('cutup_md_reports', function (Blueprint $t) {
            $t->id();
            $t->date('report_date');
            $t->string('location', 4);            // SH01 | SH02
            $t->string('shift', 10);              // Pagi | Malam
            $t->time('start_time')->nullable();   // Jam Mulai
            $t->time('break_time')->nullable();   // Jam Istirahat
            $t->time('end_time')->nullable();     // Jam Pulang

            // Diameter spesimen (mm) - dipakai di pemeriksaan & verifikasi
            $t->decimal('fe_diameter', 5, 2)->nullable();
            $t->decimal('nonfe_diameter', 5, 2)->nullable();
            $t->decimal('sus316_diameter', 5, 2)->nullable();

            // Pengesahan (boleh kosong)
            $t->string('made_by', 100)->nullable();      // Dibuat oleh (Operator MD)
            $t->string('approved_by', 100)->nullable();  // Disetujui oleh (SPV/Foreman/lady Produksi)
            $t->string('known_by', 100)->nullable();     // Diketahui oleh (Foreman/lady QC)

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->unique(['report_date', 'location', 'shift'], 'cutup_md_reports_unique');
            $t->index('report_date');
        });

        // Tabel Pemeriksaan Sensitivitas
        Schema::create('cutup_md_checks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('cutup_md_reports')->cascadeOnDelete();
            $t->string('metal_detector_no', 2);   // 01 | 02 | 03
            $t->time('check_time');               // Pukul
            $t->string('fe_result', 6);           // OK | NOT OK
            $t->string('nonfe_result', 6);
            $t->string('sus316_result', 6);
            $t->string('conclusion', 12);         // USABLE | NOT_USABLE
            $t->string('remark', 255)->nullable();
            $t->timestamps();

            $t->index(['report_id', 'check_time']);
        });

        // Tabel Verifikasi Sensitivitas
        Schema::create('cutup_md_verifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('cutup_md_reports')->cascadeOnDelete();
            $t->string('metal_detector_no', 2)->nullable();
            $t->time('check_time');               // Pukul
            $t->foreignId('product_id')->nullable()->constrained('cutup_products')->nullOnDelete();
            $t->string('product_name', 150);      // snapshot nama produk

            // D = Depan, T = Tengah, B = Belakang
            foreach (['fe', 'nonfe', 'sus316'] as $sp) {
                foreach (['d', 't', 'b'] as $pos) {
                    $t->string("{$sp}_{$pos}", 6);  // OK | NOT OK
                }
            }

            $t->string('product_code', 10);       // kode produk (10 karakter)
            $t->timestamps();

            $t->index(['report_id', 'check_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutup_md_verifications');
        Schema::dropIfExists('cutup_md_checks');
        Schema::dropIfExists('cutup_md_reports');
        Schema::dropIfExists('cutup_products');
    }
};
