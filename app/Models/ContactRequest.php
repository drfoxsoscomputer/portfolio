<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representing a contact message sent from the public portfolio.
 *
 * The ContactRequest model stores the messages visitors send through the
 * public contact form so the admin can review them later.
 */
#[Fillable(['name', 'email', 'message', 'read_at'])]
class ContactRequest extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Mark the contact request as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Scope the query to only include unread contact requests.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
