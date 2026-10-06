<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum to include 'under_review' and 'needs_revision'
        DB::statement("ALTER TABLE project_milestones MODIFY COLUMN status ENUM('pending', 'in_progress', 'under_review', 'needs_revision', 'done', 'delayed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For down, we might lose data if it's currently 'under_review', so we just fall back to the old ENUM safely by changing current new statuses to 'in_progress' first.
        DB::statement("UPDATE project_milestones SET status = 'in_progress' WHERE status IN ('under_review', 'needs_revision')");
        DB::statement("ALTER TABLE project_milestones MODIFY COLUMN status ENUM('pending', 'in_progress', 'done', 'delayed') DEFAULT 'pending'");
    }
};
