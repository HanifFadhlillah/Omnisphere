<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menyimpan pemenuhan (fulfillment) dari setiap output milestone.
// Admin/Staff PMO mengisi output: bisa berupa file upload atau teks bebas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_output_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->constrained('milestone_outputs')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete(); // shortcut untuk query
            $table->enum('type', ['file', 'freetext']);

            // Untuk tipe freetext
            $table->text('freetext_content')->nullable();

            // Untuk tipe file
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();

            $table->foreignId('submitted_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_output_fulfillments');
    }
};
