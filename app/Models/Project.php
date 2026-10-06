<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'vendor_name', 'business_owner',
        'start_date', 'end_date', 'status', 'is_completed', 'progress_percentage', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'   => 'date',
            'end_date'     => 'date',
            'is_completed' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProjectProgressUpdate::class)->latest();
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    /**
     * Hitung progress proyek otomatis berdasarkan bobot durasi timeline milestone.
     *
     * Formula: sum(durasi_milestone_done) / sum(durasi_milestone_done + durasi_milestone_berjalan) * 100
     * - Hanya milestone dengan start_date & due_date yang masuk kalkulasi.
     * - Milestone tanpa rentang tanggal diabaikan.
     * - Milestone 'done'                      → masuk pembilang & penyebut (penuh).
     * - Milestone 'in_progress'/'under_review' → masuk penyebut saja (tidak pembilang).
     * - Milestone 'pending'/'needs_revision'   → tidak masuk pembilang maupun penyebut.
     * - Jika tidak ada milestone done/berjalan → progress = 0.
     */
    public function calculateProgressFromMilestones(): int
    {
        $milestones = $this->milestones()->get();

        $totalDays    = 0;
        $doneDays     = 0;

        foreach ($milestones as $milestone) {
            if (! $milestone->start_date || ! $milestone->due_date) {
                continue;
            }

            // Hanya milestone yang sudah selesai atau sedang berjalan yang dihitung
            if (! in_array($milestone->status, ['done', 'in_progress', 'under_review'])) {
                continue;
            }

            $duration = $milestone->start_date->diffInDays($milestone->due_date);
            // Milestone dengan durasi 0 diberi bobot minimal 1 hari
            $duration = max(1, $duration);

            $totalDays += $duration;

            if ($milestone->status === 'done') {
                $doneDays += $duration;
            }
        }

        if ($totalDays === 0) {
            return 0;
        }

        return (int) round($doneDays / $totalDays * 100);
    }

    // Dipakai Dashboard & Reminder Deadline (UC-02, UC-10)
    public function scopeApproachingDeadline($query, int $days = 7)
    {
        return $query->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays($days)])
            ->where('is_completed', false);
    }
}
