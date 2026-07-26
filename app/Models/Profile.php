<?php

namespace App\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Model representing a user's portfolio/profile information.
 *
 * The Profile model is a singleton pattern, representing the core
 * portfolio information for a user. It contains personal details,
 * professional information, and serves as the root entity for all
 * other related entities (links, projects, experiences, skills,
 * education, and images).
 */
#[Fillable(['name', 'title', 'location', 'phone', 'email', 'summary', 'avatar'])]
#[Hidden([])]
class Profile extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = ProfileFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'profiles';

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
            'name' => 'string',
            'title' => 'string',
            'location' => 'string',
            'phone' => 'string',
            'email' => 'string',
            'summary' => 'string',
            'avatar' => 'string',
        ];
    }

    /**
     * Define the relationship with Link models.
     * A profile has many links.
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    /**
     * Define the relationship with Project models.
     * A profile has many projects.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Define the relationship with Experience models.
     * A profile has many experiences.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * Define the relationship with Skill models.
     * A profile has many skills.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * Define the relationship with Education models.
     * A profile has many educations.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Define the relationship with Language models.
     * A profile has many languages.
     */
    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }

    /**
     * Define the relationship with Image models.
     * A profile can have many images through polymorphic relationship.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}