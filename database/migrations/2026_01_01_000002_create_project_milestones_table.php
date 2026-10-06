<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mendukung KF-04 (timeline & milestone) dan UC-04, UC-10 (reminder deadline)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->enum('status', ['pending', 'in_progress', 'done', 'delayed'])->default('pending');
            $table->timestamps();

            $table->index('due_date'); // dipakai query reminder deadline (UC-10)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
