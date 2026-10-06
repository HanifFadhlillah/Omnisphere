<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menambahkan kolom milestone_id ke project_documents agar dokumen bebas
// bisa secara opsional dikaitkan ke milestone tertentu.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->foreignId('milestone_id')
                ->nullable()
                ->after('project_id')
                ->constrained('project_milestones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });
    }
};
