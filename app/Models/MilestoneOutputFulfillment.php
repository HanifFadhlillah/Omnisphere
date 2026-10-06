<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneOutputFulfillment extends Model
{
    protected $fillable = [
        'output_id', 'project_id', 'type',
        'freetext_content',
        'file_path', 'original_filename', 'file_size', 'mime_type',
        'submitted_by',
    ];

    public function output(): BelongsTo
    {
        return $this->belongsTo(MilestoneOutput::class, 'output_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function humanFileSize(): string
    {
        if (! $this->file_size) {
            return '—';
        }
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
