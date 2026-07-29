<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing skills/abilities for a user.
 *
 * The Skill model stores categorized skill entries for the user's portfolio.
 * Each skill belongs to a single user and can have a category and sort order.
 */
#[Fillable(['user_id', 'name', 'category', 'sort_order'])]
#[Hidden([])]
class Skill extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = SkillFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'skills';

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
     * Override the base query to apply default ordering by sort_order (ascending).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('sort_order', 'asc');
    }

    /**
     * Define the relationship with User model.
     * A skill belongs to a single user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
