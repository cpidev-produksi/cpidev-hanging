<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shfi_roots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('shfi_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('root_id')->constrained('shfi_roots')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('shfi_folders')->nullOnDelete();
            $table->string('name');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['root_id', 'parent_id']);
        });

        Schema::create('shfi_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('root_id')->constrained('shfi_roots')->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('shfi_folders')->nullOnDelete();

            $table->string('name');
            $table->string('disk', 50)->default('public');
            $table->string('disk_path');   // path relative pada disk
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['root_id', 'folder_id']);
            $table->index(['uploaded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shfi_files');
        Schema::dropIfExists('shfi_folders');
        Schema::dropIfExists('shfi_roots');
    }
};
