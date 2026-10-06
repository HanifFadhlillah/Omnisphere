<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $fillable = [
        'project_id', 'name', 'category', 'custom_category', 'file_path', 'original_filename',
        'file_size', 'mime_type', 'notes', 'uploaded_by',
    ];

    public function categoryLabel(): string
    {
        if ($this->category === 'other' && $this->custom_category) {
            return $this->custom_category;
        }

        return match ($this->category) {
            'contract' => 'Kontrak',
            'change_request' => 'Change Request',
            'berita_acara' => 'Berita Acara',
            'bast' => 'BAST',
            'sit' => 'Dokumen SIT',
            'uat' => 'Dokumen UAT',
            'progress_report' => 'Laporan Progress',
            default => 'Lainnya',
        };
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function humanFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
