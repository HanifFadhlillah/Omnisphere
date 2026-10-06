<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom is_completed ke tabel projects.
     * Digunakan untuk menandai proyek yang sudah selesai dan boleh memiliki Change Request,
     * terlepas dari jenis proyeknya (FS, QA, Implementasi, dll).
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_completed');
        });
    }
};
