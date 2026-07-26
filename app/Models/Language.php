<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing language proficiency for a profile.
 *
 * The Language model stores language skills for the user's portfolio.
 * Each language belongs to a single profile and can have a proficiency level
 * and sort order.
 */
#[Fillable(['profile_id', 'name', 'level', 'sort_order'])]
#[Hidden([])]
class Language extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = LanguageFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'languages';

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
     * Define the relationship with Profile model.
     * A language belongs to a single profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
