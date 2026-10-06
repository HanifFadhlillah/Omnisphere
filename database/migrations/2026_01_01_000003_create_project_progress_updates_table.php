<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mendukung KF-03 (kelola progres proyek) dan UC-05 (Monitoring Progres Proyek)
// Dicatat sebagai histori, bukan hanya satu angka, supaya progres proyek bisa dilihat perkembangannya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users');
            $table->unsignedTinyInteger('percentage');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_progress_updates');
    }
};
