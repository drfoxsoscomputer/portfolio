<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Modelo que representa las habilidades de un usuario.
 *
 * El modelo Skill almacena entradas de habilidades categorizadas para el portafolio.
 * Cada habilidad pertenece a un solo usuario y puede tener una categoría y orden.
 */
#[Fillable(['user_id', 'name', 'category', 'sort_order'])]
#[Hidden([])]
class Skill extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = SkillFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'skills';

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
     * Sobrescribe la consulta base para aplicar ordenamiento por sort_order (ascendente).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('sort_order', 'asc');
    }

    /**
     * Define la relación con el modelo User.
     * Una habilidad pertenece a un solo usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icons')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50);
            });
    }
}
