<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing a link/profile association.
 *
 * The Link model stores hyperlinks with metadata like label, URL, and ordering
 * for the user's portfolio profile. Each link belongs to a single profile.
 */
#[Fillable(['profile_id', 'label', 'url', 'icon', 'sort_order'])]
#[Hidden([])]
class Link extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = LinkFactory::class;

    protected static function booted(): void
    {
        static::creating(function (Link $link) {
            if (! $link->profile_id) {
                $link->profile_id = Profile::first()?->id;
            }
        });
    }

    /**
     * Get the table associated with the model.
     */
    protected $table = 'links';

    /**
     * Get the primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The number of models to return for a single query.
     */
    protected $perPage = 15;

    /**
     * Get the default ordering for the model.
     */
    protected array $order = ['sort_order' => 'asc'];

    /**
     * Override the base query to apply default ordering.
     */
    function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('sort_order', 'asc');
    }

    /**
     * Define the relationship with Profile model.
     * A link belongs to a single profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}