<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->text('validation_note')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn('validation_note');
        });
    }
};
