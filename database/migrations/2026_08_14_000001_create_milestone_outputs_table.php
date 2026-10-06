<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menyimpan daftar output yang diharapkan dari setiap milestone.
// Admin mendefinisikan output apa saja (file/freetext) yang wajib/opsional dipenuhi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('project_milestones')->cascadeOnDelete();
            $table->string('label');                              // Nama output, misal "BAST SIT"
            $table->enum('type', ['file', 'freetext']);          // Jenis output yang diharapkan
            $table->text('description')->nullable();              // Instruksi tambahan
            $table->boolean('is_required')->default(true);        // Wajib atau opsional
            $table->unsignedSmallInteger('order')->default(0);    // Urutan tampilan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_outputs');
    }
};
