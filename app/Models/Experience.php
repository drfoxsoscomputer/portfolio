<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Model representing work experience entries.
 *
 * The Experience model stores detailed work history information including
 * company details, role, description, location, and employment duration. Each
 * experience belongs to a single user.
 */
#[Fillable(['user_id', 'company', 'role', 'description', 'location', 'start_date', 'end_date', 'is_current'])]
#[Hidden([])]
class Experience extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = ExperienceFactory::class;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    /**
     * Get the table associated with the model.
     */
    protected $table = 'experiences';

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
     * Override the base query to apply default ordering by start_date (descending).
     */
    public function newQuery(): Builder
    {
        return parent::newQuery()->orderBy('start_date', 'desc');
    }

    /**
     * Define the relationship with User model.
     * An experience belongs to a single user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50);
            });
    }
}
