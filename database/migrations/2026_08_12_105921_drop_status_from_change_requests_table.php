<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Status dihapus karena Catatan Siar tidak lagi menggunakan status alur kerja.
// Keterlambatan ditentukan secara otomatis dari tanggal pengajuan vs tenggat proyek.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'closed'])
                  ->default('submitted')
                  ->after('submission_date');
        });
    }
};

