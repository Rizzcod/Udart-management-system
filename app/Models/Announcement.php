<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['title', 'body', 'type', 'posted_by', 'target_role', 'is_pinned', 'expires_at'];

    protected $casts = [
        'is_pinned'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /** Scope: only active (not expired) announcements. */
    public function scopeActive(Builder $query): void
    {
        $query->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    /** Scope: visible to a user based on their role. */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $role = $user->getRoleNames()->first();
        $query->where(fn($q) => $q->whereNull('target_role')->orWhere('target_role', $role));
    }

    public function typeBadge(): array
    {
        return match($this->type) {
            'urgent'  => ['label' => 'Urgent',  'bg' => '#fef2f2', 'color' => '#dc2626', 'icon' => 'exclamation-circle-fill'],
            'warning' => ['label' => 'Warning', 'bg' => '#ffedd5', 'color' => '#ea580c', 'icon' => 'exclamation-triangle-fill'],
            default   => ['label' => 'Info',    'bg' => '#dbeafe', 'color' => '#1c3faa', 'icon' => 'info-circle-fill'],
        };
    }

    /** Check if this announcement has been read by a given user. */
    public function isReadBy(int $userId): bool
    {
        return \DB::table('announcement_reads')
            ->where('user_id', $userId)
            ->where('announcement_id', $this->id)
            ->exists();
    }

    /** Count of unread active announcements visible to the given user. */
    public static function unreadCountFor(User $user): int
    {
        $role = $user->getRoleNames()->first();

        return static::active()
            ->where(fn($q) => $q->whereNull('target_role')->orWhere('target_role', $role))
            ->whereNotIn('id', fn($q) => $q->select('announcement_id')
                ->from('announcement_reads')
                ->where('user_id', $user->id))
            ->count();
    }
}
