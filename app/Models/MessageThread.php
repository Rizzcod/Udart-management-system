<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $category
 * @property string $subject
 */
class MessageThread extends Model
{
    protected $fillable = ['subject', 'category', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'thread_id')->latestOfMany();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_thread_participants', 'thread_id', 'user_id')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function categoryBadge(): array
    {
        return match ($this->category) {
            'protocol'    => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'label' => 'Protocol'],
            'urgent'      => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => 'Urgent'],
            'maintenance' => ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => 'Maintenance'],
            default       => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => 'General'],
        };
    }
}
