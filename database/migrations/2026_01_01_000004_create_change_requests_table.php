<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mendukung KF-05 (kelola Change Request) dan UC-06
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('submission_date');
            $table->enum('status', [
                'submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'closed',
            ])->default('submitted');
            $table->foreignId('requested_by')->nullable()->constrained('users');
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->text('follow_up_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
