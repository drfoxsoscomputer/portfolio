<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Modelo que representa una imagen que puede asociarse a múltiples entidades.
 *
 * El modelo Image utiliza relaciones polimórficas para permitir que las imágenes
 * se asocien a entidades User, Project y Education. Esto permite una gestión
 * flexible de medios en diferentes tipos de contenido del CV.
 */
#[Fillable(['url', 'alt_text', 'type', 'sort_order'])]
#[Hidden([])]
class Image extends Model
{
    use HasFactory;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = ImageFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'images';

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
     * Define la relación polimórfica para asociar la imagen a cualquier entidad.
     * El tipo imageable puede ser User, Project o Education según el morph map.
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
    }
}