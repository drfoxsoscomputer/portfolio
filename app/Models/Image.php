<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model representing an image that can be attached to multiple entity types.
 *
 * The Image model uses polymorphic relationships to allow images to be attached
 * to User, Project, and Education entities. This enables flexible media
 * management across different CV content types (avatars, screenshots, logos, certificates).
 */
#[Fillable(['url', 'alt_text', 'type', 'sort_order'])]
#[Hidden([])]
class Image extends Model
{
    use HasFactory;

    /**
     * Get the factory class for the model.
     */
    protected string $factory = ImageFactory::class;

    /**
     * Get the table associated with the model.
     */
    protected $table = 'images';

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
     * Define the polymorphic relationship to attach the image to any entity.
     * The imageable type can be User, Project, or Education based on the morph map.
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
    }
}