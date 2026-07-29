<?php

namespace App\Models;

use Database\Factories\EducationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Model representing education entries.
 *
 * The Education model stores educational background information including
 * institution, degree, field, and employment duration. Each education belongs
 * to a single user and can have associated images.
 */
#[Fillable(['user_id', 'institution', 'degree', 'field', 'description', 'start_date', 'end_date', 'is_current', 'sort_order'])]
#[Hidden([])]
class Education extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = EducationFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'education';

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
     * An education belongs to a single user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with Image model.
     * An education can have many images through polymorphic relationship.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
