<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menambahkan start_date (tanggal mulai) ke milestone agar timeline punya rentang awal–akhir.
// Kolom nullable agar data milestone lama tidak rusak.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
};
