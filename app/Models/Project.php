<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Modelo que representa un proyecto o entrada de experiencia laboral.
 *
 * El modelo Project almacena información profesional del proyecto incluyendo
 * stack tecnológico, detalles del equipo y cronología. Cada proyecto pertenece
 * a un solo usuario y puede tener imágenes asociadas.
 */
#[Fillable(['user_id', 'name', 'description', 'tech_stack', 'role', 'team_size', 'url', 'repo_url', 'start_date', 'end_date', 'is_current', 'is_featured'])]
#[Hidden([])]
class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = ProjectFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'projects';

    /**
     * Obtiene la clave primaria del modelo.
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el ID del modelo es autoincremental.
     */
    public $incrementing = true;

    /**
     * Número de modelos a devolver por consulta.
     */
    protected $perPage = 15;

    /**
     * Obtiene los atributos que deben convertirse.
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
     * Sobrescribe la consulta base para aplicar ordenamiento por start_date (descendente).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('start_date', 'desc');
    }

    /**
     * Define la relación con el modelo User.
     * Un proyecto pertenece a un solo usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación con el modelo Image.
     * Un proyecto puede tener múltiples imágenes mediante relación polimórfica.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('screenshots');
    }
}