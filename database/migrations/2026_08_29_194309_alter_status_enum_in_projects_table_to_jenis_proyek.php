<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengubah kolom status proyek ke 6 jenis baru (Jenis Proyek).
     * Data lama di-reset (fresh start sesuai permintaan).
     */
    public function up(): void
    {
        // Hapus semua data proyek lama (fresh start)
        // Hapus juga data terkait via FK cascade (milestones, documents, dll)
        DB::table('project_progress_updates')->delete();
        DB::table('milestone_output_fulfillments')->delete();
        DB::table('milestone_outputs')->delete();
        DB::table('project_milestones')->delete();
        DB::table('project_documents')->delete();
        DB::table('change_requests')->delete();
        DB::table('projects')->delete();

        // Ubah enum status ke 6 jenis baru
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM(
            'feasibility_study',
            'perencanaan',
            'quality_assurance',
            'implementasi',
            'manage_operation',
            'change_request'
        ) NOT NULL DEFAULT 'implementasi'");
    }

    /**
     * Kembalikan ke enum status lama jika rollback.
     */
    public function down(): void
    {
        DB::table('projects')->delete();

        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM(
            'planning',
            'ongoing',
            'sit',
            'uat',
            'completed',
            'on_hold',
            'cancelled'
        ) NOT NULL DEFAULT 'planning'");
    }
};
