<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id', 'title', 'description', 'start_date', 'due_date', 'status', 'completed_at', 'validation_note',
    ];

    protected function casts(): array
    {
        return [
            'start_date'   => 'date',
            'due_date'     => 'date',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (ProjectMilestone $milestone) {
            $milestone->recalculateProjectProgress();
        });

        static::deleted(function (ProjectMilestone $milestone) {
            $milestone->recalculateProjectProgress();
        });
    }

    public function recalculateProjectProgress(): void
    {
        if ($this->project) {
            $this->project->update([
                'progress_percentage' => $this->project->calculateProgressFromMilestones()
            ]);
        }
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(MilestoneOutput::class, 'milestone_id')->orderBy('order');
    }

    /**
     * Cek apakah semua output sudah terpenuhi (untuk auto-done).
     *
     * Aturan:
     *  1. Jika ada output required → semua output required harus terpenuhi.
     *  2. Jika tidak ada output required tapi ada output opsional → semua output opsional harus terpenuhi.
     *  3. Jika tidak ada output sama sekali → tidak auto-done.
     */
    public function allRequiredOutputsFulfilled(): bool
    {
        $allOutputs      = $this->outputs()->get();
        $requiredOutputs = $allOutputs->where('is_required', true);

        // Tidak ada output sama sekali → tidak auto-done
        if ($allOutputs->isEmpty()) {
            return false;
        }

        // Ada output required → cek semua yang required
        if ($requiredOutputs->isNotEmpty()) {
            foreach ($requiredOutputs as $output) {
                if (! $output->isFulfilled()) {
                    return false;
                }
            }
            return true;
        }

        // Tidak ada required → cek semua output opsional
        foreach ($allOutputs as $output) {
            if (! $output->isFulfilled()) {
                return false;
            }
        }

        return true;
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'done' && $this->due_date->isPast();
    }

    /**
     * Mengembalikan status ketepatan waktu penyelesaian milestone.
     * - 'early'   : diselesaikan sebelum tenggat
     * - 'on_time' : diselesaikan tepat pada hari tenggat
     * - 'late'    : diselesaikan setelah tenggat
     * - null      : milestone belum selesai
     */
    public function completionStatus(): ?string
    {
        if ($this->status !== 'done' || ! $this->completed_at) {
            return null;
        }

        $completedDate = $this->completed_at->startOfDay();
        $dueDate       = $this->due_date->copy()->startOfDay();

        if ($completedDate->lt($dueDate)) {
            return 'early';
        } elseif ($completedDate->eq($dueDate)) {
            return 'on_time';
        } else {
            return 'late';
        }
    }
}
