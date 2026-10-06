<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilestoneOutput extends Model
{
    protected $fillable = [
        'milestone_id', 'label', 'type', 'description', 'is_required', 'order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function fulfillments(): HasMany
    {
        return $this->hasMany(MilestoneOutputFulfillment::class, 'output_id');
    }

    /**
     * Cek apakah output ini sudah dipenuhi (ada minimal 1 fulfillment).
     */
    public function isFulfilled(): bool
    {
        return $this->fulfillments()->exists();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'file'     => 'File',
            'freetext' => 'Teks Bebas',
            default    => $this->type,
        };
    }
}
