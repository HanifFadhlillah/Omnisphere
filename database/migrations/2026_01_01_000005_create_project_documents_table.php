<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mendukung KF-06 (repository dokumen), KF-07 (dokumen SIT/UAT),
// KF-12/KF-13 (upload/download), UC-07, UC-08, UC-09
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Kategori termasuk SIT & UAT sesuai KF-07, plus dokumen administrasi umum
            $table->enum('category', [
                'contract', 'change_request', 'berita_acara', 'bast', 'sit', 'uat', 'progress_report', 'other',
            ])->default('other');
            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedBigInteger('file_size'); // bytes
            $table->string('mime_type')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
