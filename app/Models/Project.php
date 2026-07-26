<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Model representing a project/work experience entry.
 *
 * The Project model stores professional project information including
 * technology stack, team details, and timeline. Each project belongs
 * to a single profile and can have associated images.
 */
#[Fillable(['profile_id', 'name', 'description', 'tech_stack', 'role', 'team_size', 'url', 'repo_url', 'start_date', 'end_date', 'is_current', 'is_featured'])]
#[Hidden([])]
class Project extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = ProjectFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'projects';

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
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_featured' => 'boolean',
            'team_size' => 'integer',
        ];
    }

    /**
     * Override the base query to apply default ordering by start_date (descending).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('start_date', 'desc');
    }

    /**
     * Define the relationship with Profile model.
     * A project belongs to a single profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Define the relationship with Image model.
     * A project can have many images through polymorphic relationship.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}