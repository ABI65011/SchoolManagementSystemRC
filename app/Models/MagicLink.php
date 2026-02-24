<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagicLink extends Model
{
    protected $fillable = [
        'staff_id',
        'token',
        'type',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
    public function isValid(): bool
    {
        return !$this->used && !$this->hasExpired();
    }

    /**
     * Mark the magic link as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Scope to get only valid (unused and unexpired) links.
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to get expired links.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to get used links.
     */
    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }
}
