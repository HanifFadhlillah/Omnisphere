<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mendukung KF-01 (kelola data proyek) & KF-09 (status real-time)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode/no. proyek internal PMO
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('business_owner')->nullable(); // pemilik proses bisnis
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // target selesai / deadline utama
            $table->enum('status', [
                'planning', 'ongoing', 'sit', 'uat', 'completed', 'on_hold', 'cancelled',
            ])->default('planning');
            $table->unsignedTinyInteger('progress_percentage')->default(0); // KF-03
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
