<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom milestone_id dari project_documents.
     * Kolom ini sudah tidak digunakan karena form "Tautkan ke Milestone"
     * telah dihapus dari UI dan tidak ada kode yang membacanya.
     */
    public function up(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });
    }

    /**
     * Kembalikan kolom milestone_id jika perlu rollback.
     */
    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->foreignId('milestone_id')->nullable()->after('project_id')
                  ->constrained('project_milestones')->nullOnDelete();
        });
    }
};
