<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menyimpan nama jenis dokumen custom saat kategori yang dipilih adalah "Lainnya" (other)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->string('custom_category')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn('custom_category');
        });
    }
};
